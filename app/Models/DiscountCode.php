<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DiscountCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'type',
        'value',
        'min_amount',
        'max_uses',
        'used_count',
        'starts_at',
        'expires_at',
        'is_active',
        'description',
        'is_bundle',
        'bundle_price',
        'bundle_image',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_amount' => 'decimal:2',
        'bundle_price' => 'decimal:2',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
        'is_bundle' => 'boolean',
    ];

    /**
     * التحقق من صلاحية الكود
     */
    public function isValid($amount = 0)
    {
        // التحقق من التفعيل
        if (!$this->is_active) {
            return ['valid' => false, 'message' => 'كود الخصم غير مفعّل'];
        }

        // التحقق من التاريخ
        if ($this->starts_at && Carbon::now()->lt($this->starts_at)) {
            return ['valid' => false, 'message' => 'كود الخصم لم يبدأ بعد'];
        }

        if ($this->expires_at && Carbon::now()->gt($this->expires_at)) {
            return ['valid' => false, 'message' => 'كود الخصم منتهي الصلاحية'];
        }

        // التحقق من عدد الاستخدامات
        if ($this->max_uses && $this->used_count >= $this->max_uses) {
            return ['valid' => false, 'message' => 'تم استخدام كود الخصم بالكامل'];
        }

        // التحقق من الحد الأدنى
        if ($this->min_amount && $amount < $this->min_amount) {
            return ['valid' => false, 'message' => "الحد الأدنى للطلب: {$this->min_amount} جنيه"];
        }

        return ['valid' => true, 'message' => 'كود الخصم صالح'];
    }

    /**
     * حساب قيمة الخصم
     */
    public function calculateDiscount($amount)
    {
        if ($this->type === 'percentage') {
            return ($amount * $this->value) / 100;
        } else {
            return min($this->value, $amount); // لا يزيد عن المبلغ الأصلي
        }
    }

    /**
     * زيادة عدد الاستخدامات
     */
    public function incrementUsage()
    {
        $this->increment('used_count');
    }

    /**
     * علاقة الكود بالكورسات (Many-to-Many)
     */
    public function months()
    {
        return $this->belongsToMany(Month::class, 'discount_code_months', 'discount_code_id', 'month_id');
    }

    /**
     * التحقق من وجود كورسات مرتبطة بالكود
     */
    public function hasMonths()
    {
        return $this->months()->count() > 0;
    }

    /**
     * تفعيل جميع الكورسات المرتبطة بالكود للطالب
     */
    public function activateMonthsForStudent($studentId)
    {
        $student = \App\Models\Student::findOrFail($studentId);
        $activated = [];

        foreach ($this->months as $month) {
            $subscription = \App\Models\StudentSubscriptions::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'month_id' => $month->id,
                ],
                [
                    'grade' => $student->grade,
                    'is_active' => 1,
                ]
            );
            $activated[] = $month;
        }

        return $activated;
    }

    /**
     * حساب سعر الحزمة (مجموع أسعار الكورسات بعد الخصم)
     */
    public function calculateBundlePrice()
    {
        if (!$this->is_bundle || !$this->bundle_price) {
            return null;
        }

        return $this->bundle_price;
    }

    /**
     * حساب السعر الأصلي للحزمة (مجموع أسعار الكورسات بدون خصم)
     */
    public function getOriginalBundlePrice()
    {
        $total = 0;
        foreach ($this->months as $month) {
            $total += (float)$month->price;
        }
        return $total;
    }

    /**
     * حساب نسبة التوفير في الحزمة
     */
    public function getSavingsPercentage()
    {
        if (!$this->is_bundle) {
            return 0;
        }

        $originalPrice = $this->getOriginalBundlePrice();
        $bundlePrice = $this->bundle_price;

        if ($originalPrice <= 0) {
            return 0;
        }

        $savings = $originalPrice - $bundlePrice;
        return round(($savings / $originalPrice) * 100, 1);
    }
}




