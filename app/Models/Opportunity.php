<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Opportunity extends Model
{
    use HasFactory;

    protected $fillable = [
    'company_id',
    'title',
    'description',
    'location',
    'expiry_date',
];

protected $dates = ['expiry_date'];
 protected $casts = [
        'expiry_date' => 'date',
    ];

public function scopeActive($query)
{
    return $query->where('expiry_date', '>=', Carbon::today());
}

public function getIsExpiredAttribute()
{
    return $this->expiry_date->isPast();
}

    // Relationship
    public function company()
    {
        return $this->belongsTo(User::class, 'company_id');
    }

    
public function applicants()
{
    return $this->belongsToMany(User::class, 'opportunity_applications', 'opportunity_id', 'user_id')
                ->withPivot(['cover_letter', 'cv_path', 'applied_at'])
                ->withTimestamps();
}

public function applications()
{
    return $this->hasMany(\App\Models\Application::class, 'opportunity_id', 'id');
}


 

}
