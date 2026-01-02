<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Lecture;
use App\Models\ExamName;
use App\Models\Month;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        try {
            $query = $request->get('q', '');
            
            \Log::info('Search request received', ['query' => $query]);
            
            if (strlen($query) < 2) {
                return response()->json(['results' => []]);
            }
            
            $results = [];
            
            // Search Students - Use DB facade to avoid fillable issues
            try {
                // البحث بالاسم الكامل (جميع الأسماء معاً)
                $nameParts = array_filter(array_map('trim', explode(' ', trim($query))));
                
                $students = DB::table('students')
                    ->where(function($q) use ($query, $nameParts) {
                        // البحث بكود الطالب
                        $q->where('student_code', 'like', "%{$query}%");
                        
                        // البحث برقم التليفون
                        $q->orWhere('student_phone', 'like', "%{$query}%");
                        $q->orWhere('parent_phone', 'like', "%{$query}%");
                        
                        // البحث بالبريد الإلكتروني
                        $q->orWhere('email', 'like', "%{$query}%");
                        
                        // البحث بالاسم الكامل (جميع الأسماء معاً) - استخدام whereRaw
                        $q->orWhereRaw("CONCAT_WS(' ', first_name, second_name, third_name, forth_name) LIKE ?", ["%{$query}%"]);
                        
                        // البحث في كل حقل على حدة (للأسماء الفردية)
                        $q->orWhere('first_name', 'like', "%{$query}%")
                          ->orWhere('second_name', 'like', "%{$query}%")
                          ->orWhere('third_name', 'like', "%{$query}%")
                          ->orWhere('forth_name', 'like', "%{$query}%");
                        
                        // إذا كان الاسم ثنائي أو أكثر، البحث عن كل جزء من الاسم في أي حقل
                        if (count($nameParts) > 1) {
                            $q->orWhere(function($subQ) use ($nameParts) {
                                foreach ($nameParts as $part) {
                                    if (strlen($part) > 0) {
                                        $subQ->where(function($nameQ) use ($part) {
                                            $nameQ->where('first_name', 'like', "%{$part}%")
                                                  ->orWhere('second_name', 'like', "%{$part}%")
                                                  ->orWhere('third_name', 'like', "%{$part}%")
                                                  ->orWhere('forth_name', 'like', "%{$part}%");
                                        });
                                    }
                                }
                            });
                        }
                    })
                    ->take(5)
                    ->get();
                
                foreach ($students as $student) {
                    $name = trim(($student->first_name ?? '') . ' ' . ($student->second_name ?? '') . ' ' . ($student->third_name ?? '') . ' ' . ($student->forth_name ?? ''));
                    $subtitle = 'طالب';
                    if (isset($student->grade) && $student->grade) {
                        $subtitle .= ' - ' . $student->grade;
                    }
                    if (isset($student->student_code) && $student->student_code) {
                        $subtitle .= ' (كود: ' . $student->student_code . ')';
                    }
                    $results[] = [
                        'title' => $name ?: 'بدون اسم',
                        'subtitle' => $subtitle,
                        'icon' => 'fa-user',
                        'url' => url('student-profile/' . $student->id)
                    ];
                }
            } catch (\Exception $e) {
                \Log::warning('Error searching students', ['error' => $e->getMessage()]);
            }
            
            // Search Lectures
            try {
                $lectures = Lecture::where(function($q) use ($query) {
                        $q->where('title', 'like', "%{$query}%")
                          ->orWhere('description', 'like', "%{$query}%");
                    })
                    ->with('month')
                    ->take(5)
                    ->get();
                
                foreach ($lectures as $lecture) {
                    $monthName = 'غير محدد';
                    try {
                        if ($lecture->month) {
                            $monthName = $lecture->month->name ?? 'غير محدد';
                        }
                    } catch (\Exception $e) {
                        // Ignore relationship errors
                    }
                    
                    $results[] = [
                        'title' => $lecture->title ?? 'بدون عنوان',
                        'subtitle' => 'محاضرة - ' . $monthName,
                        'icon' => 'fa-chalkboard-teacher',
                        'url' => url('lecture/' . $lecture->id . '/edit')
                    ];
                }
            } catch (\Exception $e) {
                \Log::warning('Error searching lectures', ['error' => $e->getMessage()]);
            }
            
            // Search Exams
            try {
                $exams = ExamName::where('exam_title', 'like', "%{$query}%")
                    ->take(5)
                    ->get();
                
                foreach ($exams as $exam) {
                    $results[] = [
                        'title' => $exam->exam_title ?? 'بدون اسم',
                        'subtitle' => 'امتحان',
                        'icon' => 'fa-book-open',
                        'url' => url('exam_name')
                    ];
                }
            } catch (\Exception $e) {
                \Log::warning('Error searching exams', ['error' => $e->getMessage()]);
            }
            
            // Search Months
            try {
                $months = Month::where('name', 'like', "%{$query}%")
                    ->take(3)
                    ->get();
                
                foreach ($months as $month) {
                    $results[] = [
                        'title' => $month->name ?? 'بدون اسم',
                        'subtitle' => 'شهر',
                        'icon' => 'fa-calendar-alt',
                        'url' => url('month')
                    ];
                }
            } catch (\Exception $e) {
                \Log::warning('Error searching months', ['error' => $e->getMessage()]);
            }
            
            \Log::info('Search results', ['count' => count($results)]);
            
            return response()->json(['results' => array_slice($results, 0, 10)]);
        } catch (\Exception $e) {
            \Log::error('Search error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['results' => [], 'error' => $e->getMessage()], 500);
        }
    }
}
