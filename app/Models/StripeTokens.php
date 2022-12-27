<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StripeTokens extends Model
{
    
    use HasFactory;

    public $casts = [
        'access_token' => 'encrypted',
        'refresh_token' => 'encrypted',
    ];

}
