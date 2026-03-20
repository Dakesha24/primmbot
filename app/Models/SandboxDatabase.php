<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SandboxDatabase extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'prefix', 'description'];

    public function sandboxTables()
    {
        return $this->hasMany(SandboxTable::class)->orderBy('order');
    }
}