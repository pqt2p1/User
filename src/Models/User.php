<?php

namespace Pqt2p1\User\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\CanResetPassword as CanReset;

class User extends Authenticatable implements CanReset, MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, CanResetPassword;
    
    protected $hidden = [   
        'password'
    ];
}
