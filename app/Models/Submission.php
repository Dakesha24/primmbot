<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'activity_id',
        'answer_text',
        'answer_code',
        'is_correct',
        'score',
        'score_keruntutan',
        'score_berargumen',
        'score_kesimpulan',
        'attempt',
        'ai_feedback',
    ];

    protected function casts(): array
    {
        return [
            'is_correct' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function teacherReview()
    {
        return $this->hasOne(TeacherReview::class);
    }
}