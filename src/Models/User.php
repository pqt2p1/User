<?php

namespace Pqt2p1\User\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanReset;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements CanReset
{
    use HasApiTokens, HasFactory, Notifiable, CanResetPassword;
    
    protected $hidden = [
        'password'
    ];
}
