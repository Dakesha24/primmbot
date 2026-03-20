<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiInteractionLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'activity_id',
        'prompt_sent',
        'response_received',
        'tokens_used',
        'response_time',
    ];

    protected function casts(): array
    {
        return [
            'response_time' => 'decimal:2',
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
}