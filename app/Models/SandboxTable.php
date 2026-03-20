<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SandboxTable extends Model
{
    use HasFactory;

    protected $fillable = ['sandbox_database_id', 'table_name', 'display_name', 'order'];

    public function sandboxDatabase()
    {
        return $this->belongsTo(SandboxDatabase::class);
    }
}