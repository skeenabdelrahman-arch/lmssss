<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PdfView extends Model
{
    use HasFactory;

    protected $fillable = [
        'pdf_id',
        'student_id',
        'viewed_at',
    ];

    protected $casts = [
        'viewed_at' => 'datetime',
    ];

    /**
     * علاقة المشاهدة بالمذكرة
     */
    public function pdf()
    {
        return $this->belongsTo(Pdf::class);
    }

    /**
     * علاقة المشاهدة بالطالب
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}

