<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    use HasFactory;

    protected $fillable = [
      'title', 'start_date', 'end_date', 'user_id', 'is_sent'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
