<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;

class User extends Authenticatable
{
    use HasFactory;
    protected $fillable = [
        'fullname',
        'username',
        'password',
        'address',
        'gender',
        'birthday',
        'avatar',
        'role_id',
    ];
}
