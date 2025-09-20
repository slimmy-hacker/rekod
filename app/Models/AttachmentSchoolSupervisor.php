<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttachmentSchoolSupervisor extends Model
{
    protected $guarded = [];

    public function schedule() {
        return $this->belongsTo(AttachmentSchedule::class, 'attachment_schedule_id');
    }

    public function department() {
        return $this->belongsTo(Department::class, 'department_id');
    }
}
