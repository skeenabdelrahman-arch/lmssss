<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\DiscountCode;
use App\Models\Month;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DiscountCodeController extends Controller
{
    /**
     * عرض جميع أكواد الخصم
     */
    public function index()
    {
        $codes = DiscountCode::orderBy('created_at', 'desc')->get();
        return view('back.discount_codes.index', compact('codes'));
    }

    /**
     * عرض صفحة إنشاء كود خصم
     */
    public function create()
    {
        $months = Month::orderBy('grade')->orderBy('name')->get();
        return view('back.discount_codes.create', compact('months'));
    }

    /**
     * حفظ كود خصم جديد
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:discount_codes,code|string|max:50',
            'name' => 'nullable|string|max:255',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'min_amount' => 'nullable|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
            'description' => 'nullable|string',
            'months' => 'nullable|array',
            'months.*' => 'exists:months,id',
            'is_bundle' => 'nullable|boolean',
            'bundle_price' => 'nullable|numeric|min:0|required_if:is_bundle,1',
            'bundle_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // رفع صورة الحزمة إذا كانت موجودة
        $bundleImage = null;
        if ($request->hasFile('bundle_image')) {
            $image = $request->file('bundle_image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('upload_files'), $imageName);
            $bundleImage = $imageName;
        }

        $discountCode = DiscountCode::create([
            'code' => strtoupper($request->code),
            'name' => $request->name,
            'type' => $request->type,
            'value' => $request->value,
            'min_amount' => $request->min_amount,
            'max_uses' => $request->max_uses,
            'starts_at' => $request->starts_at,
            'expires_at' => $request->expires_at,
            'is_active' => $request->has('is_active') ? 1 : 0,
            'description' => $request->description,
            'is_bundle' => $request->has('is_bundle') ? 1 : 0,
            'bundle_price' => $request->bundle_price,
            'bundle_image' => $bundleImage,
        ]);

        // ربط الكورسات بالكود
        if ($request->has('months') && is_array($request->months)) {
            $discountCode->months()->sync($request->months);
        }

        return redirect()->route('admin.discount_codes.index')->with('success', 'تم إنشاء كود الخصم بنجاح');
    }

    /**
     * عرض صفحة تعديل كود خصم
     */
    public function edit($id)
    {
        $code = DiscountCode::with('months')->findOrFail($id);
        $months = Month::orderBy('grade')->orderBy('name')->get();
        return view('back.discount_codes.edit', compact('code', 'months'));
    }

    /**
     * تحديث كود خصم
     */
    public function update(Request $request, $id)
    {
        $code = DiscountCode::findOrFail($id);

        $request->validate([
            'code' => 'required|unique:discount_codes,code,' . $id . '|string|max:50',
            'name' => 'nullable|string|max:255',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'min_amount' => 'nullable|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
            'description' => 'nullable|string',
            'months' => 'nullable|array',
            'months.*' => 'exists:months,id',
            'is_bundle' => 'nullable|boolean',
            'bundle_price' => 'nullable|numeric|min:0|required_if:is_bundle,1',
            'bundle_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // رفع صورة الحزمة إذا كانت موجودة
        $bundleImage = $code->bundle_image;
        if ($request->hasFile('bundle_image')) {
            // حذف الصورة القديمة إذا كانت موجودة
            if ($bundleImage && file_exists(public_path('upload_files/' . $bundleImage))) {
                @unlink(public_path('upload_files/' . $bundleImage));
            }
            
            $image = $request->file('bundle_image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('upload_files'), $imageName);
            $bundleImage = $imageName;
        }

        $code->update([
            'code' => strtoupper($request->code),
            'name' => $request->name,
            'type' => $request->type,
            'value' => $request->value,
            'min_amount' => $request->min_amount,
            'max_uses' => $request->max_uses,
            'starts_at' => $request->starts_at,
            'expires_at' => $request->expires_at,
            'is_active' => $request->has('is_active') ? 1 : 0,
            'description' => $request->description,
            'is_bundle' => $request->has('is_bundle') ? 1 : 0,
            'bundle_price' => $request->bundle_price,
            'bundle_image' => $bundleImage,
        ]);

        // تحديث الكورسات المرتبطة بالكود
        if ($request->has('months') && is_array($request->months)) {
            $code->months()->sync($request->months);
        } else {
            $code->months()->sync([]);
        }

        return redirect()->route('admin.discount_codes.index')->with('success', 'تم تحديث كود الخصم بنجاح');
    }

    /**
     * حذف كود خصم
     */
    public function destroy($id)
    {
        $code = DiscountCode::findOrFail($id);
        $code->delete();

        return redirect()->back()->with('success', 'تم حذف كود الخصم بنجاح');
    }
}




