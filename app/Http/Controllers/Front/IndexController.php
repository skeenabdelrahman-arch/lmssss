<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\ExamName;
use App\Models\ExamQuestion;
use App\Models\ExamResult;
use App\Models\Lecture;
use App\Models\Month;
use App\Models\Pdf;
use App\Models\Student;
use App\Models\StudentSubscriptions;
use App\Models\LectureView;
use App\Models\Assignment;
use App\Models\PdfView;
use App\Mail\ForgotPasswordMail;
use App\Policies\StudentSubscriptionPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Laravel\Ui\Presets\React;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Str;

// Original methods (unchanged)
class IndexController extends Controller
{
    // Student Dashboard
    public function dashboard()
    {
        $student = Auth::guard('student')->user();
        if (!$student) {
            return redirect()->route('studentLogin')->with('error', 'يرجى تسجيل الدخول أولاً');
        }

        // إحصائيات الطالب
        $subscribedCoursesCount = \App\Models\StudentSubscriptions::where('student_id', $student->id)
            ->where('is_active', 1)
            ->distinct('month_id')
            ->count('month_id');

        $examsTakenCount = \App\Models\ExamResult::where('student_id', $student->id)
            ->whereNotNull('completed_at')
            ->count();

        $lecturesViewedCount = \App\Models\LectureView::where('student_id', $student->id)
            ->distinct('lecture_id')
            ->count('lecture_id');

        // حساب متوسط الدرجات
        $examResults = \App\Models\ExamResult::where('student_id', $student->id)
            ->whereNotNull('completed_at')
            ->where('degree', '>', 0)
            ->get();

        $averageGrade = 0;
        if ($examResults->count() > 0) {
            $totalPercentage = 0;
            $validExams = 0;

            foreach ($examResults as $result) {
                $exam = $result->exam;
                if ($exam && $exam->questions->count() > 0) {
                    $totalDegree = $exam->questions->sum('Q_degree');
                    if ($totalDegree > 0) {
                        $percentage = ($result->degree / $totalDegree) * 100;
                        $totalPercentage += $percentage;
                        $validExams++;
                    }
                }
            }

            if ($validExams > 0) {
                $averageGrade = $totalPercentage / $validExams;
            }
        }

        // الكورسات المشترك بها
        $subscribedCourses = \App\Models\Month::whereHas('subscriptions', function ($query) use ($student) {
            $query->where('student_id', $student->id)
                ->where('is_active', 1);
        })->orderBy('display_order')->orderBy('name')->get();

        return view('front.student_dashboard', compact(
            'subscribedCoursesCount',
            'examsTakenCount',
            'lecturesViewedCount',
            'averageGrade',
            'subscribedCourses'
        ));
    }

    public function index()
    {
        $student = Auth::guard('student')->user();
        if (!$student) {
            return redirect()->route('studentLogin')->with('error', 'يرجى تسجيل الدخول أولاً');
        }

        $studentGrade = trim($student->grade ?? '');
        $studentGrade = $this->normalizeGradeForQuery($studentGrade);

        $query = Month::query();
        if (!empty($studentGrade) && $studentGrade !== 'غير محدد') {
            $query->where('grade', $studentGrade);
        }

        $signupGrades = signup_grades();
        if (!empty($signupGrades)) {
            $caseStatement = "CASE grade ";
            foreach ($signupGrades as $index => $grade) {
                $order = $index + 1;
                $caseStatement .= "WHEN '" . addslashes($grade['value']) . "' THEN {$order} ";
            }
            $caseStatement .= "ELSE " . (count($signupGrades) + 1) . " END";
            $monthes = $query->orderByRaw($caseStatement)->orderBy('display_order')->orderBy('name')->get();
        } else {
            $monthes = $query->orderBy('display_order')->orderBy('name')->get();
        }

        if (!isset($monthes) || $monthes === null) {
            $monthes = collect();
        }

        return view('front.courses', compact('monthes', 'studentGrade'));
    }

    private function normalizeGradeForQuery($grade)
    {
        if (empty($grade)) {
            return null;
        }

        $grade = trim($grade);
        $grade = preg_replace('/\s+/', ' ', $grade);

        $signupGrades = signup_grades();

        if (empty($signupGrades)) {
            return $grade;
        }

        foreach ($signupGrades as $gradeOption) {
            $value = trim($gradeOption['value'] ?? '');
            $label = trim($gradeOption['label'] ?? '');

            if (strcasecmp($grade, $value) === 0 || strcasecmp($grade, $label) === 0) {
                return $value;
            }

            if (
                mb_strtolower($grade, 'UTF-8') === mb_strtolower($value, 'UTF-8') ||
                mb_strtolower($grade, 'UTF-8') === mb_strtolower($label, 'UTF-8')
            ) {
                return $value;
            }
        }

        return $grade;
    }

    public function course_details($month_id)
    {
        $user_id = Auth::guard('student')->user()->id;
        $month = Month::findOrFail($month_id);

        // إذا كان الطالب مشترك، يروح لمحتوى الكورس مباشرة
        if (StudentSubscriptionPolicy::canAccessMonth($month_id)) {
            return redirect()->route('month.content', ['month_id' => $month_id]);
        }

        // إذا لم يكن مشترك، يعرض صفحة التفاصيل للاشتراك
        return view('front.course_details', compact('month_id', 'month'));
    }

    public function month_content(Request $request)
    {
        $month_id = $request->input('month_id');

        // التحقق من وجود month_id
        if (!$month_id) {
            return redirect()->route('courses.index')->with('error', 'الكورس غير موجود!');
        }

        // التحقق من الصلاحية
        if (!StudentSubscriptionPolicy::canAccessMonth($month_id)) {
            // التحقق من سبب إلغاء التفعيل
            $deactivationReason = StudentSubscriptionPolicy::getDeactivationReason($month_id);
            $errorMessage = $deactivationReason ?: 'انت غير مشترك في هذا الكورس!';
            return redirect()->route('courses.index')->with('error', $errorMessage);
        }

        return view('front.course_details', compact('month_id'));
    }

    public function month_content_lectures(Request $request)
    {
        $month_id = $request->input('month_id');

        if (!StudentSubscriptionPolicy::canAccessMonth($month_id)) {
            $deactivationReason = StudentSubscriptionPolicy::getDeactivationReason($month_id);
            $errorMessage = $deactivationReason ?: 'انت غير مشترك في الشهر !';
            return redirect()->route('courses.index')->with('error', $errorMessage);
        }

        $student = Auth::guard('student')->user();
        $videos = Lecture::with([
            'quiz',
            'pdfs',
            'assignments' => function ($q) {
                $q->whereIn('status', ['active', '1', 1, true]);
            },
            'lectureViews' => function ($q) use ($student) {
                $q->where('student_id', $student->id);
            }
        ])
            ->where('month_id', $month_id)
            ->where('status', 1)
            ->orderBy('display_order')
            ->orderBy('id')
            ->get();
        return view('front.videos', compact('videos', 'student'));
    }

    public function lecture(Request $request)
    {
        $lec_id = $request->input('lecture_id');
        $lecture = Lecture::with([
            'quiz',
            'pdfs' => function ($q) {
                $q->where('status', 1)->orderBy('display_order')->orderBy('id', 'desc');
            }
        ])->where('id', $lec_id)->first();
        $student = Auth::guard('student')->user();

        // التحقق من قيود المحاضرات
        if (\App\Models\LectureRestriction::isRestricted($student->id, $lec_id)) {
            return redirect()->route('videos', ['month_id' => $lecture->month_id])
                ->with('error', 'عذراً، هذه المحاضرة غير متاحة لك حالياً');
        }

        $previousLectures = Lecture::where('month_id', $lecture->month_id)
            ->where('id', '<', $lec_id)
            ->where('status', 1)
            ->orderBy('id', 'asc')
            ->with('quiz')
            ->get();

        foreach ($previousLectures as $prevLecture) {
            if ($prevLecture->quiz) {
                $quiz = $prevLecture->quiz;

                // التحقق من إمكانية الوصول للكويز
                if ($quiz->canStudentAccess($student)) {
                    // التحقق من الإجبارية حسب نوع الطالب
                    if ($quiz->isRequiredForStudent($student)) {
                        $attempt = $quiz->getStudentAttempt($student->id);
                        if (!$attempt || !$attempt->is_passed) {
                            return redirect()->route('videos', ['month_id' => $lecture->month_id])
                                ->with('error', 'يجب إكمال الكويز الخاص بالمحاضرة السابقة أولاً: ' . $prevLecture->title)
                                ->with('quiz_id', $quiz->id);
                        }
                    }
                }
            }
        }

        $studentId = Auth::guard('student')->user()->id;
        $viewExists = \App\Models\LectureView::where('lecture_id', $lec_id)
            ->where('student_id', $studentId)
            ->exists();

        if (!$viewExists) {
            \App\Models\LectureView::create([
                'lecture_id' => $lec_id,
                'student_id' => $studentId,
                'viewed_at' => now(),
            ]);

            $lecture->increment('views');
            session(['viewed_lecture_' . $lec_id . '_' . $studentId => true]);
        }

        $assignments = Assignment::where('lecture_id', $lec_id)
            ->where('status', 'active')
            ->orderBy('display_order')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('front.lecture', compact('lecture', 'student', 'assignments'));
    }

    public function month_content_pdfs(Request $request)
    {
        $month_id = $request->input('month_id');

        if (!StudentSubscriptionPolicy::canAccessMonth($month_id)) {
            $deactivationReason = StudentSubscriptionPolicy::getDeactivationReason($month_id);
            $errorMessage = $deactivationReason ?: 'انت غير مشترك في الشهر !';
            return redirect()->route('courses.index')->with('error', $errorMessage);
        }

        $pdfs = Pdf::where('month_id', $month_id)->where('status', 1)->orderBy('display_order')->orderBy('id')->get();
        return view('front.pdfs', compact('pdfs'));
    }

    public function viewPdf($id)
    {
        $pdf = Pdf::findOrFail($id);

        if (empty($pdf->file_url)) {
            return redirect()->route('courses.index')->with('error', 'رابط المذكرة غير متوفر');
        }

        if (!StudentSubscriptionPolicy::canAccessMonth($pdf->month_id)) {
            $deactivationReason = StudentSubscriptionPolicy::getDeactivationReason($pdf->month_id);
            $errorMessage = $deactivationReason ?: 'انت غير مشترك في الشهر !';
            return redirect()->route('courses.index')->with('error', $errorMessage);
        }

        if ($pdf->status != 1) {
            return redirect()->route('courses.index')->with('error', 'المذكرة غير متاحة حالياً');
        }

        $studentId = Auth::guard('student')->user()->id;
        $viewExists = \App\Models\PdfView::where('pdf_id', $pdf->id)
            ->where('student_id', $studentId)
            ->exists();

        if (!$viewExists) {
            \App\Models\PdfView::create([
                'pdf_id' => $pdf->id,
                'student_id' => $studentId,
                'viewed_at' => now(),
            ]);
        }

        // استخدام route محمي لخدمة الملف المحلي
        $pdfUrl = route('pdf.serve', $pdf->id);

        return view('front.pdf_viewer', compact('pdf', 'pdfUrl'));
    }

    public function servePdf($id)
    {
        $pdf = Pdf::findOrFail($id);

        // التحقق من الصلاحيات
        if (!StudentSubscriptionPolicy::canAccessMonth($pdf->month_id)) {
            abort(403, 'غير مصرح لك بالوصول لهذا الملف');
        }

        if ($pdf->status != 1) {
            abort(404, 'الملف غير متاح');
        }

        // البحث عن الملف في المسارات المحتملة
        $fileUrl = trim($pdf->file_url);
        $fileUrl = preg_replace('/^\/?storage\//', '', $fileUrl);

        $paths = [
            storage_path('app/public/' . $fileUrl),
            storage_path('app/public/media/' . basename($fileUrl)),
            public_path('storage/' . $fileUrl),
            public_path('storage/media/' . basename($fileUrl)),
            public_path('upload_files/' . $fileUrl),
            public_path('upload_files/' . basename($fileUrl)),
        ];

        $filePath = null;
        foreach ($paths as $path) {
            if (file_exists($path) && is_file($path)) {
                $filePath = $path;
                break;
            }
        }

        if (!$filePath) {
            \Log::error('PDF file not found', [
                'pdf_id' => $id,
                'file_url' => $pdf->file_url,
                'searched_paths' => $paths
            ]);
            abort(404, 'الملف غير موجود');
        }

        // خدمة الملف
        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"'
        ]);
    }

    // New methods for image-based PDF viewer
    public function getPdfPageImage($id, $page = 1)
    {
        try {
            $pdf = Pdf::findOrFail($id);
            if (!StudentSubscriptionPolicy::canAccessMonth($pdf->month_id) || $pdf->status != 1 || empty($pdf->file_url)) {
                abort(403, 'Access denied');
            }

            $page = max(1, intval($page));
            $cacheKey = 'pdf_' . $pdf->id . '_' . $page;
            $cacheDir = storage_path('app/pdf_cache');
            if (!is_dir($cacheDir))
                @mkdir($cacheDir, 0755, true);

            $cachedImage = $cacheDir . '/' . md5($cacheKey) . '.png';
            if (file_exists($cachedImage) && filesize($cachedImage) > 0) {
                return response()->file($cachedImage, [
                    'Content-Type' => 'image/png',
                    'Cache-Control' => 'public, max-age=86400'
                ]);
            }

            $localPath = $this->getPdfFilePath(trim($pdf->file_url));
            if (!$localPath || !file_exists($localPath)) {
                \Log::error('PDF file not accessible for page image', ['pdf_id' => $id, 'page' => $page, 'path' => $localPath]);
                abort(404, 'PDF file not found');
            }

            $image = $this->convertPdfPageToImage($localPath, $page);
            if (!$image) {
                \Log::error('Failed to convert PDF page to image', ['pdf_id' => $id, 'page' => $page]);
                abort(500, 'Failed to convert page');
            }

            imagepng($image, $cachedImage, 8);
            imagedestroy($image);

            return response()->file($cachedImage, [
                'Content-Type' => 'image/png',
                'Cache-Control' => 'public, max-age=86400'
            ]);
        } catch (\Throwable $e) {
            \Log::error('Error getting PDF page image', ['pdf_id' => $id, 'page' => $page, 'error' => $e->getMessage()]);
            abort(500, 'Server error: ' . $e->getMessage());
        }
    }

    public function getPdfPageCount($id)
    {
        try {
            $pdf = Pdf::findOrFail($id);

            if (!StudentSubscriptionPolicy::canAccessMonth($pdf->month_id) || $pdf->status != 1 || empty($pdf->file_url)) {
                \Log::warning('Access denied to PDF', ['pdf_id' => $id]);
                return response()->json(['error' => 'No access to this PDF'], 403);
            }

            $localPath = $this->getPdfFilePath(trim($pdf->file_url));

            if (!$localPath) {
                $errorMsg = 'Failed to download PDF file';
                \Log::warning('PDF file path not found', ['pdf_id' => $id, 'file_url' => $pdf->file_url, 'error' => $errorMsg]);
                return response()->json(['error' => $errorMsg], 404);
            }

            if (!file_exists($localPath)) {
                $errorMsg = 'PDF file not found on disk: ' . $localPath;
                \Log::warning('PDF file does not exist on disk', ['pdf_id' => $id, 'path' => $localPath]);
                return response()->json(['error' => $errorMsg], 404);
            }

            // Check file size
            $fileSize = @filesize($localPath);
            if ($fileSize === false || $fileSize == 0) {
                $errorMsg = 'PDF file is empty or inaccessible';
                \Log::warning('PDF file is empty', ['pdf_id' => $id, 'path' => $localPath, 'size' => $fileSize]);
                return response()->json(['error' => $errorMsg], 400);
            }

            $count = $this->getPdfPageCountFromFile($localPath);

            if ($count < 1) {
                $errorMsg = 'PDF file has no readable pages. File may be corrupted.';
                \Log::warning('PDF has no pages', ['pdf_id' => $id, 'path' => $localPath, 'count' => $count]);
                return response()->json(['error' => $errorMsg], 500);
            }

            \Log::debug('PDF page count retrieved', ['pdf_id' => $id, 'count' => $count]);
            return response()->json(['page_count' => $count]);
        } catch (\Throwable $e) {
            $errorMsg = $e->getMessage();
            \Log::error('Error getting PDF page count', ['error' => $errorMsg, 'pdf_id' => $id, 'trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Server error: ' . substr($errorMsg, 0, 100)], 500);
        }
    }

    private function getPdfFilePath(string $fileUrl): ?string
    {
        $isExternal = str_starts_with($fileUrl, 'http://') || str_starts_with($fileUrl, 'https://');

        if ($isExternal) {
            $cacheDir = storage_path('app/pdf_downloads');
            if (!is_dir($cacheDir))
                @mkdir($cacheDir, 0755, true);
            $filename = md5($fileUrl) . '.pdf';
            $cachedPath = $cacheDir . '/' . $filename;

            if (file_exists($cachedPath))
                return $cachedPath;

            try {
                $content = @file_get_contents($fileUrl, false, stream_context_create(['http' => ['timeout' => 15], 'https' => ['timeout' => 15]]));
                if ($content) {
                    @file_put_contents($cachedPath, $content);
                    return $cachedPath;
                }
            } catch (\Throwable $e) {
            }
            return null;
        }

        $fileUrl = preg_replace('/^\/?storage\//', '', $fileUrl);
        $paths = [
            public_path('upload_files/' . $fileUrl),
            storage_path('app/public/' . $fileUrl),
            storage_path('app/public/media/' . basename($fileUrl)),
            public_path('upload_files/' . basename($fileUrl)),
        ];

        foreach ($paths as $path) {
            if (file_exists($path))
                return $path;
        }
        return null;
    }

    private function convertPdfPageToImage(string $pdfPath, int $page): ?\GdImage
    {
        $cacheDir = storage_path('app/pdf_cache');
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }

        $cacheFile = $cacheDir . '/' . md5($pdfPath) . '_page_' . $page . '.png';

        // Return cached image if exists
        if (file_exists($cacheFile) && filesize($cacheFile) > 0) {
            $image = @imagecreatefrompng($cacheFile);
            if ($image !== false) {
                return $image;
            }
        }

        $image = null;

        // Try Imagick
        if (extension_loaded('imagick')) {
            try {
                $imagick = new \Imagick();
                $imagick->setResolution(150, 150);
                $imagick->readImage($pdfPath . '[' . ($page - 1) . ']');
                $imagick->setImageFormat('png');
                $imageData = $imagick->getImageBlob();
                $image = @imagecreatefromstring($imageData);
                $imagick->destroy();

                if ($image !== false) {
                    imagepng($image, $cacheFile, 8);
                    return $image;
                }
            } catch (\Throwable $e) {
                \Log::debug('Imagick conversion failed', ['page' => $page, 'error' => $e->getMessage()]);
            }
        }

        // Try convert command (Windows compatible)
        if ($this->commandExists('convert')) {
            try {
                $outputPath = tempnam(sys_get_temp_dir(), 'pdf_');
                $sourceSpec = $pdfPath . '[' . ($page - 1) . ']';
                $cmd = sprintf('convert -density 150 %s -quality 85 %s', escapeshellarg($sourceSpec), escapeshellarg($outputPath));
                @exec($cmd, $output, $code);

                if ($code === 0 && file_exists($outputPath) && filesize($outputPath) > 0) {
                    copy($outputPath, $cacheFile);
                    $image = @imagecreatefrompng($outputPath);
                    @unlink($outputPath);

                    if ($image !== false) {
                        return $image;
                    }
                }
                @unlink($outputPath);
            } catch (\Throwable $e) {
                \Log::debug('convert command failed', ['page' => $page, 'error' => $e->getMessage()]);
            }
        }

        // Try pdftoppm (Windows compatible)
        if ($this->commandExists('pdftoppm')) {
            try {
                $tmpDir = sys_get_temp_dir();
                $tmpFile = tempnam($tmpDir, 'pdf_page_');
                $cmd = sprintf(
                    'pdftoppm -png -singlefile -r 150 -f %d -l %d %s %s',
                    $page,
                    $page,
                    escapeshellarg($pdfPath),
                    escapeshellarg($tmpFile)
                );
                @exec($cmd, $output, $code);

                $pngFile = $tmpFile . '.png';
                if ($code === 0 && file_exists($pngFile) && filesize($pngFile) > 0) {
                    copy($pngFile, $cacheFile);
                    $image = @imagecreatefrompng($pngFile);
                    @unlink($pngFile);
                    @unlink($tmpFile);

                    if ($image !== false) {
                        return $image;
                    }
                }
                @unlink($pngFile);
                @unlink($tmpFile);
            } catch (\Throwable $e) {
                \Log::debug('pdftoppm conversion failed', ['page' => $page, 'error' => $e->getMessage()]);
            }
        }

        \Log::error('All PDF conversion methods failed', [
            'pdf_path' => $pdfPath,
            'page' => $page,
            'imagick_available' => extension_loaded('imagick'),
            'pdftoppm_available' => $this->commandExists('pdftoppm'),
            'convert_available' => $this->commandExists('convert')
        ]);

        return null;
    }

    private function getPdfPageCountFromFile(string $pdfPath): int
    {
        if (extension_loaded('imagick')) {
            try {
                $imagick = new \Imagick($pdfPath);
                $count = $imagick->getNumberImages();
                $imagick->destroy();
                return max(1, $count);
            } catch (\Throwable $e) {
                \Log::debug('Imagick failed to get page count', ['error' => $e->getMessage()]);
            }
        }

        // Try pdfinfo on Windows without grep
        if ($this->commandExists('pdfinfo')) {
            try {
                $output = @shell_exec(escapeshellarg($pdfPath) . ' | pdfinfo');
                if (!$output) {
                    $output = @shell_exec('pdfinfo ' . escapeshellarg($pdfPath));
                }

                if ($output && preg_match('/Pages\s*:\s*(\d+)/i', $output, $matches)) {
                    return max(1, intval($matches[1]));
                }
            } catch (\Throwable $e) {
                \Log::debug('pdfinfo failed', ['error' => $e->getMessage()]);
            }
        }

        // Try pdftoppm to count pages
        if ($this->commandExists('pdftoppm')) {
            try {
                $tmpDir = sys_get_temp_dir();
                $tmpPrefix = tempnam($tmpDir, 'pdf_');
                $cmd = sprintf('pdftoppm -l 1 %s %s 2>&1', escapeshellarg($pdfPath), escapeshellarg($tmpPrefix));
                @exec($cmd, $output, $returnCode);

                // Try to get total pages by looking for last page
                for ($i = 1; $i <= 10000; $i++) {
                    $pageFile = $tmpPrefix . '-' . str_pad($i, 6, '0', STR_PAD_LEFT) . '.ppm';
                    if (!file_exists($pageFile)) {
                        if ($i > 1) {
                            return $i - 1;
                        }
                        break;
                    }
                    @unlink($pageFile);
                }
            } catch (\Throwable $e) {
                \Log::debug('pdftoppm failed', ['error' => $e->getMessage()]);
            }
        }

        // Default fallback
        return 1;
    }

    private function commandExists(string $cmd): bool
    {
        $whereIsCmd = (PHP_OS_FAMILY === 'Windows') ? 'where' : 'which';
        $process = @proc_open("$whereIsCmd $cmd", [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);
        if ($process === false)
            return false;
        $output = @stream_get_contents($pipes[1]);
        @proc_close($process);
        return !empty($output);
    }

    // ... Rest of original methods continue unchanged ...
    public function student_profile(Request $request)
    {
        $student = Auth::guard('student')->user();

        if (!$student) {
            return redirect()->route('student.login')->with('error', 'يرجى تسجيل الدخول أولاً');
        }

        $months = StudentSubscriptions::where('student_id', $student->id)
            ->where('is_active', 1)
            ->with('month')
            ->get()
            ->filter(function ($subscription) {
                return $subscription->month !== null;
            });

        // جلب نتائج الامتحانات للطالب (فقط الامتحانات الموجودة)
        $exam_results = ExamResult::where('student_id', $student->id)
            ->whereHas('exam')
            ->with(['exam', 'exam.questions'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('front.student_profile', compact('student', 'months', 'exam_results'));
    }

    public function month_content_exams(Request $request)
    {
        $month_id = $request->input('month_id');

        if (!StudentSubscriptionPolicy::canAccessMonth($month_id)) {
            $deactivationReason = StudentSubscriptionPolicy::getDeactivationReason($month_id);
            $errorMessage = $deactivationReason ?: 'انت غير مشترك في الشهر !';
            return redirect()->route('courses.index')->with('error', $errorMessage);
        }

        $exams = ExamName::where('month_id', $month_id)->where('status', 1)->orderBy('display_order')->orderBy('id')->get();

        // التحقق التلقائي من إظهار النتائج للامتحانات المغلقة
        foreach ($exams as $exam) {
            $exam->autoShowResultsIfClosed();
        }

        return view('front.exams', compact('exams'));
    }

    public function exam_questions(Request $request)
    {
        $exam_id = $request->input('exam_id');
        $studentId = Auth::guard('student')->user()->id;

        $exam = ExamName::findOrFail($exam_id);

        // التحقق من وجود نتيجة سابقة أو محاولة قائمة
        $existingResult = ExamResult::where('exam_id', $exam_id)
            ->where('student_id', $studentId)
            ->first();

        // إذا كانت هناك نتيجة مكتملة (completed_at موجود)، منع الدخول
        if ($existingResult && $existingResult->completed_at) {
            return redirect()->route('courses.index')->with('error', 'لقد قمت بإجراء هذا الامتحان من قبل! يمكنك مراجعة النتيجة من صفحة الامتحانات.');
        }

        // إذا لم توجد نتيجة، نُنشئ واحدة جديدة
        if (!$existingResult) {
            $existingResult = new ExamResult();
            $existingResult->exam_id = $exam_id;
            $existingResult->student_id = $studentId;
            $existingResult->started_at = now();
            $existingResult->time_elapsed = 0;

            // اختيار نموذج عشوائي للطالب
            $availableModels = ExamQuestion::where('exam_id', $exam_id)
                ->distinct()
                ->pluck('model_name')
                ->toArray();

            // إذا لم توجد نماذج، نستخدم A كافتراضي
            $existingResult->assigned_model = !empty($availableModels)
                ? $availableModels[array_rand($availableModels)]
                : 'A';

            $existingResult->save();
        }

        // تحميل الأسئلة حسب النموذج المعين للطالب
        $questions = ExamQuestion::where('exam_id', $exam_id)
            ->where('model_name', $existingResult->assigned_model)
            ->get();

        // إذا كان الترتيب العشوائي مفعل
        if ($exam->randomize_questions) {
            // استخدام seed ثابت بناءً على student_id + exam_id لضمان نفس الترتيب في كل مرة
            $seed = $studentId + $exam_id;
            srand($seed);

            $questionsArray = $questions->toArray();
            shuffle($questionsArray);
            $questions = collect($questionsArray);

            // إعادة تعيين seed للعشوائية الطبيعية
            srand();
        }

        $data['exam_name'] = $exam;
        $data['exam_questions'] = $questions;
        $data['exam_result'] = $existingResult;
        $data['time_elapsed'] = $existingResult->time_elapsed ?? 0; // الوقت المنقضي بالثواني

        return view('front.exam_questions', $data);
    }

    public function exam_review(Request $request)
    {
        $exam_id = $request->input('exam_id');
        $data['exam_name'] = ExamName::findorfail($exam_id);

        // التحقق التلقائي من إظهار النتائج إذا كان الامتحان مغلق
        $data['exam_name']->autoShowResultsIfClosed();

        $data['exam_questions'] = ExamQuestion::where('exam_id', $exam_id)->get();
        $data['exam_result'] = ExamResult::where('exam_id', $exam_id)->where('student_id', Auth::guard('student')->user()->id)->first();

        if ($data['exam_result']) {
            $currentUser = Auth::guard('student')->user();
            if (!Gate::forUser($currentUser)->allows('view', $data['exam_result'])) {
                abort(403, 'ليس لديك صلاحية للوصول لهذه النتيجة');
            }
        }

        $score = ExamQuestion::where('exam_id', $exam_id)->get();
        $exam_degree = 0;
        foreach ($score as $question) {
            $exam_degree += $question->Q_degree;
        }
        $data['exam_degree'] = $exam_degree;
        return view('front.exam_review', $data);
    }

    public function searchInExcel(Request $request)
    {
        $request->validate(['phone' => 'required']);

        $filePath = base_path('upload_files/students.xlsx');
        if (!file_exists($filePath)) {
            return back()->with('error', 'ملف البيانات غير موجود.');
        }

        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();

        $phoneNumber = $request->phone;
        $foundStudent = null;

        foreach ($sheet->getRowIterator() as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(true);

            $data = [];
            foreach ($cellIterator as $cell) {
                $data[] = $cell->getValue();
            }

            if (isset($data[2]) && $data[2] == $phoneNumber) {
                $foundStudent = [
                    'code' => $data[0] ?? 'غير متوفر',
                    'name' => $data[1] ?? 'غير متوفر',
                    'phone' => $data[2],
                ];
                break;
            }
        }

        if ($foundStudent) {
            return view('front.search_excel', compact('foundStudent'));
        } else {
            return back()->with('error', 'لم يتم العثور على الطالب.');
        }
    }

    public function showresetPassword()
    {
        return view('front.resetpassword');
    }

    public function resetPassword(Request $request)
    {
        $count = Student::where('email', $request->email)->count();
        if ($count > 0) {
            $user = Student::where('email', $request->email)->first();
            $user->remember_token = Str::random(50);
            $user->save();
            Mail::to($user->email)->send(new ForgotPasswordMail($user));
            return redirect()->back()->with('success', 'تم إرسال رابط إعادة تعيين كلمة المرور إلى بريدك الإلكتروني.');
        } else {
            return redirect()->back()->with('error', 'هذا البريد الإلكتروني غير موجود');
        }
    }

    public function changePassword($token)
    {
        $user = Student::where('remember_token', $token);
        if ($user->count() == 0) {
            abort(403);
        }
        $user = $user->first();
        return view('front.changepassword', compact('token'));
    }

    public function postchangePassword($token, Request $request)
    {
        $user = Student::where('remember_token', $token);
        if ($user->count() == 0) {
            abort(403);
        }
        $user = $user->first();
        $user->password = $request->password;
        $user->remember_token = null;
        $user->save();
        return redirect('student-login')->with('success', 'تم اعادة تعيين كلمة المرور بنجاح');
    }

    public function debugPdfTools()
    {
        // Only allow authenticated students
        if (!Auth::guard('student')->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $diagnostics = [
            'timestamp' => now(),
            'php_version' => phpversion(),
            'os' => PHP_OS,
            'php_modules' => [
                'imagick' => extension_loaded('imagick') ? 'Available' : 'Not installed',
                'gd' => extension_loaded('gd') ? 'Available' : 'Not installed',
            ],
            'system_commands' => [
                'convert' => $this->commandExists('convert') ? 'Available' : 'Not found',
                'pdftoppm' => $this->commandExists('pdftoppm') ? 'Available' : 'Not found',
                'pdfinfo' => $this->commandExists('pdfinfo') ? 'Available' : 'Not found',
                'which_or_where' => $this->commandExists('which') ? 'which available' : ($this->commandExists('where') ? 'where available' : 'Neither found'),
            ],
            'storage_paths' => [
                'cache_dir' => storage_path('app/pdf_cache'),
                'cache_dir_exists' => is_dir(storage_path('app/pdf_cache')),
                'cache_writable' => is_writable(storage_path('app/pdf_cache')) || is_writable(storage_path('app')),
                'download_dir' => storage_path('app/pdf_downloads'),
                'download_dir_exists' => is_dir(storage_path('app/pdf_downloads')),
            ],
            'test_imagick' => $this->testImagick(),
            'test_commands' => $this->testSystemCommands(),
        ];

        \Log::info('PDF Tools Diagnostic', $diagnostics);

        return response()->json($diagnostics);
    }

    private function testImagick(): array
    {
        $result = ['status' => 'Not available'];

        if (!extension_loaded('imagick')) {
            return $result;
        }

        try {
            $imagick = new \Imagick();
            $version = $imagick->getVersion();
            $result['status'] = 'Available';
            $result['version'] = $version;
        } catch (\Throwable $e) {
            $result['status'] = 'Error';
            $result['error'] = $e->getMessage();
        }

        return $result;
    }

    private function testSystemCommands(): array
    {
        $results = [];

        // Test convert
        if ($this->commandExists('convert')) {
            $output = null;
            $code = null;
            @exec('convert --version 2>&1 | head -1', $output, $code);
            $results['convert'] = [
                'available' => true,
                'version_info' => isset($output[0]) ? $output[0] : 'Unknown'
            ];
        }

        // Test pdftoppm
        if ($this->commandExists('pdftoppm')) {
            $output = null;
            $code = null;
            @exec('pdftoppm -v 2>&1 | head -1', $output, $code);
            $results['pdftoppm'] = [
                'available' => true,
                'version_info' => isset($output[0]) ? $output[0] : 'Unknown'
            ];
        }

        // Test pdfinfo
        if ($this->commandExists('pdfinfo')) {
            $output = null;
            $code = null;
            @exec('pdfinfo -v 2>&1 | head -1', $output, $code);
            $results['pdfinfo'] = [
                'available' => true,
                'version_info' => isset($output[0]) ? $output[0] : 'Unknown'
            ];
        }

        return $results;
    }
    public function trackProgress(Request $request, $lecture_id)
    {
        $request->validate([
            'current_time' => 'required|numeric|min:0',
            'duration' => 'required|numeric|min:0',
            'percentage' => 'required|numeric|min:0|max:100'
        ]);

        $student = Auth::guard('student')->user();
        if (!$student) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $lecture = Lecture::findOrFail($lecture_id);

        // الحصول على النسبة المطلوبة من الإعدادات
        $requiredPercentage = (float) \App\Models\Setting::get('video_completion_percentage', 80);
        $trackingEnabled = \App\Models\Setting::get('video_tracking_enabled', '1') == '1';

        if (!$trackingEnabled) {
            return response()->json(['success' => true, 'message' => 'Tracking is disabled']);
        }

        // تحديث أو إنشاء سجل المشاهدة
        $view = \App\Models\LectureView::firstOrNew([
            'lecture_id' => $lecture_id,
            'student_id' => $student->id
        ]);

        $view->watch_percentage = max((float) $view->watch_percentage, (float) $request->percentage);
        $view->watch_duration = $request->current_time;
        $view->last_position = $request->current_time;
        $view->viewed_at = now();
        $view->save();

        // تحديث حالة الاكتمال إذا وصل للنسبة المطلوبة ولم يكن مكتتملاً من قبل
        if (!$view->completed && $request->percentage >= $requiredPercentage) {
            $view->update([
                'completed' => 1,
                'completed_at' => now()
            ]);

            // زيادة عداد المشاهدات عند الإكمال لأول مرة
            $lecture->increment('views');
        }

        return response()->json([
            'success' => true,
            'completed' => (bool) $view->completed,
            'percentage' => $view->watch_percentage
        ]);
    }
}