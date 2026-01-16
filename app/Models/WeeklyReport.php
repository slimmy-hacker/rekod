<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeeklyReport extends Model
{
    use HasFactory;

    protected $fillable = [
       'attachment_student_id', // Make sure this is first
        'week_id',
        'week_start_date',
        'week_end_date',
        'weekly_report',
        'industrial_supervisor_comment',
        'lecturer_comment',
        'is_approved',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'week_start_date' => 'date',
        'week_end_date' => 'date',
    ];

    protected $appends = ['status'];

    public function getStatusAttribute()
    {
        if (empty($this->weekly_report)) {
            return 'pending_student';
        }
        if (!$this->is_approved) {
            return 'pending_industrial';
        }
        if (empty($this->lecturer_comment)) {
            return 'pending_lecturer';
        }
        return 'completed';
    }

    // --- REMOVED: attachmentStudent() ---
    // --- REMOVED: getStudentAttribute() ---

    /**
     * DIRECT Relationship to Student
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function dailyReports()
    {
        return $this->hasMany(DailyReport::class, 'weekly_report_id');
    }
    
// In app/Models/WeeklyReport.php

public function attachmentStudent()
{
    return $this->belongsTo(AttachmentStudent::class, 'attachment_student_id');
}

    
}