<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lecturer extends Model
{
    protected $guarded = [];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function department()
    {
        return $this->belongsTo(AdministrativeUnit::class, 'department_id');
    }
    public function jobGrade()
{
    // 1. The related model
    // 2. The foreign key on the lecturers table (job_grade)
    // 3. The owner key on the job_grades table (dekut_grade)
    return $this->belongsTo(JobGrade::class, 'job_grade', 'dekut_grade');
}
public function budgets()
{
    return $this->hasMany(Budget::class);
}
public function attachmentStudents()
{
    // 'attachment_lecturer_id' is the foreign key on the attachment_students table
    return $this->hasMany(AttachmentStudent::class, 'attachment_lecturer_id');
}
}
