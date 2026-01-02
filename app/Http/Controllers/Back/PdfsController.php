<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PdfsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pdfs = Pdf::all();
        return view('back.pdf.index', compact('pdfs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('back.pdf.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'grade' => 'required',
            'Month' => 'required|exists:months,id',
            'lecture_id' => 'nullable|exists:lectures,id',
        ]);

        $save = new Pdf();

        // الحصول على رابط الملف - الأولوية لـ file_url من input text
        $fileUrl = trim($request->input('file_url') ?? '');

        // إذا كان file_url فارغاً، نتحقق من media_file_url (من media picker)
        if (empty($fileUrl)) {
            $fileUrl = trim($request->input('media_file_url') ?? '');
        }

        // التأكد من وجود رابط
        if (empty($fileUrl)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'يرجى إدخال رابط الملف أو اختيار ملف من المكتبة');
        }

        // تنظيف الرابط من المسافات الزائدة
        $fileUrl = trim($fileUrl);

        $save->file_url = $fileUrl;
        $save->title = $request->title;
        $save->description = $request->description;
        $save->grade = $request->grade;
        $save->month_id = $request->Month;
        $save->lecture_id = $request->lecture_id;
        if (isset($request->status)) {
            $save->status = 1;
        } else {
            $save->status = 0;
        }
        $save->save();

        // إرسال إشعار للطلاب المشتركين إذا كانت المذكرة مفعلة
        if ($save->status == 1) {
            try {
                \App\Services\NotificationService::notifyNewPdf($save);
            } catch (\Exception $e) {
                \Log::error('Error sending PDF notification', ['pdf_id' => $save->id, 'error' => $e->getMessage()]);
            }
        }

        return redirect()->route('pdf.index')->with('success', 'تم اضافة المذكرة بنجاح');

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
        $pdf = Pdf::findOrFail($id);
        return view('back.pdf.edit', compact('pdf'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'grade' => 'required',
            'Month' => 'required|exists:months,id',
            'lecture_id' => 'nullable|exists:lectures,id',
        ]);

        $save = Pdf::findorfail($id);

        // الحصول على رابط الملف - الأولوية لـ file_url من input text
        $fileUrl = trim($request->input('file_url') ?? '');

        // إذا كان file_url فارغاً، نتحقق من media_file_url (من media picker)
        if (empty($fileUrl)) {
            $fileUrl = trim($request->input('media_file_url') ?? '');
        }

        // التأكد من وجود رابط
        if (empty($fileUrl)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'يرجى إدخال رابط الملف أو اختيار ملف من المكتبة');
        }

        // تنظيف الرابط من المسافات الزائدة
        $fileUrl = trim($fileUrl);

        // حفظ رابط الملف (يدعم الروابط الخارجية والملفات المحلية)
        $save->file_url = $fileUrl;
        $save->title = $request->title;
        $save->description = $request->description;
        $save->grade = $request->grade;
        $save->month_id = $request->Month;
        $save->lecture_id = $request->lecture_id;
        if (isset($request->status)) {
            $save->status = 1;
        } else {
            $save->status = 0;
        }
        $save->save();
        return redirect()->route('pdf.index')->with('success', 'تم تحديث البيانات بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete($id)
    {
        Pdf::findorfail($id)->delete();
        return redirect()->back()->with('success', 'تم حذف المذكرة');
    }
    public function deleteAllPdfs(Request $request)
    {
        // حماية إضافية: يتطلب تأكيد صريح
        if (!$request->has('confirm') || $request->confirm !== 'DELETE_ALL_PDFS') {
            return redirect()->back()->with('error', 'يجب تأكيد العملية. هذه العملية خطيرة جداً!');
        }

        // استخدام soft delete بدلاً من truncate
        $count = Pdf::query()->delete();
        return redirect()->back()->with('success', "تم حذف {$count} مذكرة (يمكن استعادتهم من البيانات المحذوفة)");
    }
}
