<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LecturerAssigment extends Model
{
    use HasFactory;


    protected $table = 'lecturer_assignments';

    protected $guarded = [];

 public function lecturer()
    {
        return $this->belongsTo(AttachmentLecturer::class, 'attachment_lecturer_id');
    }
   public function student()
    {
        return $this->belongsTo(AttachmentStudent::class, 'attachment_student_id');
    }

    // Relationship to the company
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
