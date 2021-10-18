<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill_Detail extends Model
{
    use HasFactory;
    protected $fillable = [
        'bill_id',
        'product_id',
        'number'
    ];
}
