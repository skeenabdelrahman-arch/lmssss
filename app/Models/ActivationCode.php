<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ActivationCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'month_id',
        'bundle_id',
        'code',
        'student_id',
        'used_at',
        'expires_at',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'used_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * العلاقات
     */
    public function month()
    {
        return $this->belongsTo(Month::class);
    }

    public function bundle()
    {
        return $this->belongsTo(DiscountCode::class, 'bundle_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * إنشاء كود تفعيل تلقائي
     */
    public static function generateCode($length = 12)
    {
        do {
            $code = strtoupper(Str::random($length));
        } while (self::where('code', $code)->exists());
        
        return $code;
    }

    /**
     * التحقق من صلاحية الكود
     */
    public function isValid()
    {
        // التحقق من التفعيل
        if (!$this->is_active) {
            return ['valid' => false, 'message' => 'كود التفعيل غير مفعّل'];
        }

        // التحقق من الاستخدام السابق
        if ($this->used_at) {
            return ['valid' => false, 'message' => 'كود التفعيل مستخدم بالفعل'];
        }

        // التحقق من تاريخ الانتهاء
        if ($this->expires_at && Carbon::now()->gt($this->expires_at)) {
            return ['valid' => false, 'message' => 'كود التفعيل منتهي الصلاحية'];
        }

        return ['valid' => true, 'message' => 'كود التفعيل صالح'];
    }

    /**
     * تفعيل الكود للطالب
     */
    public function activate($studentId)
    {
        if (!$this->isValid()['valid']) {
            return false;
        }

        $this->update([
            'student_id' => $studentId,
            'used_at' => now(),
        ]);

        return true;
    }
}
