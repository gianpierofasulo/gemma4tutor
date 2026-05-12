<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Exercise extends Model
{
    use HasFactory;

    // Aggiungi queste righe:
    protected $fillable = [
        'type',
        'file_path',
        'prompt_sent',
        'ai_response',
        'language'
    ];
}
