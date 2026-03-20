<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'order',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function lessonMaterials()
    {
        return $this->hasMany(LessonMaterial::class)->orderBy('order');
    }

    public function activities()
    {
        return $this->hasMany(Activity::class)->orderBy('order');
    }
}