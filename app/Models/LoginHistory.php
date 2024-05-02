<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginHistory extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'user_id',
        'email',
        'request_ip',
        'user_agent',
        'request_for',
        'user_agent',
        'remark',
        'status',
    ];
}
