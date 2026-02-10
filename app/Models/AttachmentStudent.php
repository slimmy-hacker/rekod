<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttachmentStudent extends Model
{
    protected $guarded = [];

    public function attachment() {
        return $this->belongsTo(Attachment::class, 'attachment_id');
    }

    public function lecturer()
    {
        return $this->belongsTo(Lecturer::class, 'lecturer_id');
    }
    public function attachment_lecturer()
    {
        return $this->belongsTo(AttachmentLecturer::class, 'lecturer_id');
    }

   
    public function student() {
        return $this->belongsTo(Student::class, 'student_id');
    }
    public function company() {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function program()
    {
        return $this->hasOneThrough(
            AdministrativeUnit::class,     
            Student::class,               
            'id',                          
            'id',                          
            'student_id',                  
            'program_id'                   
        )->with('parent');
    }
    public function getDepartmentAttribute()
    {
        return $this->student?->program?->parent;
    }
   public function weeklyReports()
{
    return $this->hasMany(WeeklyReport::class);
}

public function industrialSupervisor()
{
    return $this->belongsTo(IndustrialSupervisor::class);
}

public function attachmentLecturer() // Use camelCase
{
    // The second parameter MUST be the column in your attachment_students table
    return $this->belongsTo(AttachmentLecturer::class, 'attachment_lecturer_id');
}



}
