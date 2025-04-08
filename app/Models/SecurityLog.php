<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SecurityLog extends Model
{
    protected $fillable = [
        'user_id',
        'email',
        'ip_address',
        'user_agent',
        'event'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}