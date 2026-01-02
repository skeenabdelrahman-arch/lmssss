<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'month_id',
        'kashier_order_id',
        'payment_id',
        'amount',
        'original_amount',
        'discount_amount',
        'discount_code_id',
        'currency',
        'status',
        'payment_method',
        'kashier_response',
        'paid_at',
    ];

    protected $appends = ['status_label'];

    protected $casts = [
        'amount' => 'decimal:2',
        'original_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'kashier_response' => 'array',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function month()
    {
        return $this->belongsTo(Month::class);
    }

    public function discountCode()
    {
        return $this->belongsTo(DiscountCode::class);
    }

    public function isPaid()
    {
        return $this->status === 'paid';
    }

    public function getStatusLabelAttribute()
    {
        $labels = [
            'paid' => 'مدفوعة',
            'pending' => 'معلقة',
            'failed' => 'فاشلة',
            'refunded' => 'مسترجعة',
        ];

        return $labels[$this->status] ?? $this->status;
    }
}

