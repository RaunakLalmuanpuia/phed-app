<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    //
    use HasFactory;

    const MOBILE = 'Mobile';


    protected $fillable = ['recipient', 'otp', 'type','used'];
}
