<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\ActivationCode;
use App\Models\Month;
use Illuminate\Http\Request;

class ActivationCodeController extends Controller
{
    /**
     * عرض جميع أكواد التفعيل
     */
    public function index(Request $request)
    {
        $query = ActivationCode::with(['month', 'bundle', 'student']);
        
        // Filters
        if ($request->has('type') && $request->type) {
            if ($request->type === 'course') {
                $query->whereNotNull('month_id')->whereNull('bundle_id');
            } elseif ($request->type === 'bundle') {
                $query->whereNotNull('bundle_id')->whereNull('month_id');
            }
        }
        
        if ($request->has('month_id') && $request->month_id) {
            $query->where('month_id', $request->month_id);
        }
        
        if ($request->has('status') && $request->status) {
            if ($request->status === 'used') {
                $query->whereNotNull('used_at');
            } elseif ($request->status === 'unused') {
                $query->whereNull('used_at');
            }
        }
        
        if ($request->has('is_active') && $request->is_active !== '') {
            $query->where('is_active', $request->is_active);
        }
        
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhereHas('month', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('student', function($q) use ($search) {
                      $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('second_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }
        
        $codes = $query->orderBy('created_at', 'desc')->paginate(20);
        $months = Month::orderBy('name')->get();
        
        return view('back.activation_codes.index', compact('codes', 'months'));
    }

    /**
     * عرض صفحة إنشاء أكواد تفعيل
     */
    public function create()
    {
        $months = Month::orderBy('name')->get();
        $bundles = \App\Models\DiscountCode::where('is_bundle', 1)
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();
        
        \Log::info('Bundles loaded for activation codes:', [
            'count' => $bundles->count(),
            'bundles' => $bundles->pluck('name', 'id')->toArray()
        ]);
        
        return view('back.activation_codes.create', compact('months', 'bundles'));
    }

    /**
     * حفظ أكواد تفعيل جديدة
     */
    public function store(Request $request)
    {
        $request->validate([
            'code_type' => 'required|in:course,bundle',
            'month_id' => 'required_if:code_type,course|nullable|exists:months,id',
            'bundle_id' => 'required_if:code_type,bundle|nullable|exists:discount_codes,id',
            'count' => 'required|integer|min:1|max:100',
            'expires_at' => 'nullable|date',
            'notes' => 'nullable|string|max:500',
        ], [
            'month_id.required_if' => 'يرجى اختيار الكورس',
            'bundle_id.required_if' => 'يرجى اختيار الحزمة',
        ]);

        $codes = [];
        for ($i = 0; $i < $request->count; $i++) {
            $data = [
                'code' => ActivationCode::generateCode(),
                'expires_at' => $request->expires_at,
                'notes' => $request->notes,
                'is_active' => true,
            ];
            
            if ($request->code_type === 'course') {
                $data['month_id'] = $request->month_id;
                $data['bundle_id'] = null;
            } else {
                $data['month_id'] = null;
                $data['bundle_id'] = $request->bundle_id;
            }
            
            ActivationCode::create($data);
        }

        $type = $request->code_type === 'course' ? 'كورس' : 'حزمة';
        return redirect()->route('admin.activation_codes.index')
            ->with('success', "تم إنشاء {$request->count} كود تفعيل لـ{$type} بنجاح");
    }

    /**
     * عرض تفاصيل كود تفعيل
     */
    public function show($id)
    {
        $code = ActivationCode::with(['month', 'student'])->findOrFail($id);
        return view('back.activation_codes.show', compact('code'));
    }

    /**
     * عرض صفحة تعديل كود تفعيل
     */
    public function edit($id)
    {
        $code = ActivationCode::findOrFail($id);
        $months = Month::orderBy('name')->get();
        return view('back.activation_codes.edit', compact('code', 'months'));
    }

    /**
     * تحديث كود تفعيل
     */
    public function update(Request $request, $id)
    {
        $code = ActivationCode::findOrFail($id);

        $request->validate([
            'month_id' => 'required|exists:months,id',
            'code' => 'required|unique:activation_codes,code,' . $id . '|string|max:50',
            'expires_at' => 'nullable|date',
            'notes' => 'nullable|string|max:500',
        ]);

        $code->update([
            'month_id' => $request->month_id,
            'code' => strtoupper($request->code),
            'expires_at' => $request->expires_at,
            'notes' => $request->notes,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('admin.activation_codes.index')
            ->with('success', 'تم تحديث كود التفعيل بنجاح');
    }

    /**
     * حذف كود تفعيل
     */
    public function destroy($id)
    {
        $code = ActivationCode::findOrFail($id);
        $code->delete();

        return redirect()->back()->with('success', 'تم حذف كود التفعيل بنجاح');
    }

    /**
     * تصدير أكواد التفعيل
     */
    public function export(Request $request)
    {
        $query = ActivationCode::with(['month', 'student']);
        
        // Filters
        if ($request->has('month_id') && $request->month_id) {
            $query->where('month_id', $request->month_id);
        }
        
        if ($request->has('status') && $request->status) {
            if ($request->status === 'used') {
                $query->whereNotNull('used_at');
            } elseif ($request->status === 'unused') {
                $query->whereNull('used_at');
            }
        }
        
        if ($request->has('is_active') && $request->is_active !== '') {
            $query->where('is_active', $request->is_active);
        }
        
        $codes = $query->orderBy('created_at', 'desc')->get();

        $filename = 'activation_codes_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($codes) {
            $file = fopen('php://output', 'w');
            
            // BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Headers
            fputcsv($file, [
                'الكود',
                'الكورس',
                'الصف',
                'الطالب',
                'البريد الإلكتروني',
                'تاريخ الاستخدام',
                'تاريخ الانتهاء',
                'الحالة',
                'مفعّل',
                'ملاحظات',
                'تاريخ الإنشاء'
            ]);

            // Data
            foreach ($codes as $code) {
                fputcsv($file, [
                    $code->code,
                    $code->month ? $code->month->name : '-',
                    $code->month ? $code->month->grade : '-',
                    $code->student ? $code->student->first_name . ' ' . $code->student->second_name . ' ' . $code->student->third_name . ' ' . $code->student->forth_name : 'غير مستخدم',
                    $code->student ? $code->student->email : '-',
                    $code->used_at ? $code->used_at->format('Y-m-d H:i') : 'غير مستخدم',
                    $code->expires_at ? $code->expires_at->format('Y-m-d') : 'غير محدد',
                    $code->used_at ? 'مستخدم' : ($code->expires_at && $code->expires_at->isPast() ? 'منتهي' : 'متاح'),
                    $code->is_active ? 'نعم' : 'لا',
                    $code->notes ?? '-',
                    $code->created_at->format('Y-m-d H:i'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * تصدير أكواد التفعيل كـ PDF للطباعة
     */
    public function exportPdf(Request $request)
    {
        $query = ActivationCode::with(['month', 'student']);
        
        // Filters
        if ($request->has('month_id') && $request->month_id) {
            $query->where('month_id', $request->month_id);
        }
        
        if ($request->has('status') && $request->status === 'unused') {
            $query->whereNull('used_at');
        }
        
        if ($request->has('is_active') && $request->is_active !== '') {
            $query->where('is_active', $request->is_active);
        }
        
        $codes = $query->whereNull('used_at')->orderBy('created_at', 'desc')->get();
        $size = $request->get('size', 'a4'); // a4, receipt
        
        return view('back.activation_codes.pdf', compact('codes', 'size'));
    }
}
