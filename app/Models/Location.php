<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $guarded = [];

    function parent()
    {
        return $this->belongsTo(Location::class, 'parent_code', 'code');

    }
}
