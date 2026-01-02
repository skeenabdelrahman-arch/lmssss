<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Month extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'price',
        'grade',
        'image',
        'display_order',
    ];

    public function lectures()
    {
        return $this->hasMany(Lecture::class, 'month_id');
    }

    public function pdfs()
    {
        return $this->hasMany(Pdf::class, 'month_id');
    }

    public function examNames()
    {
        return $this->hasMany(ExamName::class, 'month_id');
    }

    public function subscriptions()
    {
        return $this->hasMany(StudentSubscriptions::class, 'month_id');
    }

    /**
     * علاقة الكورس بأكواد الخصم (Many-to-Many)
     */
    public function discountCodes()
    {
        return $this->belongsToMany(DiscountCode::class, 'discount_code_months', 'month_id', 'discount_code_id');
    }
}
