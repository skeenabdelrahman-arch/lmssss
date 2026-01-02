<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Month;
use App\Models\StudentSubscriptions;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ExcelImportController extends Controller
{
    /**
     * التحقق من نوع حقول الهاتف وتغييرها إلى bigInteger إذا لزم الأمر
     */
    private function ensureBigIntegerColumns()
    {
        static $columnsChecked = false;
        
        // تجنب التحقق المتكرر في نفس الطلب
        if ($columnsChecked) {
            return;
        }
        
        try {
            $columns = DB::select("SHOW COLUMNS FROM students WHERE Field IN ('student_phone', 'parent_phone')");
            
            foreach ($columns as $column) {
                $field = $column->Field;
                $type = strtolower($column->Type);
                
                // إذا كان الحقل من نوع integer (وليس bigint)، قم بتغييره
                if (preg_match('/^int(\(\d+\))?$/', $type) && strpos($type, 'bigint') === false) {
                    try {
                        DB::statement("ALTER TABLE students MODIFY `{$field}` BIGINT NULL");
                        \Log::info("Changed {$field} column from integer to bigInteger");
                    } catch (\Exception $e) {
                        \Log::warning("Could not change {$field} column: " . $e->getMessage());
                    }
                }
            }
            
            $columnsChecked = true;
        } catch (\Exception $e) {
            \Log::warning('Could not check/update phone columns: ' . $e->getMessage());
        }
    }

    /**
     * عرض صفحة استيراد الطلاب
     */
    public function showImportStudents()
    {
        return view('back.excel.import_students');
    }

    /**
     * استيراد الطلاب من ملف Excel
     * التنسيق المتوقع:
     * A: الاسم (أو الاسم الأول)
     * B: رقم الهاتف
     * C: رقم ولي الأمر
     * D: الصف الدراسي
     * E: الباسورد
     */
    public function importStudents(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls|max:10240', // 10MB max
            'grade' => 'required|string', // الصف الدراسي مطلوب
        ]);

        try {
            // التحقق من نوع الحقول وتغييرها إذا لزم الأمر
            $this->ensureBigIntegerColumns();
            
            $file = $request->file('excel_file');
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            
            $rows = [];
            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            foreach ($sheet->getRowIterator(2) as $row) { // ابدأ من الصف الثاني (تخطى العناوين)
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                
                $rowData = [];
                foreach ($cellIterator as $cell) {
                    $rowData[] = $cell->getValue();
                }

                // التحقق من وجود البيانات الأساسية
                $name = trim($rowData[0] ?? '');
                $phone = trim($rowData[1] ?? '');
                $parentPhone = trim($rowData[2] ?? '');
                $password = trim($rowData[3] ?? ''); // الباسورد الآن في العمود D بدلاً من E
                
                // استخدام الصف الدراسي المختار من الصفحة
                $grade = $request->input('grade');
                // تطبيع الصف الدراسي - إزالة المسافات الزائدة وضمان التطابق مع الإعدادات
                $grade = $this->normalizeGrade($grade);

                // تخطي الصفوف الفارغة
                if (empty($name) && empty($phone)) {
                    continue;
                }

                // التحقق من البيانات المطلوبة
                if (empty($phone)) {
                    $errorCount++;
                    $errors[] = "الصف " . $row->getRowIndex() . ": رقم الهاتف مطلوب";
                    continue;
                }

                // تنظيف رقم الهاتف - إزالة أي أحرف غير رقمية
                $phoneCleaned = preg_replace('/[^0-9]/', '', (string)$phone);
                
                // التحقق من أن رقم الهاتف ليس فارغاً بعد التنظيف
                if (empty($phoneCleaned) || strlen($phoneCleaned) < 8) {
                    $errorCount++;
                    $errors[] = "الصف " . $row->getRowIndex() . ": رقم الهاتف غير صحيح أو قصير جداً (القيمة الأصلية: " . ($phone ?: 'فارغ') . ")";
                    continue;
                }
                
                // التحقق من طول الرقم (bigInteger يدعم حتى 19 رقم)
                if (strlen($phoneCleaned) > 19) {
                    $errorCount++;
                    $errors[] = "الصف " . $row->getRowIndex() . ": رقم الهاتف طويل جداً (يجب أن يكون أقل من 20 رقم)";
                    continue;
                }
                
                // استخدام الرقم (سيتم حفظه كـ bigInteger في قاعدة البيانات)
                $phone = $phoneCleaned;

                // تقسيم الاسم إلى أجزاء
                $nameParts = explode(' ', $name, 4);
                $firstName = $nameParts[0] ?? 'طالب';
                $secondName = $nameParts[1] ?? 'جديد';
                $thirdName = $nameParts[2] ?? '';
                $forthName = $nameParts[3] ?? '';

                // إنشاء email فريد
                $email = 'student_' . $phone . '@platform.local';
                $emailExists = Student::where('email', $email)->exists();
                if ($emailExists) {
                    $email = 'student_' . $phone . '_' . time() . '@platform.local';
                }

                // التحقق من وجود الطالب
                $student = Student::where('student_phone', $phone)->first();

                try {
                    if ($student) {
                    // تحديث بيانات الطالب
                    $student->first_name = $firstName;
                    $student->second_name = $secondName;
                    $student->third_name = $thirdName;
                    $student->forth_name = $forthName;
                    if (!empty($parentPhone)) {
                        $cleanParentPhone = preg_replace('/[^0-9]/', '', (string)$parentPhone);
                        if (!empty($cleanParentPhone) && strlen($cleanParentPhone) <= 19) {
                            $student->parent_phone = $cleanParentPhone;
                        }
                    }
                    // تطبيع الصف الدراسي قبل الحفظ
                    $normalizedGrade = $this->normalizeGrade($grade);
                    $student->grade = !empty($normalizedGrade) ? $normalizedGrade : $student->grade;
                    if (!empty($password)) {
                        $student->password = $password; // حفظ الباسورد كنص عادي (مثل باقي النظام)
                    }
                    $student->register = 'excel_import';
                    $student->save();
                } else {
                    // معالجة رقم ولي الأمر
                    $parentPhoneFinal = 0;
                    if (!empty($parentPhone)) {
                        $cleanParentPhone = preg_replace('/[^0-9]/', '', (string)$parentPhone);
                        if (!empty($cleanParentPhone) && strlen($cleanParentPhone) <= 19) {
                            // استخدام الرقم كـ string ليدعم الأرقام الكبيرة
                            $parentPhoneFinal = $cleanParentPhone;
                        }
                    }
                    
                    // إنشاء طالب جديد
                    $student = Student::create([
                        'first_name' => $firstName,
                        'second_name' => $secondName,
                        'third_name' => $thirdName,
                        'forth_name' => $forthName,
                        'email' => $email,
                        'student_phone' => $phone,
                        'parent_phone' => $parentPhoneFinal ?: 0,
                        'grade' => $this->normalizeGrade($grade) ?: 'غير محدد',
                        'city' => 'غير محدد',
                        'gender' => 'ذكر',
                        'register' => 'excel_import',
                        'password' => $password ?: Str::random(8), // إذا لم يكن هناك باسورد، أنشئ واحد عشوائي
                    ]);
                }

                    $successCount++;
                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "الصف " . $row->getRowIndex() . ": خطأ في قاعدة البيانات - " . $e->getMessage();
                    // لا نكسر الحلقة، نكمل الصفوف التالية
                    continue;
                }
            }


            $message = "تم استيراد {$successCount} طالب بنجاح";
            if ($errorCount > 0) {
                $message .= " مع {$errorCount} أخطاء";
            }

            return redirect()->back()->with([
                'success' => $message,
                'import_stats' => [
                    'success' => $successCount,
                    'errors' => $errorCount,
                    'error_details' => $errors
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ أثناء الاستيراد: ' . $e->getMessage());
        }
    }

    /**
     * عرض صفحة استيراد الاشتراكات
     */
    public function showImportSubscriptions()
    {
        $months = Month::all();
        return view('back.excel.import_subscriptions', compact('months'));
    }

    /**
     * استيراد الاشتراكات من ملف Excel
     * التنسيق المتوقع:
     * A: رقم الهاتف
     * B: اسم الشهر (أو ID الشهر)
     * C: الصف الدراسي (اختياري)
     */
    public function importSubscriptions(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls|max:10240',
            'month_id' => 'nullable|exists:months,id', // إذا كان الشهر محدد مسبقاً
        ]);

        try {
            // التحقق من نوع الحقول وتغييرها إذا لزم الأمر
            $this->ensureBigIntegerColumns();
            
            $file = $request->file('excel_file');
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            
            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            DB::beginTransaction();

            // الحصول على ID الشهر
            $monthId = $request->month_id;

            foreach ($sheet->getRowIterator(2) as $row) { // ابدأ من الصف الثاني
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                
                $rowData = [];
                foreach ($cellIterator as $cell) {
                    $rowData[] = $cell->getValue();
                }

                $phone = trim($rowData[0] ?? '');
                $monthIdentifier = trim($rowData[1] ?? ''); // اسم الشهر أو ID
                $grade = trim($rowData[2] ?? '');

                // تخطي الصفوف الفارغة
                if (empty($phone)) {
                    continue;
                }

                // تنظيف رقم الهاتف - إزالة أي أحرف غير رقمية
                $phoneCleaned = preg_replace('/[^0-9]/', '', (string)$phone);
                
                // التحقق من أن رقم الهاتف ليس فارغاً بعد التنظيف
                if (empty($phoneCleaned) || strlen($phoneCleaned) < 8) {
                    $errorCount++;
                    $errors[] = "الصف " . $row->getRowIndex() . ": رقم الهاتف غير صحيح أو قصير جداً (القيمة الأصلية: " . ($phone ?: 'فارغ') . ")";
                    continue;
                }
                
                // استخدام الرقم (يدعم الأرقام الكبيرة)
                if (strlen($phoneCleaned) > 19) {
                    $errorCount++;
                    $errors[] = "الصف " . $row->getRowIndex() . ": رقم الهاتف طويل جداً (يجب أن يكون أقل من 20 رقم)";
                    continue;
                }
                
                // استخدام الرقم (سيتم تحويله تلقائياً عند الحفظ)
                $phone = $phoneCleaned;

                // البحث عن الطالب
                $student = Student::where('student_phone', $phone)->first();
                
                if (!$student) {
                    $errorCount++;
                    $errors[] = "الصف " . $row->getRowIndex() . ": الطالب برقم {$phone} غير موجود";
                    continue;
                }

                // الحصول على الشهر
                $month = null;
                if ($monthId) {
                    // إذا كان الشهر محدد مسبقاً
                    $month = Month::find($monthId);
                } else {
                    // البحث عن الشهر بالاسم أو ID
                    if (is_numeric($monthIdentifier)) {
                        $month = Month::find($monthIdentifier);
                    } else {
                        $month = Month::where('name', $monthIdentifier)->first();
                    }
                }

                if (!$month) {
                    $errorCount++;
                    $errors[] = "الصف " . $row->getRowIndex() . ": الشهر ({$monthIdentifier}) غير موجود";
                    continue;
                }

                // استخدام الصف من الطالب إذا لم يكن محدد
                $finalGrade = $grade ?: $student->grade;

                // التحقق من وجود الاشتراك
                $subscription = StudentSubscriptions::where('student_id', $student->id)
                    ->where('month_id', $month->id)
                    ->first();

                if ($subscription) {
                    // تحديث الاشتراك
                    $subscription->grade = $finalGrade;
                    $subscription->is_active = 1;
                    $subscription->save();
                } else {
                    // إنشاء اشتراك جديد
                    StudentSubscriptions::create([
                        'student_id' => $student->id,
                        'month_id' => $month->id,
                        'first_name' => $student->first_name,
                        'second_name' => $student->second_name,
                        'third_name' => $student->third_name,
                        'forth_name' => $student->forth_name,
                        'grade' => $finalGrade,
                        'is_active' => 1,
                    ]);
                }

                $successCount++;
            }

            DB::commit();

            $message = "تم استيراد {$successCount} اشتراك بنجاح";
            if ($errorCount > 0) {
                $message .= " مع {$errorCount} أخطاء";
            }

            return redirect()->back()->with([
                'success' => $message,
                'import_stats' => [
                    'success' => $successCount,
                    'errors' => $errorCount,
                    'error_details' => $errors
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ أثناء الاستيراد: ' . $e->getMessage());
        }
    }
    
    /**
     * تطبيع الصف الدراسي للتطابق مع الصفوف المحددة في الإعدادات
     */
    private function normalizeGrade($grade)
    {
        if (empty($grade)) {
            return 'غير محدد';
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
        
        // إذا لم يوجد تطابق، إرجاع القيمة كما هي (قد تكون صحيحة)
        return $grade;
    }

    /**
     * عرض صفحة الطلاب المفعلين من Excel
     */
    public function showActivatedStudents(Request $request)
    {
        // جلب الطلاب المستوردين من Excel والذين لديهم اشتراكات مفعلة
        $query = Student::where('register', 'excel_import')
            ->whereHas('subscriptions', function($q) {
                $q->where('is_active', 1);
            })
            ->with(['subscriptions' => function($q) {
                $q->where('is_active', 1)->with('month');
            }]);

        // فلترة حسب الصف الدراسي
        if ($request->has('grade') && $request->grade) {
            $query->where('grade', $request->grade);
        }

        // فلترة حسب الشهر
        if ($request->has('month_id') && $request->month_id) {
            $query->whereHas('subscriptions', function($q) use ($request) {
                $q->where('month_id', $request->month_id)
                  ->where('is_active', 1);
            });
        }

        // البحث
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('student_phone', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('second_name', 'like', "%{$search}%")
                  ->orWhere('third_name', 'like', "%{$search}%")
                  ->orWhere('forth_name', 'like', "%{$search}%")
                  ->orWhereRaw("CONCAT(first_name, ' ', second_name, ' ', third_name, ' ', forth_name) LIKE ?", ["%{$search}%"]);
            });
        }

        $students = $query->orderBy('created_at', 'desc')->paginate(50);

        // جلب الطلاب المستوردين من Excel ولكن ليس لديهم اشتراكات مفعلة
        $notActivatedQuery = Student::where('register', 'excel_import')
            ->whereDoesntHave('subscriptions', function($q) {
                $q->where('is_active', 1);
            });

        // تطبيق نفس الفلاتر على الطلاب غير المفعلين
        if ($request->has('grade') && $request->grade) {
            $notActivatedQuery->where('grade', $request->grade);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $notActivatedQuery->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('student_phone', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('second_name', 'like', "%{$search}%")
                  ->orWhere('third_name', 'like', "%{$search}%")
                  ->orWhere('forth_name', 'like', "%{$search}%")
                  ->orWhereRaw("CONCAT(first_name, ' ', second_name, ' ', third_name, ' ', forth_name) LIKE ?", ["%{$search}%"]);
            });
        }

        $notActivatedStudents = $notActivatedQuery->orderBy('created_at', 'desc')->limit(50)->get();
        $notActivatedCount = $notActivatedQuery->count(); // إجمالي عدد الطلاب غير المفعلين

        // جلب الصفوف والأشهر للفلترة
        $grades = signup_grades();
        $months = Month::orderBy('name')->get();

        return view('back.excel.activated_students', compact('students', 'notActivatedStudents', 'notActivatedCount', 'grades', 'months'));
    }

    /**
     * استيراد الاشتراكات يدوياً من خلال إدخال أرقام الهواتف
     */
    public function importSubscriptionsManual(Request $request)
    {
        $request->validate([
            'phone_numbers' => 'required|string',
            'manual_month_id' => 'required|exists:months,id',
            'manual_grade' => 'nullable|string|max:255',
        ]);

        try {
            // التحقق من نوع الحقول وتغييرها إذا لزم الأمر
            $this->ensureBigIntegerColumns();
            
            $phoneNumbers = $request->phone_numbers;
            $monthId = $request->manual_month_id;
            $grade = $request->manual_grade;

            // تقسيم أرقام الهواتف (سطر جديد أو فواصل)
            $phoneLines = preg_split('/[\r\n,]+/', $phoneNumbers);
            
            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            DB::beginTransaction();

            // الحصول على الشهر
            $month = Month::find($monthId);
            if (!$month) {
                return redirect()->back()->with('error', 'الشهر المحدد غير موجود');
            }

            foreach ($phoneLines as $index => $phoneLine) {
                $phone = trim($phoneLine);
                
                // تخطي الأسطر الفارغة
                if (empty($phone)) {
                    continue;
                }

                // تنظيف رقم الهاتف - إزالة أي أحرف غير رقمية
                $phoneCleaned = preg_replace('/[^0-9]/', '', (string)$phone);
                
                // التحقق من أن رقم الهاتف ليس فارغاً بعد التنظيف
                if (empty($phoneCleaned) || strlen($phoneCleaned) < 8) {
                    $errorCount++;
                    $errors[] = "السطر " . ($index + 1) . ": رقم الهاتف غير صحيح أو قصير جداً (القيمة: " . ($phone ?: 'فارغ') . ")";
                    continue;
                }
                
                // التحقق من طول الرقم
                if (strlen($phoneCleaned) > 19) {
                    $errorCount++;
                    $errors[] = "السطر " . ($index + 1) . ": رقم الهاتف طويل جداً (يجب أن يكون أقل من 20 رقم)";
                    continue;
                }
                
                $phone = $phoneCleaned;

                // البحث عن الطالب
                $student = Student::where('student_phone', $phone)->first();
                
                if (!$student) {
                    $errorCount++;
                    $errors[] = "السطر " . ($index + 1) . ": الطالب برقم {$phone} غير موجود";
                    continue;
                }

                // استخدام الصف المحدد أو صف الطالب
                $finalGrade = $grade ?: $student->grade;

                // التحقق من وجود الاشتراك
                $subscription = StudentSubscriptions::where('student_id', $student->id)
                    ->where('month_id', $month->id)
                    ->first();

                if ($subscription) {
                    // تحديث الاشتراك
                    $subscription->grade = $finalGrade;
                    $subscription->is_active = 1;
                    $subscription->save();
                } else {
                    // إنشاء اشتراك جديد
                    StudentSubscriptions::create([
                        'student_id' => $student->id,
                        'month_id' => $month->id,
                        'first_name' => $student->first_name,
                        'second_name' => $student->second_name,
                        'third_name' => $student->third_name,
                        'forth_name' => $student->forth_name,
                        'grade' => $finalGrade,
                        'is_active' => 1,
                    ]);
                }

                $successCount++;
            }

            DB::commit();

            $message = "تم إضافة {$successCount} اشتراك بنجاح";
            if ($errorCount > 0) {
                $message .= " مع {$errorCount} أخطاء";
            }

            return redirect()->back()->with([
                'success' => $message,
                'import_stats' => [
                    'success' => $successCount,
                    'errors' => $errorCount,
                    'error_details' => $errors
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ أثناء الإضافة: ' . $e->getMessage());
        }
    }

    /**
     * عرض صفحة إلغاء تفعيل الاشتراكات جماعياً
     */
    public function showDeactivateSubscriptions()
    {
        return view('back.excel.deactivate_subscriptions');
    }

    /**
     * إلغاء تفعيل جميع الاشتراكات من ملف Excel
     */
    public function deactivateSubscriptions(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls|max:10240',
            'deactivation_reason' => 'required|string|max:500'
        ]);

        try {
            $file = $request->file('excel_file');
            $spreadsheet = IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            // إزالة العنوان
            array_shift($rows);

            $successCount = 0;
            $notFoundCount = 0;
            $errorCount = 0;
            $errors = [];
            
            // الحصول على سبب الإلغاء من الـ request
            $deactivationReason = trim($request->deactivation_reason);

            DB::beginTransaction();

            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2; // +2 لأن الصف الأول عنوان والـ index يبدأ من 0

                // تنظيف رقم الهاتف
                $phone = trim($row[0] ?? '');
                
                if (empty($phone)) {
                    continue; // تخطي الصفوف الفارغة
                }

                // البحث عن الطالب
                $student = Student::where('student_phone', $phone)->first();

                if (!$student) {
                    $notFoundCount++;
                    $errors[] = "صف {$rowNumber}: لم يتم العثور على طالب بالرقم {$phone}";
                    continue;
                }

                try {
                    // إلغاء تفعيل جميع الاشتراكات للطالب مع إضافة سبب الإلغاء
                    $deactivatedCount = StudentSubscriptions::where('student_id', $student->id)
                        ->update([
                            'is_active' => 0,
                            'deactivation_reason' => $deactivationReason
                        ]);

                    $successCount += $deactivatedCount;
                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "صف {$rowNumber}: خطأ في إلغاء تفعيل اشتراكات الطالب {$phone} - " . $e->getMessage();
                }
            }

            DB::commit();

            $message = "تم إلغاء تفعيل {$successCount} اشتراك";
            if ($notFoundCount > 0) {
                $message .= " ({$notFoundCount} طالب غير موجود)";
            }
            if ($errorCount > 0) {
                $message .= " مع {$errorCount} أخطاء";
            }

            return redirect()->back()->with([
                'success' => $message,
                'deactivate_stats' => [
                    'success' => $successCount,
                    'not_found' => $notFoundCount,
                    'errors' => $errorCount,
                    'error_details' => array_slice($errors, 0, 20) // عرض أول 20 خطأ فقط
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ أثناء إلغاء التفعيل: ' . $e->getMessage());
        }
    }

    /**
     * إلغاء تفعيل الاشتراكات يدوياً من خلال أرقام الهواتف
     */
    public function deactivateSubscriptionsManual(Request $request)
    {
        $request->validate([
            'phone_numbers' => 'required|string',
            'deactivation_reason' => 'required|string|max:500'
        ]);

        try {
            // تقسيم أرقام الهواتف (بفواصل أو أسطر جديدة)
            $phoneNumbers = preg_split('/[\s,;]+/', $request->phone_numbers, -1, PREG_SPLIT_NO_EMPTY);
            $phoneNumbers = array_map('trim', $phoneNumbers);
            $phoneNumbers = array_filter($phoneNumbers);

            if (empty($phoneNumbers)) {
                return redirect()->back()->with('error', 'لم يتم إدخال أي أرقام هواتف صحيحة');
            }

            $successCount = 0;
            $notFoundCount = 0;
            $errorCount = 0;
            $errors = [];
            
            // الحصول على سبب الإلغاء من الـ request
            $deactivationReason = trim($request->deactivation_reason);

            DB::beginTransaction();

            foreach ($phoneNumbers as $phone) {
                // البحث عن الطالب
                $student = Student::where('student_phone', $phone)->first();

                if (!$student) {
                    $notFoundCount++;
                    $errors[] = "لم يتم العثور على طالب بالرقم {$phone}";
                    continue;
                }

                try {
                    // إلغاء تفعيل جميع الاشتراكات للطالب مع إضافة سبب الإلغاء
                    $deactivatedCount = StudentSubscriptions::where('student_id', $student->id)
                        ->update([
                            'is_active' => 0,
                            'deactivation_reason' => $deactivationReason
                        ]);

                    $successCount += $deactivatedCount;
                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "خطأ في إلغاء تفعيل اشتراكات الطالب {$phone} - " . $e->getMessage();
                }
            }

            DB::commit();

            $message = "تم إلغاء تفعيل {$successCount} اشتراك";
            if ($notFoundCount > 0) {
                $message .= " ({$notFoundCount} طالب غير موجود)";
            }
            if ($errorCount > 0) {
                $message .= " مع {$errorCount} أخطاء";
            }

            return redirect()->back()->with([
                'success' => $message,
                'deactivate_stats' => [
                    'success' => $successCount,
                    'not_found' => $notFoundCount,
                    'errors' => $errorCount,
                    'error_details' => array_slice($errors, 0, 20)
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ أثناء إلغاء التفعيل: ' . $e->getMessage());
        }
    }

    /**
     * إعادة تفعيل جميع اشتراكات طالب واحد
     */
    public function reactivateStudent(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id'
        ]);

        try {
            DB::beginTransaction();

            // الحصول على جميع الاشتراكات الملغية لهذا الطالب
            $subscriptions = StudentSubscriptions::where('student_id', $request->student_id)
                ->where('is_active', 0)
                ->whereNotNull('deactivation_reason')
                ->get();

            if ($subscriptions->isEmpty()) {
                return redirect()->back()->with('error', 'لا توجد اشتراكات ملغية لهذا الطالب');
            }

            // إعادة تفعيل جميع الاشتراكات
            foreach ($subscriptions as $subscription) {
                $subscription->is_active = 1;
                $subscription->deactivation_reason = null;
                $subscription->save();
            }

            DB::commit();

            $student = Student::find($request->student_id);
            $studentName = trim($student->first_name . ' ' . $student->second_name . ' ' . $student->third_name . ' ' . $student->forth_name);

            return redirect()->back()->with('success', "تم إعادة تفعيل {$subscriptions->count()} اشتراك للطالب: {$studentName}");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ أثناء إعادة التفعيل: ' . $e->getMessage());
        }
    }
}


