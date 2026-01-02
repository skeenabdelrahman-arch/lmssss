<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\AssignmentQuestion;
use App\Models\AssignmentQuestionOption;
use App\Models\Month;
use App\Models\Lecture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AssignmentController extends Controller
{
    public function index()
    {
        $assignments = Assignment::with(['month', 'lecture'])
            ->orderBy('display_order')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('back.assignments.index', compact('assignments'));
    }

    public function create()
    {
        $months = Month::orderBy('display_order')->get();
        
        return view('back.assignments.create', compact('months'));
    }

    public function store(Request $request)
    {
        // Basic validation first
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'month_id' => 'required|exists:months,id',
            'lecture_id' => 'nullable|exists:lectures,id',
            'total_marks' => 'required|integer|min:1|max:100',
            'deadline' => 'nullable|date',
            'display_order' => 'nullable|integer',
            'file_path' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,zip|max:20480',
            'questions' => 'nullable|array',
            'questions.*.type' => 'required|string|in:mcq_single,mcq_multi,essay',
            'questions.*.question_text' => 'required|string',
            'questions.*.max_marks' => 'required|numeric|min:0',
            'questions.*.is_required' => 'nullable|boolean',
            'questions.*.auto_grade' => 'nullable|boolean',
            'questions.*.allow_text' => 'nullable|boolean',
            'questions.*.allow_file' => 'nullable|boolean',
            'questions.*.display_order' => 'nullable|integer',
        ];

        // Add options validation only for MCQ questions
        if ($request->has('questions')) {
            foreach ($request->input('questions', []) as $index => $question) {
                if (isset($question['type']) && in_array($question['type'], ['mcq_single', 'mcq_multi'])) {
                    $rules["questions.{$index}.options"] = 'required|array|min:2';
                    $rules["questions.{$index}.options.*.option_text"] = 'required|string';
                    $rules["questions.{$index}.options.*.is_correct"] = 'nullable|boolean';
                }
            }
        }

        $validated = $request->validate($rules);

        $filePath = null;
        if ($request->hasFile('file_path')) {
            $filePath = $request->file('file_path')->store('assignments', 'public');
        }

        $assignment = Assignment::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'month_id' => $validated['month_id'],
            'lecture_id' => $validated['lecture_id'] ?? null,
            'file_path' => $filePath,
            'total_marks' => $validated['total_marks'],
            'deadline' => $validated['deadline'] ?? null,
            'status' => $request->has('status') ? 'active' : 'hidden',
            'display_order' => $validated['display_order'] ?? 0,
            'auto_grade_all' => $request->has('auto_grade_all'),
        ]);

        $this->syncQuestions($assignment, $request->input('questions', []));

        return redirect()->route('assignments.index')
            ->with('success', 'تم إضافة الواجب بنجاح');
    }

    public function show($id)
    {
        $assignment = Assignment::with(['month', 'lecture', 'submissions.student'])->findOrFail($id);
        
        return view('back.assignments.show', compact('assignment'));
    }

    public function edit($id)
    {
        $assignment = Assignment::findOrFail($id);
        $months = Month::orderBy('display_order')->get();
        
        return view('back.assignments.edit', compact('assignment', 'months'));
    }

    public function update(Request $request, $id)
    {
        $assignment = Assignment::findOrFail($id);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'month_id' => 'required|exists:months,id',
            'lecture_id' => 'nullable|exists:lectures,id',
            'total_marks' => 'required|integer|min:1|max:100',
            'deadline' => 'nullable|date',
            'display_order' => 'nullable|integer',
            'file_path' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,zip|max:20480',
            'questions' => 'nullable|array',
            'questions.*.type' => 'required|string|in:mcq_single,mcq_multi,essay',
            'questions.*.question_text' => 'required|string',
            'questions.*.max_marks' => 'required|numeric|min:0',
            'questions.*.is_required' => 'nullable|boolean',
            'questions.*.auto_grade' => 'nullable|boolean',
            'questions.*.allow_text' => 'nullable|boolean',
            'questions.*.allow_file' => 'nullable|boolean',
            'questions.*.display_order' => 'nullable|integer',
            'questions.*.options' => 'nullable|array',
            'questions.*.options.*.option_text' => 'required|string',
            'questions.*.options.*.is_correct' => 'nullable|boolean',
        ]);

        if ($request->hasFile('file_path')) {
            // Delete old file
            if ($assignment->file_path) {
                Storage::disk('public')->delete($assignment->file_path);
            }
            $validated['file_path'] = $request->file('file_path')->store('assignments', 'public');
        }

        $validated['status'] = $request->has('status') ? 'active' : 'hidden';
        
        $assignment->update($validated);

        $this->syncQuestions($assignment, $request->input('questions', []));

        return redirect()->route('assignments.index')
            ->with('success', 'تم تحديث الواجب بنجاح');
    }

    public function destroy($id)
    {
        $assignment = Assignment::findOrFail($id);
        
        // Delete file
        if ($assignment->file_path) {
            Storage::disk('public')->delete($assignment->file_path);
        }
        
        $assignment->delete();

        return redirect()->route('assignments.index')
            ->with('success', 'تم حذف الواجب بنجاح');
    }

    public function submissions($id)
    {
        $assignment = Assignment::with(['month', 'lecture'])->findOrFail($id);
        $submissions = AssignmentSubmission::where('assignment_id', $id)
            ->with('student')
            ->orderBy('submitted_at', 'desc')
            ->get();
        
        return view('back.assignments.submissions', compact('assignment', 'submissions'));
    }

    public function grade(Request $request, $id, $submission)
    {
        $validated = $request->validate([
            'marks' => 'required|numeric|min:0',
            'feedback' => 'nullable|string',
        ]);

        $assignment = Assignment::findOrFail($id);
        $submissionRecord = AssignmentSubmission::findOrFail($submission);

        if ($validated['marks'] > $assignment->total_marks) {
            return redirect()->back()
                ->with('error', 'الدرجة لا يمكن أن تكون أكبر من الدرجة الكلية للواجب');
        }

        $submissionRecord->update([
            'marks' => $validated['marks'],
            'feedback' => $validated['feedback'],
            'graded_at' => now(),
            'status' => 'graded',
        ]);

        return redirect()->back()
            ->with('success', 'تم تصحيح الواجب بنجاح');
    }

    public function downloadSubmission($submissionId)
    {
        $submission = AssignmentSubmission::findOrFail($submissionId);

        if (!$submission->file_path) {
            return redirect()->back()->with('error', 'الملف غير موجود أو تم حذفه');
        }

        $files = json_decode($submission->file_path, true) ?: [$submission->file_path];
        
        // If single file, download directly
        if (count($files) === 1 && Storage::disk('public')->exists($files[0])) {
            return Storage::disk('public')->download($files[0], basename($files[0]));
        }
        
        // Multiple files - create zip
        $zipName = 'submission_' . $submission->id . '_' . time() . '.zip';
        $zipPath = storage_path('app/public/temp/' . $zipName);
        
        if (!is_dir(dirname($zipPath))) {
            mkdir(dirname($zipPath), 0775, true);
        }
        
        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE) === true) {
            foreach ($files as $index => $file) {
                if (Storage::disk('public')->exists($file)) {
                    $zip->addFile(storage_path('app/public/' . $file), basename($file));
                }
            }
            $zip->close();
            
            return response()->download($zipPath, $zipName)->deleteFileAfterSend(true);
        }
        
        return redirect()->back()->with('error', 'حدث خطأ أثناء إنشاء ملف التحميل');
    }

    public function previewSubmission($submissionId)
    {
        $submission = AssignmentSubmission::findOrFail($submissionId);

        if (!$submission->file_path) {
            return redirect()->back()->with('error', 'الملف غير موجود أو تم حذفه');
        }

        // Parse files array
        $files = json_decode($submission->file_path, true) ?: [$submission->file_path];
        
        return view('back.assignments.preview', compact('submission', 'files'));
    }

    private function syncQuestions(Assignment $assignment, array $questions): void
    {
        // Remove existing to simplify sync (cascade deletes options/answers)
        $assignment->questions()->delete();

        foreach ($questions as $qIndex => $questionData) {
            if (!is_array($questionData) || empty($questionData['type']) || empty($questionData['question_text'])) {
                continue;
            }

            $question = $assignment->questions()->create([
                'type' => $questionData['type'],
                'question_text' => $questionData['question_text'],
                'max_marks' => $questionData['max_marks'] ?? 0,
                'is_required' => !empty($questionData['is_required']),
                'auto_grade' => !empty($questionData['auto_grade']),
                'allow_text' => !empty($questionData['allow_text']),
                'allow_file' => !empty($questionData['allow_file']),
                'display_order' => $questionData['display_order'] ?? $qIndex,
            ]);

            if (in_array($question->type, ['mcq_single', 'mcq_multi'])) {
                $options = $questionData['options'] ?? [];
                foreach ($options as $optIndex => $opt) {
                    if (!is_array($opt) || empty($opt['option_text'])) {
                        continue;
                    }
                    $question->options()->create([
                        'option_text' => $opt['option_text'],
                        'is_correct' => !empty($opt['is_correct']),
                        'display_order' => $opt['display_order'] ?? $optIndex,
                    ]);
                }
            }
        }
    }
}
