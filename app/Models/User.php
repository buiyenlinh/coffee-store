<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;
    protected $fiiable = [
        'fullname',
        'username',
        'password',
        'address',
        'gender',
        'birthday',
        'avatar',
        'role_id'
    ];
}
