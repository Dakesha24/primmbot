<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'order',
        'kelas_id',
        'cover_image',
    ];

    public function coverImageUrl(): string
    {
        if ($this->cover_image) {
            return asset($this->cover_image);
        }
        return asset('assets/images/cover-course-default.png');
    }

    public function kelas()
    {
        return $this->belongsTo(\App\Models\Kelas::class);
    }

    public function chapters()
    {
        return $this->hasMany(Chapter::class)->orderBy('order');
    }

    public function activities()
    {
        return $this->hasManyThrough(\App\Models\Activity::class, \App\Models\Chapter::class);
    }

    public function enrollments()
    {
        return $this->hasMany(\App\Models\CourseEnrollment::class);
    }
}
