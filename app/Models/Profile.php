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
        'kelas',
        'school_name',
        'avatar',
        'tahun_ajaran',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isComplete(): bool
    {
        return !empty($this->full_name)
            && !empty($this->nim)
            && !empty($this->gender)
            && !empty($this->kelas)
            && !empty($this->school_name);
    }
}