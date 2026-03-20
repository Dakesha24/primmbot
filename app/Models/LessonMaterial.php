<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'chapter_id',
        'type',
        'content',
        'order',
    ];

    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }
}