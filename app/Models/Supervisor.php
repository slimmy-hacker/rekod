<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supervisor extends Model
{
   
public function assignedStudents()
{
    return $this->hasMany(Student::class, 'supervisor_id');
}

}
