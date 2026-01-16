<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinalReport extends Model
{
    protected $fillable = [
        'attachment_student_id',
        'title',
        'content',
        'file_path',
        'is_submitted',
    ];

   public function attachmentStudent()
    {
        // 'attachment_student_id' is the column in your final_reports table
        return $this->belongsTo(AttachmentStudent::class, 'attachment_student_id');
    }
}

