<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

   
    protected $table = 'students';

    protected $fillable = [
        'user_id',
        'registration_number',
        'programme',
        'year_of_study',
        'department',
        'phone_alt',
    ];

    public function placements()
    {
        return $this->hasMany(Placement::class, 'student_id');
    }
    public function supervisor()
{
    return $this->belongsTo(User::class, 'supervisor_id');
}

}
