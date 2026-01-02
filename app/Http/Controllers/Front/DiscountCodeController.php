<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\DiscountCode;
use Illuminate\Http\Request;

class DiscountCodeController extends Controller
{
    /**
     * التحقق من كود الخصم
     */
    public function validateCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'amount' => 'required|numeric|min:0',
        ]);

        $code = DiscountCode::where('code', strtoupper($request->code))->first();

        if (!$code) {
            return response()->json([
                'valid' => false,
                'message' => 'كود الخصم غير صحيح'
            ], 404);
        }

        $validation = $code->isValid($request->amount);

        if (!$validation['valid']) {
            return response()->json([
                'valid' => false,
                'message' => $validation['message']
            ], 400);
        }

        $discount = $code->calculateDiscount($request->amount);
        $finalAmount = $request->amount - $discount;

        return response()->json([
            'valid' => true,
            'message' => 'كود الخصم صالح',
            'discount' => $discount,
            'final_amount' => $finalAmount,
            'code_id' => $code->id,
            'code_type' => $code->type,
            'code_value' => $code->value,
        ]);
    }
}




