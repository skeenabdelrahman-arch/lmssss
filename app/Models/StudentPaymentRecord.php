<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentPaymentRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'month_id',
        'amount',
        'payment_date',
        'payment_method',
        'notes',
        'is_confirmed',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'is_confirmed' => 'boolean',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function month()
    {
        return $this->belongsTo(Month::class, 'month_id');
    }

    /**
     * Get payment statistics for a student
     */
    public static function getPaymentStats($studentId)
    {
        $total = self::where('student_id', $studentId)->where('is_confirmed', true)->sum('amount');
        $pending = self::where('student_id', $studentId)->where('is_confirmed', false)->sum('amount');
        $count = self::where('student_id', $studentId)->where('is_confirmed', true)->count();

        return [
            'total_paid' => $total,
            'pending' => $pending,
            'payment_count' => $count,
        ];
    }
}
