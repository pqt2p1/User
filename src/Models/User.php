<?php

namespace Pqt2p1\User\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class User extends Model
{
    use HasApiTokens, HasFactory ;
    
    protected $hidden = [
        'password'
    ];
}
