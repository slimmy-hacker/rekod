<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'department_code',
        'school_code',
    ];

   
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_code', 'code');
    }

    public function school()
    {
        return $this->belongsTo(School::class, 'school_code', 'code');
    }
}
