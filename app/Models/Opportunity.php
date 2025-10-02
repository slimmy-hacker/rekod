<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Opportunity extends Model
{
    use HasFactory;

    protected $fillable = [
        'industry_id',
        'title',
        'description',
        'location',
        'deadline',
        'status',
    ];

    
    public function applications()
    {
        return $this->hasMany(Application::class);
    }
     public function industry()
    {
        return $this->belongsTo(User::class, 'industry_id');
    }
}
