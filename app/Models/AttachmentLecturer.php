<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttachmentLecturer extends Model
{
    protected $guarded = [];

    public function attachment() {
        return $this->belongsTo(Attachment::class, 'attachment_id');
    }
  //  public function lecturer() {
   
  
  //return $this->belongsTo(Lecturer::class, 'lecturer_id');
// }

    public function department() {
        return $this->belongsTo(AdministrativeUnit::class, 'department_id');
    }
    public function jobGrade()
{
    // Make sure 'job_grade_id' matches the column name in your lecturers table
    return $this->belongsTo(\App\Models\JobGrade::class, 'job_grade_id');
}

public function budgets()
{
    return $this->hasMany(Budget::class);
}
public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
   // app/Models/AttachmentLecturer.php
public function lecturer()
{
    return $this->belongsTo(Lecturer::class, 'lecturer_id');
}
}

