<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $table = 'kelas';

    protected $fillable = ['school_id', 'tahun_ajaran_id', 'name'];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function profiles()
    {
        return $this->hasMany(Profile::class);
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->school->name} — {$this->name} ({$this->tahunAjaran->name})";
    }
}
