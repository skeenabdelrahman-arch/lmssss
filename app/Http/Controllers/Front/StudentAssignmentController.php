<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\AssignmentQuestionAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class StudentAssignmentController extends Controller
{
    public function index($month_id)
    {
        $student = Auth::guard('student')->user();
        
        // Check if student has access to this month
        if (!$student || !$student->canAccessMonth((int) $month_id)) {
            return redirect()->route('courses.index')
                ->with('error', 'ليس لديك صلاحية للوصول إلى هذا الشهر. يرجى التأكد من تفعيل الاشتراك أو تسجيل الدخول.');
        }

        $assignments = Assignment::where('month_id', $month_id)
            ->where('status', 'active')
            ->orderBy('display_order')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('front.assignments.index', compact('assignments', 'month_id'));
    }

    public function show(Assignment $assignment)
    {
        $student = Auth::guard('student')->user();
        
        // Check access
        if (!$student || !$student->canAccessMonth($assignment->month_id)) {
            return redirect()->route('courses.index')
                ->with('error', 'ليس لديك صلاحية للوصول إلى هذا الواجب. يرجى التأكد من تفعيل الاشتراك أو تسجيل الدخول.');
        }

        $assignment->load(['questions.options']);

        // Get student's submission if exists
        $submission = $assignment->getSubmissionForStudent($student->id);

        return view('front.assignments.show', compact('assignment', 'submission'));
    }

    public function submit(Request $request, Assignment $assignment)
    {
        $student = Auth::guard('student')->user();
        
        // Check access
        if (!$student || !$student->canAccessMonth($assignment->month_id)) {
            $message = 'ليس لديك صلاحية لإرسال هذا الواجب. يرجى التأكد من تفعيل الاشتراك أو تسجيل الدخول.';

            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 403);
            }

            return redirect()->route('courses.index')->with('error', $message);
        }

        // Check if already submitted
        $existingSubmission = $assignment->getSubmissionForStudent($student->id);
        if ($existingSubmission) {
            $message = 'لقد قمت بإرسال هذا الواجب من قبل';

            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 409);
            }

            return redirect()->back()->with('error', $message);
        }

        $assignment->load(['questions.options']);

        $hasQuestions = $assignment->questions && $assignment->questions->count() > 0;

        $rules = [
            'notes' => 'nullable|string',
            'file_path' => $hasQuestions ? 'nullable' : 'required',
            'file_path.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:20480',
        ];

        foreach ($assignment->questions as $question) {
            $baseKey = 'question_answers.' . $question->id;

            if (in_array($question->type, ['mcq_single', 'mcq_multi'])) {
                $rules[$baseKey . '.selected_options'] = ($question->is_required ? 'required' : 'nullable') . '|array';
                if ($question->type === 'mcq_single') {
                    $rules[$baseKey . '.selected_options'] .= '|max:1';
                } else {
                    $rules[$baseKey . '.selected_options'] .= ($question->is_required ? '|min:1' : '');
                }
            }

            if ($question->type === 'essay') {
                // For essay questions, at least one input (text or file) is required if question is required
                if ($question->is_required) {
                    if ($question->allow_text && !$question->allow_file) {
                        // Only text allowed and required
                        $rules[$baseKey . '.answer_text'] = 'required|string';
                    } elseif (!$question->allow_text && $question->allow_file) {
                        // Only file allowed and required
                        $rules[$baseKey . '.attachment'] = 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:20480';
                    } else {
                        // Both allowed, require at least one
                        $rules[$baseKey . '.answer_text'] = 'required_without:' . $baseKey . '.attachment|nullable|string';
                        $rules[$baseKey . '.attachment'] = 'required_without:' . $baseKey . '.answer_text|nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:20480';
                    }
                } else {
                    // Optional question
                    $rules[$baseKey . '.answer_text'] = 'nullable|string';
                    $rules[$baseKey . '.attachment'] = 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:20480';
                }
            }
        }

        $validated = $request->validate($rules);

        // Handle single or multiple files - save as array
        $filePath = null;
        if ($request->hasFile('file_path')) {
            $uploadedFiles = $request->file('file_path');
            $filePaths = [];
            
            if (is_array($uploadedFiles)) {
                foreach ($uploadedFiles as $file) {
                    $filePaths[] = $file->store('assignment_submissions', 'public');
                }
            } else {
                $filePaths[] = $uploadedFiles->store('assignment_submissions', 'public');
            }
            
            // Save as JSON
            $filePath = json_encode($filePaths);
        }

        $status = 'pending';
        if ($assignment->deadline && now()->isAfter($assignment->deadline)) {
            $status = 'late';
        }

        $submission = AssignmentSubmission::create([
            'assignment_id' => $assignment->id,
            'student_id' => $student->id,
            'notes' => $validated['notes'] ?? null,
            'file_path' => $filePath,
            'submitted_at' => now(),
            'status' => $status,
        ]);

        // Store question answers and auto-grade MCQ
        $totalAutoMarks = 0;
        foreach ($assignment->questions as $question) {
            $baseKey = 'question_answers.' . $question->id;
            $answerData = data_get($validated, $baseKey, []);

            $selectedOptions = (array) ($answerData['selected_options'] ?? []);
            $answerText = $answerData['answer_text'] ?? null;
            $attachmentPath = null;
            $awarded = null;
            $statusAnswer = 'pending';

            if ($question->allow_file && $request->hasFile($baseKey . '.attachment')) {
                $attachmentPath = $request->file($baseKey . '.attachment')
                    ->store('assignment_answers', 'public');
            }

            if (in_array($question->type, ['mcq_single', 'mcq_multi'])) {
                [$awarded, $statusAnswer] = $this->autoGradeMcq($question, $selectedOptions);
                $totalAutoMarks += $awarded;
            }

            AssignmentQuestionAnswer::create([
                'submission_id' => $submission->id,
                'question_id' => $question->id,
                'answer_text' => $answerText,
                'selected_options' => $selectedOptions,
                'attachment_path' => $attachmentPath,
                'awarded_marks' => $awarded,
                'status' => $statusAnswer,
            ]);
        }

        // Save auto-graded sum and mark as graded if auto_grade_all is enabled
        if ($totalAutoMarks > 0) {
            $submission->marks = $totalAutoMarks;
            
            // Check if all questions are MCQ and auto_grade_all is enabled
            $allMcq = $assignment->questions->every(function($q) {
                return in_array($q->type, ['mcq_single', 'mcq_multi']);
            });
            
            if ($assignment->auto_grade_all && $allMcq && $assignment->questions->count() > 0) {
                $submission->status = 'graded';
                $submission->graded_at = now();
            }
            
            $submission->save();
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'تم إرسال الواجب بنجاح'], 200);
        }

        return redirect()->back()->with('success', 'تم إرسال الواجب بنجاح');
    }

    public function deleteSubmission(AssignmentSubmission $submission)
    {
        $student = Auth::guard('student')->user();
        
        // Check if this is student's submission
        if (!$student || $submission->student_id != $student->id) {
            return redirect()->route('courses.index')
                ->with('error', 'ليس لديك صلاحية لإدارة هذه الإجابة.');
        }

        // Can't delete if already graded
        if ($submission->status == 'graded') {
            return redirect()->back()
                ->with('error', 'لا يمكن حذف الواجب بعد التصحيح');
        }

        // Delete file(s)
        if ($submission->file_path) {
            $files = json_decode($submission->file_path, true);
            if (is_array($files)) {
                foreach ($files as $file) {
                    Storage::disk('public')->delete($file);
                }
            } else {
                Storage::disk('public')->delete($submission->file_path);
            }
        }

        $submission->delete();

        return redirect()->back()
            ->with('success', 'تم حذف الإجابة بنجاح');
    }

    private function autoGradeMcq($question, array $selectedOptions): array
    {
        $correctIds = $question->correctOptionIds();

        if (empty($correctIds)) {
            return [0, 'auto_graded'];
        }

        $selectedOptions = array_map('intval', $selectedOptions);
        $selectedCorrect = count(array_intersect($selectedOptions, $correctIds));
        $selectedWrong = count(array_diff($selectedOptions, $correctIds));

        if ($question->type === 'mcq_single') {
            $score = ($selectedCorrect === count($correctIds) && $selectedWrong === 0) ? $question->max_marks : 0;
        } else {
            // proportional: correct - wrong, bounded [0, max_marks]
            $denominator = max(count($correctIds), 1);
            $ratio = ($selectedCorrect - $selectedWrong) / $denominator;
            $score = max(0, min(1, $ratio)) * $question->max_marks;
        }

        return [round($score, 2), 'auto_graded'];
    }
}
