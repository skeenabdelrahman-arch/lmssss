<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentSubscriptions extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'first_name',
        'second_name',
        'third_name',
        'forth_name',
        'month_id',
        'grade',
        'is_active',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function month()
    {
        return $this->belongsTo(Month::class, 'month_id');
    }
}
