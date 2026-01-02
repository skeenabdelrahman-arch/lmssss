<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lecture extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'month_id',
        'grade',
        'title',
        'description',
        'video_url',
        'video_server',
        'image',
        'status',
        'views',
        'is_featured',
        'scheduled_at',
        'display_order',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public function month()
    {
        return $this->belongsTo(Month::class, 'month_id');
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class, 'lesson_id');
    }

    /**
     * علاقة المحاضرة بالكويز الخاص بها (الكويز بعد هذه المحاضرة)
     */
    public function quiz()
    {
        return $this->hasOne(LectureQuiz::class, 'lecture_id');
    }

    /**
     * علاقة المحاضرة بمشاهدات الطلاب
     */
    public function lectureViews()
    {
        return $this->hasMany(LectureView::class);
    }

    /**
     * علاقة المحاضرة بالطلاب الذين شاهدوها
     */
    public function studentsViewed()
    {
        return $this->belongsToMany(Student::class, 'lecture_views', 'lecture_id', 'student_id')
            ->withPivot('viewed_at')
            ->withTimestamps();
    }

    public function restrictions()
    {
        return $this->hasMany(LectureRestriction::class);
    }

    /**
     * المذكرات المرتبطة بالمحاضرة
     */
    public function pdfs()
    {
        return $this->hasMany(Pdf::class, 'lecture_id');
    }

    /**
     * واجبات مرتبطة بهذه المحاضرة
     */
    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'lecture_id');
    }
    public function getImageUrl()
    {
        // إذا كانت المحاضرة لها صورة، ارجعها
        if (!empty($this->image)) {
            return url('upload_files/' . $this->image);
        }

        // إذا لم يكن لها صورة، ارجع صورة الكورس إذا كانت موجودة
        if ($this->month && !empty($this->month->image)) {
            return url('upload_files/' . $this->month->image);
        }

        // إذا لم يكن هناك أي صورة، ارجع null
        return null;
    }

}
