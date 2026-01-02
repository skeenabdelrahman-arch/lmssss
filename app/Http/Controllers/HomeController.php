<?php

namespace App\Http\Controllers;

use App\Models\ExamAnswer;
use App\Models\ExamName;
use App\Models\ExamQuestion;
use App\Models\ExamResult;
use App\Models\Lecture;
use App\Models\Student;
use App\Models\StudentSubscriptions;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $data['students'] = Student::count();
        $data['taken_exams'] = ExamResult::whereHas('exam')->count(); // فقط الامتحانات الموجودة
        $data['lectures'] = Lecture::count();
        $data['public_exams'] = ExamName::where('public_access', 1)->count();
        $data['public_exams_list'] = ExamName::where('public_access', 1)->orderBy('created_at', 'desc')->take(5)->get();
        return view('home',$data);
    }


    public function admin_profile_show()
    {
        $admin = User::where('id',Auth::guard('web')->user()->id)->first();
        return view('back.admin.admin_profile',compact('admin'));
    }

    public function admin_profile_update(Request $request, $id)
    {
        $admin = User::find($id);
        $admin->name = $request->name;
        $admin->email = $request->email;
        if(isset($request->password))
        {
            $admin->password = Hash::make($request->password);
        }
        $admin->save();
        return redirect()->back()->with('success','تم تحديث بيانات الادمن بنجاح');
    }
    public function logoutAdmin()
    {
        Auth::guard('web')->logout();
        return redirect('/admin');
    }

    public function students_male()
    {
        // جلب البيانات الأساسية فقط لتحسين الأداء
        $maleStudents = Student::where('gender','ذكر')
            ->select('id', 'first_name', 'second_name', 'third_name', 'forth_name', 'grade', 'student_phone', 'image', 'password')
            ->get();
        return view('back.students.students_male',compact('maleStudents'));
    }

    /**
     * عرض جميع الطلاب في صفحة واحدة
     */
    public function students_all(Request $request)
    {
        // جلب الفلاتر من الطلب
        $selectedGrade = $request->input('grade', '');
        $selectedCity = $request->input('city', '');
        $selectedRegister = $request->input('register', '');
        
        // بناء الـ query
        $query = Student::select('id', 'first_name', 'second_name', 'third_name', 'forth_name', 'grade', 'student_phone', 'image', 'password', 'gender', 'city', 'register');
        
        // تطبيق فلتر الصف إذا تم اختيار واحد
        if (!empty($selectedGrade)) {
            $query->where('grade', $selectedGrade);
        }
        
        // تطبيق فلتر المحافظة إذا تم اختيار واحدة
        if (!empty($selectedCity)) {
            $query->where('city', $selectedCity);
        }
        
        // تطبيق فلتر نوع التسجيل إذا تم اختيار واحد
        if (!empty($selectedRegister)) {
            $query->where('register', $selectedRegister);
        }
        
        // جلب الطلاب
        $students = $query->orderBy('gender')
            ->orderBy('first_name')
            ->get();
        
        // جلب قائمة الصفوف الفريدة من قاعدة البيانات
        $grades = Student::select('grade')
            ->distinct()
            ->whereNotNull('grade')
            ->pluck('grade')
            ->sort()
            ->values();
        
        // جلب قائمة المحافظات الفريدة من قاعدة البيانات
        $cities = Student::select('city')
            ->distinct()
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->pluck('city')
            ->sort()
            ->values();
        
        // جلب قائمة أنواع التسجيل الفريدة
        $registerTypes = Student::select('register')
            ->distinct()
            ->whereNotNull('register')
            ->where('register', '!=', '')
            ->pluck('register')
            ->sort()
            ->values();
        
        return view('back.students.students_all', compact('students', 'grades', 'cities', 'registerTypes', 'selectedGrade', 'selectedCity', 'selectedRegister'));
    }

    public function edit_student($id)
    {
        $student = Student::findOrFail($id);
        return view('back.students.edit', compact('student'));
    }


    public function student_profile($id)
    {
        $student = Student::findOrFail($id);
        $months = StudentSubscriptions::where('student_id',$id)->with('month')->get();
        $exam_results = ExamResult::where('student_id',$id)->with('exam')->get();
        
        // تطبيع الصف الدراسي للطالب للتطابق مع الصفوف في جدول الأشهر
        $normalizedGrade = $this->normalizeGradeForQuery($student->grade);
        
        // جلب جميع الأشهر المتاحة للطالب (بناءً على صفه)
        // ترتيب الأشهر بناءً على ترتيب الصفوف في الإعدادات العامة
        $availableMonthsQuery = \App\Models\Month::query();
        
        // إذا كان هناك صف محدد، فلترة حسب الصف
        if (!empty($normalizedGrade)) {
            $availableMonthsQuery->where('grade', $normalizedGrade);
        }
        
        $signupGrades = signup_grades();
        if (!empty($signupGrades)) {
            $caseStatement = "CASE grade ";
            foreach ($signupGrades as $index => $grade) {
                $order = $index + 1;
                $caseStatement .= "WHEN '" . addslashes($grade['value']) . "' THEN {$order} ";
            }
            $caseStatement .= "ELSE " . (count($signupGrades) + 1) . " END";
            $availableMonthsQuery->orderByRaw($caseStatement);
        } else {
            $availableMonthsQuery->orderBy('grade');
        }
        $availableMonths = $availableMonthsQuery->orderBy('name')->get();
        
        // جلب IDs الأشهر المشترك فيها
        $subscribedMonthIds = $months->pluck('month_id')->toArray();
        
        return view('back.students.student_profile',compact('student','months','exam_results', 'availableMonths', 'subscribedMonthIds'));
    }
    
    /**
     * تطبيع الصف الدراسي للتطابق مع الصفوف المحددة في الإعدادات
     */
    private function normalizeGradeForQuery($grade)
    {
        if (empty($grade)) {
            return null;
        }
        
        // إزالة المسافات الزائدة والفراغات
        $grade = trim($grade);
        $grade = preg_replace('/\s+/', ' ', $grade); // استبدال المسافات المتعددة بمسافة واحدة
        
        // الحصول على الصفوف المحددة في الإعدادات
        $signupGrades = signup_grades();
        
        if (empty($signupGrades)) {
            return $grade;
        }
        
        // البحث عن تطابق دقيق أولاً
        foreach ($signupGrades as $gradeOption) {
            $value = trim($gradeOption['value'] ?? '');
            $label = trim($gradeOption['label'] ?? '');
            
            // تطابق دقيق
            if (strcasecmp($grade, $value) === 0 || strcasecmp($grade, $label) === 0) {
                return $value; // إرجاع القيمة من الإعدادات
            }
            
            // تطابق مرن (حساسية حالة)
            if (mb_strtolower($grade, 'UTF-8') === mb_strtolower($value, 'UTF-8') || 
                mb_strtolower($grade, 'UTF-8') === mb_strtolower($label, 'UTF-8')) {
                return $value;
            }
        }
        
        // إذا لم يوجد تطابق، إرجاع القيمة كما هي
        return $grade;
    }

    /**
     * Add student to a course (month)
     */
    public function addStudentToCourse(Request $request, $student_id)
    {
        $request->validate([
            'month_id' => 'required|exists:months,id',
        ]);

        $student = Student::findOrFail($student_id);
        $month = \App\Models\Month::findOrFail($request->month_id);

        // تطبيع الصف الدراسي للطالب والشهر للمقارنة
        $normalizedStudentGrade = $this->normalizeGradeForQuery($student->grade);
        $normalizedMonthGrade = $this->normalizeGradeForQuery($month->grade);

        // التحقق من أن الشهر مناسب لصف الطالب
        if ($normalizedMonthGrade !== $normalizedStudentGrade) {
            return redirect()->back()->with('error', 'هذا الكورس غير مناسب لصف الطالب');
        }

        // التحقق من وجود اشتراك سابق
        $subscription = StudentSubscriptions::where('student_id', $student_id)
            ->where('month_id', $request->month_id)
            ->first();

        if ($subscription) {
            // تحديث الاشتراك الموجود
            $wasActive = $subscription->is_active;
            $subscription->is_active = 1;
            $subscription->save();
            
            // إرسال إشعار إذا تم التفعيل الآن ولم يكن مفعل من قبل
            if ($wasActive != 1) {
                try {
                    $subscription->refresh(); // تحديث الـ model لضمان تحميل العلاقات
                    \App\Services\NotificationService::notifyCourseActivated($subscription);
                } catch (\Exception $e) {
                    \Log::error('Error sending course activation notification', [
                        'subscription_id' => $subscription->id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }
            
            return redirect()->back()->with('success', 'تم تفعيل اشتراك الطالب في الكورس بنجاح');
        } else {
            // إنشاء اشتراك جديد
            $newSubscription = new StudentSubscriptions();
            $newSubscription->student_id = $student_id;
            $newSubscription->month_id = $request->month_id;
            $newSubscription->grade = $normalizedStudentGrade ?: $student->grade; // استخدام القيمة المطبعة
            $newSubscription->is_active = 1;
            $newSubscription->save();
            
            // إرسال إشعار للطالب عند تفعيل الكورس
            try {
                $newSubscription->refresh(); // تحديث الـ model لضمان تحميل العلاقات
                \App\Services\NotificationService::notifyCourseActivated($newSubscription);
            } catch (\Exception $e) {
                \Log::error('Error sending course activation notification', [
                    'subscription_id' => $newSubscription->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
            
            return redirect()->back()->with('success', 'تم إضافة الطالب إلى الكورس بنجاح');
        }
    }

    /**
     * Remove student from a course (month)
     */
    public function removeStudentFromCourse($student_id, $subscription_id)
    {
        $subscription = StudentSubscriptions::findOrFail($subscription_id);
        
        // التحقق من أن الاشتراك يخص هذا الطالب
        if ($subscription->student_id != $student_id) {
            return redirect()->back()->with('error', 'خطأ في البيانات');
        }

        // إلغاء تفعيل الاشتراك بدلاً من حذفه
        $subscription->is_active = 0;
        $subscription->save();

        return redirect()->back()->with('success', 'تم إلغاء تفعيل اشتراك الطالب في الكورس بنجاح');
    }

       public function resetDevice($id)
    {
        $student = Student::findorfail($id);
        
        // التحقق من صلاحية إعادة تعيين الجهاز (الأدمن فقط)
        $this->authorize('update', $student);
        
        $student->session_id = null;
        $student->save();
        Auth::guard('student')->logout();
        return redirect()->back()->with('success','تم تغير جهاز الطالب ');

    }


    public function deleteAllMaleStudents(Request $request)
    {
        // حماية إضافية: يتطلب تأكيد صريح
        if (!$request->has('confirm') || $request->confirm !== 'DELETE_ALL_MALE_STUDENTS') {
            return redirect()->back()->with('error', 'يجب تأكيد العملية. هذه العملية خطيرة جداً!');
        }
        
        // استخدام soft delete بدلاً من truncate
        $count = Student::where('gender','ذكر')->delete();
        return redirect()->back()->with('success', "تم حذف {$count} طالب (يمكن استعادتهم من البيانات المحذوفة)");
    }

    public function delete_student($id)
    {
        $student = Student::findorfail($id);
        
        // التحقق من صلاحية الحذف (الأدمن فقط)
        $this->authorize('delete', $student);
        
        $student->delete();
        return redirect()->back()->with('success','تم حذف الطالب');
    }

    public function students_female()
    {
        // جلب البيانات الأساسية فقط لتحسين الأداء
        $femaleStudents = Student::where('gender','انثي')
            ->select('id', 'first_name', 'second_name', 'third_name', 'forth_name', 'grade', 'student_phone', 'image', 'password')
            ->get();
        return view('back.students.students_female',compact('femaleStudents'));
    }

    public function deleteAllFemaleStudents(Request $request)
    {
        // حماية إضافية: يتطلب تأكيد صريح
        if (!$request->has('confirm') || $request->confirm !== 'DELETE_ALL_FEMALE_STUDENTS') {
            return redirect()->back()->with('error', 'يجب تأكيد العملية. هذه العملية خطيرة جداً!');
        }
        
        // استخدام soft delete بدلاً من truncate
        $count = Student::where('gender','انثي')->delete();
        return redirect()->back()->with('success', "تم حذف {$count} طالبة (يمكن استعادتهم من البيانات المحذوفة)");
    }



    public function Student_update(Request $request, $id)
    {
        $student = Student::findorfail($id);
        $student->first_name = $request->first_name;
        $student->second_name = $request->second_name;
        $student->third_name = $request->third_name;
        $student->forth_name = $request->forth_name;
        $student->student_phone = $request->student_phone;
        $student->parent_phone = $request->parent_phone;
        $student->city = $request->city;
        $student->gender = $request->gender;
        $student->grade= $request->grade;
        $student->register= $request->register;
        
        // تحديث كود الطالب وكلمة المرور فقط إذا تم إدخال قيمة جديدة
        if (!empty($request->student_code)) {
            $student->student_code = $request->student_code;
            $student->password = $request->student_code;
        }
        
        // اشتراك شامل في جميع الكورسات
        $student->has_all_access = $request->has('has_all_access') ? 1 : 0;
        if (!empty($request->file('image'))) {
            $file                      = $request->file('image');
            $ext                       =  $file->getClientOriginalExtension();
            $filename                  = strtolower( $student->id . Str::random(20) . '.'. $ext );
            $file->move('upload_files/',$filename);
            $student->image= $filename;
        }
        $student->save();
        return redirect()->route('student.edit', $id)->with('success','تم تعديل الطالب');
    }


    public function show_taken_exams(Request $request)
    {
        $q = trim($request->query('q', ''));

        $query = ExamResult::with(['exam', 'student'])
            ->whereHas('exam') // فقط النتائج التي لها امتحانات موجودة
            ->orderBy('created_at', 'desc');

        if ($q !== '') {
            $query->where(function($qr) use ($q) {
                // بحث في بيانات الطالب
                $qr->whereHas('student', function($s) use ($q) {
                    $s->where('student_phone', 'like', "%{$q}%")
                      ->orWhere('first_name', 'like', "%{$q}%")
                      ->orWhere('second_name', 'like', "%{$q}%")
                      ->orWhere('third_name', 'like', "%{$q}%")
                      ->orWhere('forth_name', 'like', "%{$q}%")
                      ->orWhereRaw("CONCAT(first_name, ' ', second_name, ' ', third_name, ' ', forth_name) LIKE ?", ["%{$q}%"]);
                });

                // بحث في عنوان الامتحان
                $qr->orWhereHas('exam', function($e) use ($q) {
                    $e->where('exam_title', 'like', "%{$q}%")
                      ->orWhere('exam_description', 'like', "%{$q}%");
                });
            });
        }

        $taken_exams = $query->get();

        // حساب وقت الجلوس لكل امتحان (يعمل مع القديمة والجديدة)
        foreach ($taken_exams as $examResult) {
            $duration = 0;
            $exam = $examResult->exam;
            
            // محاولة حساب الوقت من ExamAnswer (للامتحانات الجديدة)
            $firstAnswer = \App\Models\ExamAnswer::where('exam_id', $examResult->exam_id)
                ->where('student_id', $examResult->student_id)
                ->orderBy('created_at', 'asc')
                ->first();
            
            $lastAnswer = \App\Models\ExamAnswer::where('exam_id', $examResult->exam_id)
                ->where('student_id', $examResult->student_id)
                ->orderBy('updated_at', 'desc')
                ->first();
            
            if ($firstAnswer && $lastAnswer) {
                // استخدام وقت الإجابات (الأدق)
                $startTime = $firstAnswer->created_at;
                $endTime = $lastAnswer->updated_at ?? $lastAnswer->created_at ?? $examResult->created_at;
                $duration = $startTime->diffInSeconds($endTime);
            } 
            
            // للامتحانات القديمة: استخدام created_at و updated_at من ExamResult
            if ($duration == 0 && $examResult->created_at && $examResult->updated_at) {
                if ($examResult->updated_at->gt($examResult->created_at)) {
                    $duration = $examResult->created_at->diffInSeconds($examResult->updated_at);
                }
            }
            
            // إذا لم يكن هناك فرق، استخدم وقت الامتحان المحدد كتقدير
            if ($duration == 0 && $exam && isset($exam->exam_time) && $exam->exam_time > 0) {
                $duration = $exam->exam_time * 60; // تحويل الدقائق إلى ثواني
            }
            
            // تنسيق الوقت
            if ($duration > 0) {
                $examResult->time_spent_seconds = $duration;
                $examResult->time_spent_formatted = $this->formatDuration($duration);
            } else {
                $examResult->time_spent_seconds = 0;
                $examResult->time_spent_formatted = 'غير متاح';
            }
        }

        // تجميع النتائج حسب الامتحان
        $exams_grouped = $taken_exams->groupBy('exam_id');

        return view('back.exam.taken_exams', compact('taken_exams', 'exams_grouped', 'q'));
    }
    
    /**
     * تنسيق المدة الزمنية
     */
    private function formatDuration($seconds)
    {
        if ($seconds < 60) {
            return $seconds . ' ثانية';
        } elseif ($seconds < 3600) {
            $minutes = floor($seconds / 60);
            $remainingSeconds = $seconds % 60;
            if ($remainingSeconds > 0) {
                return $minutes . ' دقيقة ' . $remainingSeconds . ' ثانية';
            }
            return $minutes . ' دقيقة';
        } else {
            $hours = floor($seconds / 3600);
            $remainingMinutes = floor(($seconds % 3600) / 60);
            if ($remainingMinutes > 0) {
                return $hours . ' ساعة ' . $remainingMinutes . ' دقيقة';
            }
            return $hours . ' ساعة';
        }
    }

    /**
     * تصدير درجات الامتحان إلى Excel
     */
    public function exportExamDegreesToExcel(Request $request)
    {
        $exam_id = $request->get('exam_id');
        
        // جلب نتائج الامتحان المحدد
        $exam_results = ExamResult::where('exam_id', $exam_id)
            ->with(['exam', 'student'])
            ->orderBy('created_at', 'asc')
            ->get();
        
        if ($exam_results->isEmpty()) {
            return redirect()->back()->with('error', 'لا توجد نتائج لهذا الامتحان');
        }
        
        // حساب الدرجة الكلية للامتحان
        $exam = $exam_results->first()->exam;
        $totalDegree = 0;
        foreach ($exam->questions as $question) {
            $totalDegree += (float)$question->Q_degree;
        }
        
        // إنشاء ملف Excel
        $fileName = 'درجات_' . $exam->exam_title . '_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        // استخدام PhpSpreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // تعيين المرجع RTL (من اليمين لليسار)
        $sheet->setRightToLeft(true);
        
        // إضافة الرؤوس
        $sheet->setCellValue('A1', 'اسم الامتحان: ' . $exam->exam_title);
        $sheet->setCellValue('A2', 'التاريخ: ' . now()->format('Y-m-d H:i'));
        $sheet->setCellValue('A3', 'الدرجة الكلية: ' . $totalDegree);
        
        // تنسيق رؤوس الجدول
        $sheet->setCellValue('A5', '#');
        $sheet->setCellValue('B5', 'اسم الطالب');
        $sheet->setCellValue('C5', 'رقم الموبايل');
        $sheet->setCellValue('D5', 'الدرجة المحصول عليها');
        $sheet->setCellValue('E5', 'الدرجة الكلية');
        $sheet->setCellValue('F5', 'النسبة المئوية');
        $sheet->setCellValue('G5', 'التاريخ');
        
        // تنسيق الرؤوس
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '7424a9']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
        ];
        
        for ($col = 'A'; $col <= 'G'; $col++) {
            $sheet->getStyle($col . '5')->applyFromArray($headerStyle);
        }
        
        // إضافة البيانات
        $row = 6;
        foreach ($exam_results as $index => $result) {
            $percentage = $totalDegree > 0 ? round(($result->degree / $totalDegree) * 100, 2) : 0;
            $studentName = trim($result->student->first_name . ' ' . $result->student->second_name . ' ' . 
                           $result->student->third_name . ' ' . $result->student->forth_name);
            
            // تنسيق رقم الموبايل ليبدأ بصفر
            $phone = $result->student->student_phone ?? '-';
            if ($phone !== '-' && !str_starts_with($phone, '0')) {
                $phone = '0' . $phone;
            }
            
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $studentName);
            $sheet->setCellValue('C' . $row, $phone);
            $sheet->setCellValue('D' . $row, $result->degree);
            $sheet->setCellValue('E' . $row, $totalDegree);
            $sheet->setCellValue('F' . $row, $percentage . '%');
            $sheet->setCellValue('G' . $row, $result->created_at->format('Y-m-d H:i'));
            
            // تنسيق الصفوف
            $dataStyle = [
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
            ];
            
            for ($col = 'A'; $col <= 'G'; $col++) {
                $sheet->getStyle($col . $row)->applyFromArray($dataStyle);
            }
            
            $row++;
        }
        
        // ضبط عرض الأعمدة
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(18);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(18);
        
        // الحفظ والتحميل
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        
        $writer->save('php://output');
        exit();
    }

    // public function deleteAllTakenExams()
    // {
    //     $exams = ExamResult::all();
    //     foreach ($exams as $exam) {
    //         ExamAnswer::where('exam_id',$exam->exam_id)->delete();
    //     }
    //     ExamResult::truncate();

    //     return redirect()->back()->with('success','تم حذف الامتحانات الممتحنة بنجاح');
    // }

    public function deleteTakenExam($id)
    {
        $exam = ExamResult::find($id);
        
        // التحقق من صلاحية الحذف (الأدمن فقط)
        $this->authorize('delete', $exam);
        
       $exam_id = $exam->exam_id ;
       $student_id = $exam->student_id ;
       ExamAnswer::where('student_id',$student_id)->where('exam_id',$exam_id)->delete();
       
        $exam->delete();
        return redirect()->back()->with('success','تم حذف الامتحان بنجاح');
    }
    
    /**
     * حذف جميع نتائج امتحان معين
     */
    public function deleteExamResults(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|exists:exam_names,id'
        ]);
        
        $exam_id = $request->exam_id;
        $exam = ExamName::findOrFail($exam_id);
        
        // حذف جميع الإجابات المرتبطة بالامتحان
        ExamAnswer::where('exam_id', $exam_id)->delete();
        
        // حذف جميع النتائج المرتبطة بالامتحان
        $count = ExamResult::where('exam_id', $exam_id)->delete();
        
        return redirect()->back()->with('success', "تم حذف {$count} نتيجة للامتحان: {$exam->exam_title}");
    }


    public function showExam(Request $request)
    {
        $exam_id = $request->exam_id;
        $student_id = $request->student_id;
        $data['student_id'] = $student_id;
        $studentName = Student::where('id',$student_id)->first();
        $data['f_name'] =$studentName->first_name;
        $data['s_name'] =$studentName->second_name;
        $data['t_name'] =$studentName->third_name;

        $data['exam_name'] = ExamName::findorfail($exam_id);
        $data['exam_questions'] = ExamQuestion::where('exam_id',$exam_id)->get();
        $data['exam_result'] = ExamResult::where('exam_id',$exam_id)->where('student_id',$student_id)->first();
        
        // التحقق من صلاحية الوصول لنتيجة الامتحان (الأدمن فقط)
        if ($data['exam_result']) {
            $this->authorize('view', $data['exam_result']);
        }

        $score = ExamQuestion::where('exam_id',$exam_id)->get();
        $exam_degree = 0;
        foreach ($score as $question) {
            $exam_degree += $question->Q_degree;
        }
            $data['exam_degree'] = $exam_degree;
        return view('back.exam.exam_review',$data);
    }

    public function exam_degree_update(Request $request, $id)
    {
        $exam_result = ExamResult::findOrFail($id);
        
        // التحقق من صلاحية التحديث (الأدمن فقط)
        $this->authorize('update', $exam_result);
        
        $request->validate([
            'degree' => 'required|numeric|min:0',
            'show_degree' => 'nullable'
        ]);
        
        $show_degree_before = $exam_result->show_degree;
        $exam_result->degree = $request->degree;
        $exam_result->show_degree = isset($request->show_degree) ? 1 : 0;
        $exam_result->save();

        // Send notification if grade is now visible and wasn't before
        if ($exam_result->show_degree && !$show_degree_before) {
            \App\Services\NotificationService::notifyExamGradeVisible($exam_result);
        }

        return redirect()->back()->with('success','تم تحديث الدرجة بنجاح');
    }
    
    public function logOutAllStudents()
    {
       Student::query()->update(['session_id' => null]);
        return redirect()->back()->with('success','تم خروج جميع الاجهزة ');
        
    }
    
    public function showAllDegrees(Request $request)
    {
        $exam_id = $request->exam_id;
        
        // Get exam results that weren't marked as visible before
        $exam_results = ExamResult::where('exam_id', $exam_id)
            ->where('show_degree', 0)
            ->get();
        
        // Update all exam results to show degree
        ExamResult::where('exam_id', $exam_id)->update(['show_degree'=>1, 'is_marked'=>1]);

        // Send notifications for newly visible grades
        foreach ($exam_results as $exam_result) {
            \App\Services\NotificationService::notifyExamGradeVisible($exam_result);
        }

        return redirect()->back()->with('success','تم تحديث الدرجات بنجاح');
    }

    // Add Student (Admin)
    public function add_student()
    {
        // التحقق من الصلاحية
        if (!Auth::guard('web')->user()->hasPermission('add_student')) {
            abort(403, 'ليس لديك صلاحية لإضافة طالب');
        }
        return view('back.students.add_student');
    }

    public function store_student(Request $request)
    {
        // التحقق من الصلاحية
        if (!Auth::guard('web')->user()->hasPermission('add_student')) {
            abort(403, 'ليس لديك صلاحية لإضافة طالب');
        }

        $request->validate([
            'student_phone' => 'required|unique:students,student_phone',
            'parent_phone' => 'nullable|different:student_phone',
            'password' => 'required|string|min:8|confirmed',
            'email' => 'nullable|email|unique:students,email',
        ]);

        $save = new Student();
        $save->first_name = $request->first_name ?? '';
        $save->second_name = $request->second_name ?? '';
        $save->third_name = $request->third_name ?? '';
        $save->forth_name = $request->forth_name ?? '';
        
        // إنشاء بريد افتراضي إذا لم يُدخل
        if (empty($request->email)) {
            $save->email = 'student_' . time() . '_' . rand(1000, 9999) . '@platform.local';
        } else {
            $save->email = $request->email;
        }
        
        $save->student_phone = $request->student_phone;
        $save->parent_phone = $request->parent_phone;
        $save->city = $request->city;
        $save->gender = $request->gender;
        $save->grade = $request->grade;
        
        // تحديد مصدر التسجيل (online, excel_import, admin)
        $registration_source = $request->registration_source ?? 'admin';
        // تحويل "excel" إلى "excel_import" للتوافق
        if ($registration_source === 'excel') {
            $registration_source = 'excel_import';
        }
        $save->register = $registration_source;
        
        // جعل كود الطالب هو نفس كلمة المرور
        $save->student_code = $request->password;
        $save->password = $save->student_code;
        
        // حفظ الطالب أولاً للحصول على ID
        $save->save();
        
        // ثم معالجة الصورة إذا كانت موجودة
        if (!empty($request->file('image'))) {
            $file = $request->file('image');
            $ext = $file->getClientOriginalExtension();
            $filename = strtolower($save->id . Str::random(20) . '.' . $ext);
            $file->move('upload_files/', $filename);
            $save->image = $filename;
            $save->save(); // حفظ مرة أخرى بعد إضافة الصورة
        }

        return redirect()->route('admin.student.create')->with('success', 'تم إضافة الطالب بنجاح');
    }

    /**
     * شاشة تفعيل الاشتراك الشامل لمجموعة من الطلبة
     */
    public function showAllAccessForm()
    {
        // يمكن لاحقًا إضافة صلاحيات أدق لو حابب
        $grades = signup_grades();

        // جلب جميع الطلاب الذين لديهم اشتراك شامل
        // استبعاد الطلاب الذين تم استيرادهم عبر Excel ولم يتم تفعيل اشتراكات لهم
        $allAccessStudents = Student::where('has_all_access', true)
            ->where(function($q) {
                $q->whereNull('register')
                  ->orWhere('register', '!=', 'excel_import')
                  ->orWhereHas('subscriptions', function($sq) {
                      $sq->where('is_active', 1);
                  });
            })
            ->select('id', 'first_name', 'second_name', 'third_name', 'forth_name', 'student_phone', 'student_code', 'grade')
            ->orderBy('grade')
            ->orderBy('first_name')
            ->get();

        return view('back.students.all_access', compact('grades', 'allAccessStudents'));
    }

    /**
     * تفعيل اشتراك شامل لمجموعة طلبة من ملف Excel (برقم الموبايل)
     */
    public function setAllAccessFromExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        $filePath = $request->file('file')->getRealPath();

        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();

        $updated = 0;
        $notFound = 0;
        $isFirstRow = true;
        $skippedImported = 0;
        $includeImported = $request->has('include_imported') && $request->input('include_imported') == '1';

        foreach ($sheet->getRowIterator() as $rowIndex => $row) {
            // تخطي الصف الأول (الرؤوس)
            if ($isFirstRow) {
                $isFirstRow = false;
                continue;
            }

            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            $data = [];
            foreach ($cellIterator as $cell) {
                $data[] = trim((string)$cell->getValue());
            }

            // تخطي الصفوف الفارغة تماماً
            if (empty(array_filter($data))) {
                continue;
            }

            // الحصول على رقم التليفون من العمود الثالث (index 2)
            $phone = $data[2] ?? null;
            
            if (!$phone || trim($phone) === '') {
                $notFound++;
                continue;
            }

            $phone = trim($phone);
            $student = Student::where('student_phone', $phone)->first();
            if ($student) {
                // إذا لم يُسمح بتضمين الطلاب المستوردين، تجاهلهم
                if (!$includeImported && isset($student->register) && $student->register === 'excel_import') {
                    $skippedImported++;
                } else {
                    $student->has_all_access = 1;
                    $student->save();
                    $updated++;
                }
            } else {
                $notFound++;
            }
        }

        if ($updated > 0) {
            $msg = "✓ تم تفعيل اشتراك شامل لـ {$updated} طالب من الملف.";
            if ($notFound > 0) {
                $msg .= " {$notFound} سجل لم يتم العثور على رقم الموبايل المطابق له.";
            }
            if ($skippedImported > 0) {
                $msg .= " ({$skippedImported} طالب تم تجاهلهم لأنهم أضيفوا عبر استيراد Excel).";
            }
            return redirect()->back()->with('success', $msg);
        } else {
            return redirect()->back()->with('error', "لم يتم تفعيل أي طالب. تأكد من أن الملف يحتوي على أرقام موبايل صحيحة في العمود الثالث.");
        }
    }

    /**
     * تفعيل اشتراك شامل لمجموعة طلبة بإدخال أرقامهم / أكوادهم يدويًا
     */
    public function setAllAccessFromIds(Request $request)
    {
        $request->validate([
            'identifiers' => 'required|string',
            'identifier_type' => 'required|in:id,phone,code',
        ]);

        // تقسيم النص إلى أسطر وإزالة المسافات والأسطر الفارغة
        $identifiersText = $request->identifiers;
        $lines = preg_split('/[\r\n]+/', $identifiersText);
        $lines = array_filter(array_map('trim', $lines), function($line) {
            return $line !== '' && $line !== null;
        });

        if (empty($lines)) {
            return redirect()->back()->with('error', 'لم تُدخل أي بيانات في قائمة الطلاب.');
        }

        $updated = 0;
        $notFound = 0;
        $errors = [];

        foreach ($lines as $value) {
            if (trim($value) === '') {
                continue;
            }

            $value = trim($value);
            $query = Student::query();

            if ($request->identifier_type === 'id') {
                // تحقق من أن القيمة رقمية
                if (!is_numeric($value)) {
                    $errors[] = "القيمة '$value' ليست رقم صحيح";
                    $notFound++;
                    continue;
                }
                $query->where('id', (int)$value);
            } elseif ($request->identifier_type === 'phone') {
                $query->where('student_phone', $value);
            } else { // code
                $query->where('student_code', $value);
            }

            $student = $query->first();
            if ($student) {
                // تحقق أنه لم يتم تفعيله مسبقاً
                if (!$student->has_all_access) {
                    $student->has_all_access = 1;
                    $student->save();
                }
                $updated++;
            } else {
                $notFound++;
            }
        }

        if ($updated > 0) {
            $msg = "✓ تم تفعيل اشتراك شامل لـ {$updated} طالب من القائمة.";
            if ($notFound > 0) {
                $msg .= " {$notFound} من المدخلات لم يتم العثور عليها.";
            }
            return redirect()->back()->with('success', $msg);
        } else {
            return redirect()->back()->with('error', "لم يتم تفعيل أي طالب. تأكد من صحة البيانات المُدخلة.");
        }
    }

    /**
     * بحث سريع عن الطلاب بالاسم / رقم الموبايل / كود الطالب (لصفحة الاشتراك الشامل)
     */
    public function searchStudents(Request $request)
    {
        $q = trim($request->get('q', ''));

        if ($q === '') {
            return response()->json(['students' => []]);
        }

        $students = Student::query()
            ->where(function ($query) use ($q) {
                $query->where('student_phone', 'like', "%{$q}%")
                    ->orWhere('student_code', 'like', "%{$q}%")
                    ->orWhere('first_name', 'like', "%{$q}%")
                    ->orWhere('second_name', 'like', "%{$q}%")
                    ->orWhere('third_name', 'like', "%{$q}%")
                    ->orWhere('forth_name', 'like', "%{$q}%")
                    ->orWhereRaw("CONCAT(first_name, ' ', second_name, ' ', third_name, ' ', forth_name) LIKE ?", ["%{$q}%"]);
            })
            ->select('id', 'first_name', 'second_name', 'third_name', 'forth_name', 'student_phone', 'student_code', 'grade')
            ->orderBy('first_name')
            ->limit(20)
            ->get()
            ->map(function ($student) {
                return [
                    'id' => $student->id,
                    'name' => trim($student->first_name . ' ' . $student->second_name . ' ' . $student->third_name . ' ' . $student->forth_name),
                    'phone' => $student->student_phone,
                    'code' => $student->student_code,
                    'grade' => $student->grade,
                ];
            });

        return response()->json(['students' => $students]);
    }

    /**
     * إلغاء الاشتراك الشامل لطالب محدد
     */
    public function removeAllAccess($id)
    {
        $student = Student::findOrFail($id);
        $student->has_all_access = 0;
        $student->save();

        return redirect()->route('admin.students.all_access.form')
            ->with('success', 'تم إلغاء الاشتراك الشامل للطالب بنجاح');
    }

}
