<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LecturerAssigment extends Model
{
    use HasFactory;


    protected $table = 'lecturer_assignments';

    protected $guarded = [];
}
