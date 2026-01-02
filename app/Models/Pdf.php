<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pdf extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'file_url',
        'grade',
        'month_id',
        'lecture_id',
        'status',
        'display_order',
    ];

    public function month()
    {
        return $this->belongsTo(Month::class, 'month_id');
    }

    public function lecture()
    {
        return $this->belongsTo(Lecture::class, 'lecture_id');
    }

    /**
     * علاقة المذكرة بمشاهدات الطلاب
     */
    public function views()
    {
        return $this->hasMany(PdfView::class);
    }

    /**
     * علاقة المذكرة بالطلاب الذين شاهدوها
     */
    public function studentsViewed()
    {
        return $this->belongsToMany(Student::class, 'pdf_views', 'pdf_id', 'student_id')
            ->withPivot('viewed_at')
            ->withTimestamps();
    }
}
