<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttachmentSchedule extends Model
{
    use HasFactory;

    // Disable guarded so all fields are mass assignable
    protected $guarded = [];
}
