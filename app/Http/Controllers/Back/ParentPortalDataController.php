<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\StudentAttendance;
use App\Models\StudentPaymentRecord;
use App\Models\StudentTask;
use App\Models\Student;
use App\Models\Month;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ParentPortalDataController extends Controller
{
    /**
     * Show parent portal data management page
     */
    public function index()
    {
        return view('back.parent_portal.index');
    }

    /**
     * Import attendance data from Excel
     */
    public function importAttendance(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        try {
            $file = $request->file('file');
            $spreadsheet = IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray();

            $imported = 0;
            $errors = [];
            $failedRows = [];

            foreach ($data as $index => $row) {
                if ($index == 0) continue; // Skip header

                // التنسيق الجديد: الحالة | رقم هاتف الطالب | اليوم
                $status = strtolower(trim($row[0] ?? ''));
                $studentPhone = trim($row[1] ?? '');
                $attendanceDate = $row[2] ?? null;
                
                // تحديد حالة الحضور
                $isPresent = in_array($status, ['حاضر', 'نعم', 'present', 'yes']);

                if (!$studentPhone) {
                    $errorMsg = "رقم هاتف الطالب مفقود";
                    $errors[] = "الصف {$index}: {$errorMsg}";
                    $failedRows[] = array_merge($row, [$errorMsg]);
                    continue;
                }

                // تطبيع الطريقة المستخدمة في بوابة أولياء الأمور
                $studentPhoneDigits = preg_replace('/\D+/', '', $studentPhone);
                
                $student = Student::where(function($q) use ($studentPhone, $studentPhoneDigits) {
                    $q->where('student_phone', $studentPhone)
                      ->orWhere('student_phone', 'like', "%{$studentPhoneDigits}")
                      ->orWhere('student_code', $studentPhone);
                })->first();
                
                if (!$student) {
                    $errors[] = "الصف {$index}: الطالب غير موجود ({$studentPhone})";
                    continue;
                }

                try {
                    $parsedDate = \Carbon\Carbon::createFromFormat('Y-m-d', $attendanceDate);
                    
                    // التحقق من عدم تكرار البيانات
                    $exists = StudentAttendance::where([
                        'student_id' => $student->id,
                        'attendance_date' => $parsedDate->toDateString(),
                    ])->exists();

                    if ($exists) {
                        \Log::info("Attendance Record Skipped (Duplicate)", [
                            'student_id' => $student->id,
                            'attendance_date' => $parsedDate->toDateString()
                        ]);
                        continue;
                    }
                    
                    StudentAttendance::updateOrCreate(
                        [
                            'student_id' => $student->id,
                            'attendance_date' => $parsedDate,
                        ],
                        [
                            'is_present' => $isPresent,
                            'notes' => null,
                        ]
                    );
                    $imported++;
                } catch (\Exception $e) {
                    $errorMsg = "خطأ في المعالجة - {$e->getMessage()}";
                    $errors[] = "الصف {$index}: {$errorMsg}";
                    $failedRows[] = array_merge($row, [$errorMsg]);
                }
            }

            // حفظ الصفوف الفاشلة في الـ session
            if (!empty($failedRows)) {
                session(['attendance_failed_rows' => $failedRows]);
            }

            return back()->with([
                'success' => "تم استيراد {$imported} سجل حضور",
                'errors' => $errors,
                'has_failed_rows' => !empty($failedRows),
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ في الملف: ' . $e->getMessage());
        }
    }

    /**
     * Import payment data from Excel
     */
    public function importPayments(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        try {
            $file = $request->file('file');
            $spreadsheet = IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray();

            $imported = 0;
            $errors = [];
            $uniqueMonths = [];
            $paymentData = [];
            $failedRows = [];

            foreach ($data as $index => $row) {
                if ($index == 0) continue; // Skip header

                // التنسيق الجديد: رقم الطالب | المبلغ | الشهر (mm/yyyy) | تاريخ الدفع
                $studentPhone = trim($row[0] ?? '');
                $amount = $row[1] ?? null;
                $monthStr = trim($row[2] ?? ''); // mm/yyyy
                $paymentDateStr = trim($row[3] ?? ''); // 2025-12-16 01:12 AM

                if (!$studentPhone || !$amount) {
                    $errorMsg = "بيانات ناقصة";
                    $errors[] = "الصف {$index}: {$errorMsg}";
                    $failedRows[] = array_merge($row, [$errorMsg]);
                    continue;
                }

                // البحث عن الطالب برقم الهاتف أو الكود
                $studentPhoneDigits = preg_replace('/\D+/', '', $studentPhone);
                
                $student = Student::where(function($q) use ($studentPhone, $studentPhoneDigits) {
                    $q->where('student_phone', $studentPhone)
                      ->orWhere('student_phone', 'like', "%{$studentPhoneDigits}")
                      ->orWhere('student_code', $studentPhone);
                })->first();
                
                if (!$student) {
                    $errorMsg = "الطالب غير موجود ({$studentPhone})";
                    $errors[] = "الصف {$index}: {$errorMsg}";
                    $failedRows[] = array_merge($row, [$errorMsg]);
                    continue;
                }

                // معالجة تاريخ الدفع (2025-12-16 01:12 AM)
                try {
                    $paymentDate = \Carbon\Carbon::createFromFormat('Y-m-d h:i A', $paymentDateStr);
                } catch (\Exception $e) {
                    try {
                        $paymentDate = \Carbon\Carbon::parse($paymentDateStr);
                    } catch (\Exception $e2) {
                        $paymentDate = \Carbon\Carbon::now();
                        $errors[] = "الصف {$index}: تنسيق التاريخ غير صحيح ({$paymentDateStr}), تم استخدام التاريخ الحالي";
                    }
                }

                // التحقق من عدم تكرار البيانات
                $exists = StudentPaymentRecord::where([
                    'student_id' => $student->id,
                    'amount' => $amount,
                    'payment_date' => $paymentDate->toDateString(),
                ])->exists();

                if ($exists) {
                    \Log::info("Payment Record Skipped (Duplicate)", [
                        'student_id' => $student->id,
                        'amount' => $amount,
                        'payment_date' => $paymentDate->toDateString()
                    ]);
                    continue;
                }

                try {
                    $notesValue = $monthStr ? "الشهر: {$monthStr}" : null;
                    
                    $record = StudentPaymentRecord::create([
                        'student_id' => $student->id,
                        'month_id' => null,
                        'amount' => $amount,
                        'payment_date' => $paymentDate,
                        'payment_method' => 'نقدً',
                        'is_confirmed' => true,
                        'notes' => $notesValue,
                    ]);
                    
                    // جمع بيانات الدفع للمعالجة اللاحقة
                    $paymentData[] = [
                        'record_id' => $record->id,
                        'student_id' => $student->id,
                        'month' => $monthStr,
                    ];

                    // جمع الشهور الفريدة
                    if ($monthStr && !in_array($monthStr, $uniqueMonths)) {
                        $uniqueMonths[] = $monthStr;
                    }
                    
                    $imported++;
                } catch (\Exception $e) {
                    $errorMsg = "خطأ في المعالجة - {$e->getMessage()}";
                    $errors[] = "الصف {$index}: {$errorMsg}";
                    $failedRows[] = array_merge($row, [$errorMsg]);
                }
            }

            // حفظ الصفوف الفاشلة في الـ session
            if (!empty($failedRows)) {
                session(['payment_failed_rows' => $failedRows]);
            }

            // حفظ البيانات في session للمعالجة اللاحقة
            if (!empty($paymentData)) {
                session(['payment_data' => $paymentData, 'unique_months' => $uniqueMonths]);
                
                return redirect()->route('parent-portal.select-courses')
                    ->with([
                        'success' => "تم استيراد {$imported} سجل دفع",
                        'errors' => $errors,
                        'has_failed_rows' => !empty($failedRows),
                    ]);
            }

            return back()->with([
                'success' => "تم استيراد {$imported} سجل دفع",
                'errors' => $errors,
                'has_failed_rows' => !empty($failedRows),
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ في الملف: ' . $e->getMessage());
        }
    }

    /**
     * Import tasks/assignments from Excel
     */
    public function importTasks(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        try {
            $file = $request->file('file');
            $spreadsheet = IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray();

            $imported = 0;
            $errors = [];
            $failedRows = [];

            foreach ($data as $index => $row) {
                if ($index == 0) continue; // Skip header

                $studentCode = $row[0] ?? null;
                $title = $row[1] ?? null;
                $description = $row[2] ?? null;
                $taskType = $row[3] ?? 'واجب';
                $dueDate = $row[4] ?? null;
                $status = $row[5] ?? 'pending';
                $grade = $row[6] ?? null;
                $notes = $row[7] ?? null;

                if (!$studentCode || !$title) {
                    $errorMsg = "بيانات ناقصة";
                    $errors[] = "الصف {$index}: {$errorMsg}";
                    $failedRows[] = array_merge($row, [$errorMsg]);
                    continue;
                }

                $student = Student::where('student_code', $studentCode)->first();
                if (!$student) {
                    $errorMsg = "الطالب غير موجود ({$studentCode})";
                    $errors[] = "الصف {$index}: {$errorMsg}";
                    $failedRows[] = array_merge($row, [$errorMsg]);
                    continue;
                }

                try {
                    StudentTask::create([
                        'student_id' => $student->id,
                        'title' => $title,
                        'description' => $description,
                        'task_type' => $taskType,
                        'due_date' => $dueDate ? \Carbon\Carbon::createFromFormat('Y-m-d', $dueDate) : null,
                        'status' => $status,
                        'grade' => $grade,
                        'notes' => $notes,
                    ]);
                    $imported++;
                } catch (\Exception $e) {
                    $errorMsg = "خطأ في المعالجة - {$e->getMessage()}";
                    $errors[] = "الصف {$index}: {$errorMsg}";
                    $failedRows[] = array_merge($row, [$errorMsg]);
                }
            }

            // حفظ الصفوف الفاشلة في الـ session
            if (!empty($failedRows)) {
                session(['task_failed_rows' => $failedRows]);
            }

            return back()->with([
                'success' => "تم استيراد {$imported} واجب/امتحان",
                'errors' => $errors,
                'has_failed_rows' => !empty($failedRows),
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ في الملف: ' . $e->getMessage());
        }
    }

    /**
     * Export template for attendance
     */
    public function exportAttendanceTemplate()
    {
        $headers = ['الحالة (غائب/حاضر)', 'رقم هاتف الطالب', 'اليوم (YYYY-MM-DD)'];
        
        return response()->streamDownload(function() use ($headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            fputcsv($file, ['حاضر', '01012345678', '2025-12-16']);
            fputcsv($file, ['غائب', '01098765432', '2025-12-16']);
            fclose($file);
        }, 'attendance_template.csv', [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="attendance_template.csv"',
        ]);
    }

    /**
     * Export template for payments
     */
    public function exportPaymentTemplate()
    {
        $headers = ['رقم الطالب', 'المبلغ', 'الشهر المدفوع (MM/YYYY)', 'تاريخ الدفع (YYYY-MM-DD HH:MM AM/PM)'];
        
        return response()->streamDownload(function() use ($headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            fputcsv($file, ['01012345678', '500', '12/2025', '2025-12-16 01:12 AM']);
            fputcsv($file, ['01098765432', '750', '01/2026', '2025-12-16 03:30 PM']);
            fclose($file);
        }, 'payment_template.csv', [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="payment_template.csv"',
        ]);
    }

    /**
     * Export template for tasks
     */
    public function exportTaskTemplate()
    {
        $headers = ['رقم الطالب', 'العنوان', 'الوصف', 'النوع (واجب/امتحان)', 'تاريخ الاستحقاق (YYYY-MM-DD)', 'الحالة (pending/completed/overdue)', 'الدرجة', 'ملاحظات'];
        
        return response()->streamDownload(function() use ($headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            fclose($file);
        }, 'task_template.csv', [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="task_template.csv"',
        ]);
    }

    /**
     * View all attendance records
     */
    public function viewAttendance()
    {
        $records = StudentAttendance::with('student')
            ->orderBy('attendance_date', 'desc')
            ->paginate(50);

        return view('back.parent_portal.attendance', ['records' => $records]);
    }

    /**
     * View all payment records
     */
    public function viewPayments()
    {
        $records = StudentPaymentRecord::with(['student', 'month'])
            ->orderBy('payment_date', 'desc')
            ->paginate(50);

        return view('back.parent_portal.payments', ['records' => $records]);
    }

    /**
     * View all tasks
     */
    public function viewTasks()
    {
        $records = StudentTask::with('student')
            ->orderBy('due_date', 'desc')
            ->paginate(50);

        return view('back.parent_portal.tasks', ['records' => $records]);
    }

    /**
     * Show course selection page for months
     */
    public function selectCourses()
    {
        $paymentData = session('payment_data', []);
        $uniqueMonths = session('unique_months', []);
        
        if (empty($uniqueMonths)) {
            return redirect()->route('parent-portal.index')
                ->with('error', 'لا توجد بيانات للمعالجة');
        }

        // جلب الكورسات
        $courses = \App\Models\Month::select('id', 'name')
            ->orderBy('display_order')
            ->get();

        return view('back.parent_portal.select_courses', [
            'months' => $uniqueMonths,
            'courses' => $courses,
        ]);
    }

    /**
     * Export failed attendance rows
     */
    public function exportFailedAttendance()
    {
        $failedRows = session('attendance_failed_rows', []);
        
        if (empty($failedRows)) {
            return back()->with('error', 'لا توجد صفوف فاشلة للتصدير');
        }

        $headers = ['الحالة (غائب/حاضر)', 'رقم هاتف الطالب', 'اليوم (YYYY-MM-DD)', 'سبب الخطأ'];
        
        return response()->streamDownload(function() use ($headers, $failedRows) {
            $file = fopen('php://output', 'w');
            
            // إضافة BOM لدعم UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($file, $headers);
            
            foreach ($failedRows as $row) {
                fputcsv($file, $row);
            }
            
            fclose($file);
        }, 'attendance_errors_' . date('Y-m-d_H-i-s') . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="attendance_errors_' . date('Y-m-d_H-i-s') . '.csv"',
        ]);
    }

    /**
     * Export failed payment rows
     */
    public function exportFailedPayments()
    {
        $failedRows = session('payment_failed_rows', []);
        
        if (empty($failedRows)) {
            return back()->with('error', 'لا توجد صفوف فاشلة للتصدير');
        }

        $headers = ['رقم الطالب', 'المبلغ', 'الشهر المدفوع (MM/YYYY)', 'تاريخ الدفع (YYYY-MM-DD HH:MM AM/PM)', 'سبب الخطأ'];
        
        return response()->streamDownload(function() use ($headers, $failedRows) {
            $file = fopen('php://output', 'w');
            
            // إضافة BOM لدعم UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($file, $headers);
            
            foreach ($failedRows as $row) {
                fputcsv($file, $row);
            }
            
            fclose($file);
        }, 'payment_errors_' . date('Y-m-d_H-i-s') . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="payment_errors_' . date('Y-m-d_H-i-s') . '.csv"',
        ]);
    }

    /**
     * Export failed task rows
     */
    public function exportFailedTasks()
    {
        $failedRows = session('task_failed_rows', []);
        
        if (empty($failedRows)) {
            return back()->with('error', 'لا توجد صفوف فاشلة للتصدير');
        }

        $headers = ['رقم الطالب', 'العنوان', 'الوصف', 'النوع (واجب/امتحان)', 'تاريخ الاستحقاق (YYYY-MM-DD)', 'الحالة (pending/completed/overdue)', 'الدرجة', 'ملاحظات', 'سبب الخطأ'];
        
        return response()->streamDownload(function() use ($headers, $failedRows) {
            $file = fopen('php://output', 'w');
            
            // إضافة BOM لدعم UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($file, $headers);
            
            foreach ($failedRows as $row) {
                fputcsv($file, $row);
            }
            
            fclose($file);
        }, 'task_errors_' . date('Y-m-d_H-i-s') . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="task_errors_' . date('Y-m-d_H-i-s') . '.csv"',
        ]);
    }

    /**
     * Save course selections and create subscriptions
     */
    public function saveCourseSelections(Request $request)
    {
        $paymentData = session('payment_data', []);
        $uniqueMonths = session('unique_months', []);

        if (empty($paymentData)) {
            return redirect()->route('parent-portal.index')
                ->with('error', 'لا توجد بيانات للمعالجة');
        }

        // التحقق من الإدخال - قد تكون فارغة إذا لم يختر أي كورس
        $request->validate([
            'courses' => 'nullable|array',
            'month_labels' => 'required|array',
        ]);

        $courseSelections = $request->input('courses', []);
        $monthLabels = $request->input('month_labels', []);
        $monthToCourseMap = [];

        // بناء خريطة الشهر -> الكورسات (مصفوفة لكل شهر)
        foreach ($monthLabels as $index => $month) {
            $monthToCourseMap[$month] = $courseSelections[$index] ?? [];
        }

        $subscriptionCount = 0;
        $errors = [];

        // إنشاء subscriptions
        foreach ($paymentData as $payment) {
            $month = $payment['month'];
            $studentId = $payment['student_id'];
            
            if (isset($monthToCourseMap[$month]) && is_array($monthToCourseMap[$month]) && !empty($monthToCourseMap[$month])) {
                // إنشاء اشتراك لكل كورس تم اختياره للشهر
                foreach ($monthToCourseMap[$month] as $courseId) {
                    // التحقق من عدم وجود اشتراك سابق
                    $exists = \App\Models\StudentSubscriptions::where([
                        'student_id' => $studentId,
                        'month_id' => $courseId,
                    ])->exists();

                    if (!$exists) {
                        try {
                            // جلب بيانات الطالب
                            $student = \App\Models\Student::find($studentId);
                            
                            \App\Models\StudentSubscriptions::create([
                                'student_id' => $studentId,
                                'month_id' => $courseId,
                                'grade' => $student->grade ?? 'غير محدد',
                                'is_active' => true,
                            ]);
                            $subscriptionCount++;
                        } catch (\Exception $e) {
                            $errors[] = "خطأ في إنشاء اشتراك للطالب {$studentId} في الكورس {$courseId}: {$e->getMessage()}";
                            \Log::error('Subscription Creation Failed', [
                                'student_id' => $studentId,
                                'course_id' => $courseId,
                                'error' => $e->getMessage(),
                            ]);
                        }
                    }
                }
            }
        }

        // مسح البيانات من session
        session()->forget(['payment_data', 'unique_months']);

        return redirect()->route('parent-portal.view-payments')
            ->with([
                'success' => "تم إنشاء {$subscriptionCount} اشتراك",
                'errors' => $errors,
            ]);
    }
}
