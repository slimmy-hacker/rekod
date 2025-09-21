<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    protected $fillable = [
        'staffnumber',
        'grade',
        'lecturer_name',
        'daily_allowance',
        'transport_town',
        'totals',
        'student_list_file'
    ];
}
