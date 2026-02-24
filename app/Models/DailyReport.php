<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyReport extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Add casting for dates
    protected $casts = [
        'report_date' => 'date',
         
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationship: Each logbook belongs to a student (via registration_number)
    public function student()
    {
        return $this->belongsTo(User::class, 'registration_number', 'registration_number');
    }
   
    public function weeklyReport()
    {
        return $this->belongsTo(WeeklyReport::class, 'weekly_report_id');
    }
}