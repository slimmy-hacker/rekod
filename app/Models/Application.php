<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'opportunity_id',
        'student_id',
        'cover_letter',
        'status',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    
    public function opportunity()
    {
        return $this->belongsTo(Opportunity::class);
    }
}
