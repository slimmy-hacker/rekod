<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
    'student_id',
    'report_type',
    'week_number',
    'report_date',
    'description',
    'file_path'
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
