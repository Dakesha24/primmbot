<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'full_name',
        'nim',
        'gender',
        'kelas_id',
        'avatar',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kelas()
    {
        return $this->belongsTo(\App\Models\Kelas::class);
    }

    public function avatarUrl(): ?string
    {
        if (!$this->avatar) return null;
        // Google OAuth atau URL eksternal lain — gunakan langsung
        if (str_starts_with($this->avatar, 'http')) return $this->avatar;
        // File lokal di public/avatars/
        return asset($this->avatar);
    }

    public function isComplete(): bool
    {
        return !empty($this->full_name)
            && !empty($this->nim)
            && !empty($this->gender)
            && !empty($this->kelas_id);
    }
}