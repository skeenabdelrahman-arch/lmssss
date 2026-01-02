<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class ExamName extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'exam_title',
        'exam_description',
        'exam_time',
        'grade',
        'month_id',
        'status',
        'public_access',
        'hide_public_result',
        'display_order',
        'opens_at',
        'closes_at',
        'auto_show_results',
        'randomize_questions',
    ];

    protected $casts = [
        'opens_at' => 'datetime',
        'closes_at' => 'datetime',
        'auto_show_results' => 'boolean',
    ];

    public function month()
    {
        return $this->belongsTo(Month::class,'month_id');
    }
    
    public function questions()
    {
        return $this->hasMany(ExamQuestion::class, 'exam_id');
    }

    /**
     * Check if exam is currently open
     */
    public function isOpen()
    {
        $now = Carbon::now();
        
        // إذا لم يتم تحديد أوقات، الامتحان مفتوح
        if (!$this->opens_at && !$this->closes_at) {
            return true;
        }
        
        // التحقق من وقت الفتح
        if ($this->opens_at && $now->lt($this->opens_at)) {
            return false;
        }
        
        // التحقق من وقت الإغلاق
        if ($this->closes_at && $now->gt($this->closes_at)) {
            return false;
        }
        
        return true;
    }

    /**
     * Check if exam is closed
     */
    public function isClosed()
    {
        if (!$this->closes_at) {
            return false;
        }
        
        return Carbon::now()->gt($this->closes_at);
    }

    /**
     * Check if exam hasn't opened yet
     */
    public function isUpcoming()
    {
        if (!$this->opens_at) {
            return false;
        }
        
        return Carbon::now()->lt($this->opens_at);
    }

    /**
     * Get time until exam opens (in human readable format)
     */
    public function getTimeUntilOpenAttribute()
    {
        if (!$this->opens_at || !$this->isUpcoming()) {
            return null;
        }
        
        return Carbon::now()->diffForHumans($this->opens_at);
    }

    /**
     * Get time until exam closes (in human readable format)
     */
    public function getTimeUntilCloseAttribute()
    {
        if (!$this->closes_at || $this->isClosed()) {
            return null;
        }
        
        return Carbon::now()->diffForHumans($this->closes_at);
    }

    /**
     * Auto show results if exam is closed and auto_show_results is enabled
     */
    public function autoShowResultsIfClosed()
    {
        if ($this->isClosed() && $this->auto_show_results) {
            // إظهار النتائج لجميع الطلاب
            \App\Models\ExamResult::where('exam_id', $this->id)
                ->where('show_degree', 0)
                ->update(['show_degree' => 1, 'is_marked' => 1]);
            
            return true;
        }
        
        return false;
    }
}

