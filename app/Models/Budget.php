<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
  
    protected $fillable = [
        'attachment_lecturer_id',
        'students_count',
        'days',
        'transport'
    ];
    public function lecturer()
{
    return $this->belongsTo(Lecturer::class, 'attachment_lecturer_id');
}

public function attachmentStudents()
{
    // 'attachment_lecturer_id' is the foreign key on the attachment_students table
    return $this->hasMany(AttachmentStudent::class, 'attachment_lecturer_id');
}
public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
