<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Panel;


class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'email',
        'password',
        'role',
        'is_active',
        'google_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // === Relationships ===

    public function getNameAttribute(): string
    {
        return $this->profile?->full_name ?? $this->username;
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    public function aiInteractionLogs()
    {
        return $this->hasMany(AiInteractionLog::class);
    }

    public function teacherReviews()
    {
        return $this->hasMany(TeacherReview::class, 'teacher_id');
    }
}
