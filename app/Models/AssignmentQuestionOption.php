<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignmentQuestionOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'option_text',
        'is_correct',
        'display_order',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'display_order' => 'integer',
    ];

    public function question()
    {
        return $this->belongsTo(AssignmentQuestion::class, 'question_id');
    }
}
