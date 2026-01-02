<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Lecture;
use App\Models\Month;
use App\Services\CacheService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LectureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lectures = Lecture::all();
        $months = Month::all();
        return view('back.lecture.index', compact('lectures', 'months'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $months = Month::all();
        return view('back.lecture.create', compact('months'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $grade = $request->grade;
        $month_id = $request->Month;
        $savedCount = 0;
        $errors = [];

        // التحقق من وجود محاضرات متعددة
        if ($request->has('lectures') && is_array($request->lectures)) {
            // إضافة محاضرات متعددة
            foreach ($request->lectures as $index => $lectureData) {
                try {
                    $save = new Lecture();
                    $save->title = $lectureData['title'] ?? '';
                    $save->description = $lectureData['description'] ?? '';
                    $save->video_url = $lectureData['video_url'] ?? null;
                    $save->grade = $grade;
                    $save->month_id = $month_id;

                    $save->status = isset($lectureData['status']) ? 1 : 0;
                    $save->is_featured = isset($lectureData['is_featured']) ? 1 : 0;

                    // حفظ المحاضرة أولاً للحصول على ID
                    $save->save();

                    // معالجة الصورة بعد الحصول على ID
                    if (!empty($request->file("lectures.{$index}.image"))) {
                        $file = $request->file("lectures.{$index}.image");
                        $ext = $file->getClientOriginalExtension();
                        $filename = strtolower($save->id . Str::random(20) . '.' . $ext);
                        $file->move('upload_files/', $filename);
                        $save->image = $filename;
                        $save->save();
                    } elseif (!empty($lectureData['image_url'])) {
                        // إذا تم اختيار صورة من المكتبة
                        $imageUrl = $lectureData['image_url'];
                        // استخراج اسم الملف من الرابط
                        if (filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                            $parsedUrl = parse_url($imageUrl);
                            $path = $parsedUrl['path'] ?? '';
                            $filename = basename($path);
                            // التأكد من أن الملف موجود في المجلد الصحيح
                            if (file_exists(public_path('upload_files/' . $filename))) {
                                $save->image = $filename;
                                $save->save();
                            }
                        } else {
                            // إذا كان الرابط نسبي
                            $filename = basename($imageUrl);
                            if (file_exists(public_path('upload_files/' . $filename))) {
                                $save->image = $filename;
                                $save->save();
                            }
                        }
                    }

                    // معالجة الفيديو بعد الحصول على ID
                    if (!empty($request->file("lectures.{$index}.video_server"))) {
                        $file = $request->file("lectures.{$index}.video_server");
                        $ext = $file->getClientOriginalExtension();
                        $filename = strtolower($save->id . Str::random(20) . '.' . $ext);
                        $file->move('upload_files/', $filename);
                        $save->video_server = $filename;
                        $save->save();
                    }
                    $savedCount++;

                    // إرسال إشعار للطلاب المشتركين إذا كانت المحاضرة مفعلة
                    if ($save->status == 1) {
                        try {
                            \App\Services\NotificationService::notifyNewLecture($save);
                        } catch (\Exception $e) {
                            \Log::error('Error sending lecture notification', ['lecture_id' => $save->id, 'error' => $e->getMessage()]);
                        }
                    }
                } catch (\Exception $e) {
                    $errors[] = "خطأ في المحاضرة #" . ($index + 1) . ": " . $e->getMessage();
                }
            }
        } else {
            // إضافة محاضرة واحدة (للتوافق مع النموذج القديم)
            $save = new Lecture();
            $save->title = $request->title;
            $save->description = $request->description;
            $save->video_url = $request->video_url;
            $save->grade = $request->grade;
            $save->month_id = $request->Month;

            // حفظ المحاضرة أولاً للحصول على ID
            $save->status = isset($request->status) ? 1 : 0;
            $save->is_featured = isset($request->is_featured) ? 1 : 0;
            $save->save();

            // معالجة الصورة بعد الحصول على ID
            if (!empty($request->file('image'))) {
                $file = $request->file('image');
                $ext = $file->getClientOriginalExtension();
                $filename = strtolower($save->id . Str::random(20) . '.' . $ext);
                $file->move('upload_files/', $filename);
                $save->image = $filename;
                $save->save();
            } elseif (!empty($request->image_url)) {
                // إذا تم اختيار صورة من المكتبة
                $imageUrl = $request->image_url;
                if (filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                    $parsedUrl = parse_url($imageUrl);
                    $path = $parsedUrl['path'] ?? '';
                    $filename = basename($path);
                    if (file_exists(public_path('upload_files/' . $filename))) {
                        $save->image = $filename;
                        $save->save();
                    }
                } else {
                    $filename = basename($imageUrl);
                    if (file_exists(public_path('upload_files/' . $filename))) {
                        $save->image = $filename;
                        $save->save();
                    }
                }
            }

            // معالجة الفيديو
            if (!empty($request->file('video_server'))) {
                $file = $request->file('video_server');
                $ext = $file->getClientOriginalExtension();
                $filename = strtolower($save->id . Str::random(20) . '.' . $ext);
                $file->move('upload_files/', $filename);
                $save->video_server = $filename;
                $save->save();
            }

            // إرسال إشعار للطلاب المشتركين إذا كانت المحاضرة مفعلة
            if ($save->status == 1) {
                try {
                    \App\Services\NotificationService::notifyNewLecture($save);
                } catch (\Exception $e) {
                    \Log::error('Error sending lecture notification', ['lecture_id' => $save->id, 'error' => $e->getMessage()]);
                }
            }

            $savedCount = 1;
        }

        // Clear cache after creating lecture
        CacheService::clearLecturesCache();

        if ($savedCount > 0) {
            $message = $savedCount > 1
                ? "تم حفظ {$savedCount} محاضرة بنجاح"
                : 'تم حفظ البيانات بنجاح';

            if (!empty($errors)) {
                $message .= ' (مع بعض الأخطاء: ' . implode(', ', $errors) . ')';
            }

            return redirect()->route('lecture.index')->with('success', $message);
        } else {
            return redirect()->back()->with('error', 'فشل في حفظ المحاضرات. ' . implode(', ', $errors));
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $lecture = Lecture::findOrFail($id);
        $months = Month::all();
        return view('back.lecture.edit', compact('lecture', 'months'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $save = Lecture::findorfail($id);

        // معالجة الصورة
        if (!empty($request->file('image'))) {
            // حذف الصورة القديمة إذا كانت موجودة
            if ($save->image && file_exists('upload_files/' . $save->image)) {
                @unlink('upload_files/' . $save->image);
            }

            $file = $request->file('image');
            $ext = $file->getClientOriginalExtension();
            $filename = strtolower($save->id . Str::random(20) . '.' . $ext);
            $file->move('upload_files/', $filename);
            $save->image = $filename;
        } elseif (!empty($request->image_url)) {
            // إذا تم اختيار صورة من المكتبة
            $imageUrl = $request->image_url;
            if (filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                $parsedUrl = parse_url($imageUrl);
                $path = $parsedUrl['path'] ?? '';
                $filename = basename($path);
                if (file_exists(public_path('upload_files/' . $filename))) {
                    // حذف الصورة القديمة إذا كانت مختلفة
                    if ($save->image && $save->image != $filename && file_exists('upload_files/' . $save->image)) {
                        @unlink('upload_files/' . $save->image);
                    }
                    $save->image = $filename;
                }
            } else {
                $filename = basename($imageUrl);
                if (file_exists(public_path('upload_files/' . $filename))) {
                    // حذف الصورة القديمة إذا كانت مختلفة
                    if ($save->image && $save->image != $filename && file_exists('upload_files/' . $save->image)) {
                        @unlink('upload_files/' . $save->image);
                    }
                    $save->image = $filename;
                }
            }
        }
        if (!empty($request->file('video_server'))) {
            $file = $request->file('video_server');
            $ext = $file->getClientOriginalExtension();
            $filename = strtolower($save->id . Str::random(20) . '.' . $ext);
            $file->move('upload_files/', $filename);
            $save->video_server = $filename;
        }
        $save->title = $request->title;
        $save->description = $request->description;
        $save->video_url = $request->video_url;
        $save->grade = $request->grade;
        $save->month_id = $request->Month;
        if (isset($request->status)) {
            $save->status = 1;
        } else {
            $save->status = 0;
        }
        if (isset($request->is_featured)) {
            $save->is_featured = 1;
        } else {
            $save->is_featured = 0;
        }
        $save->save();

        // Clear cache after updating lecture
        CacheService::clearLecturesCache();

        return redirect()->route('lecture.index')->with('success', 'تم تعديل البيانات بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete($id)
    {
        Lecture::findorfail($id)->delete();

        // Clear cache after deleting lecture
        CacheService::clearLecturesCache();

        return redirect()->back()->with('success', 'تم حذف البيانات بنجاح');
    }

    public function deleteAllLectures(Request $request)
    {
        // حماية إضافية: يتطلب تأكيد صريح
        if (!$request->has('confirm') || $request->confirm !== 'DELETE_ALL_LECTURES') {
            return redirect()->back()->with('error', 'يجب تأكيد العملية. هذه العملية خطيرة جداً!');
        }

        // استخدام soft delete بدلاً من truncate
        $count = Lecture::query()->delete();
        return redirect()->back()->with('success', "تم حذف {$count} محاضرة (يمكن استعادتهم من البيانات المحذوفة)");
    }

    public function get_monthes($grade)
    {
        $monthes = Month::where('grade', $grade)->pluck('name', 'id');
        return response()->json($monthes);
    }

    public function get_lectures($month_id)
    {
        $lectures = Lecture::where('month_id', $month_id)->pluck('title', 'id');
        return response()->json($lectures);
    }
}
