<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndurstrialSupervisor extends Model
{
    protected $table = 'industry_supervisors';
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


}
