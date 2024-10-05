<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Register extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    const Types = [
        'binary',
        'integer',
        'string',
        'char',
        'float',
        'long',
    ];
}
