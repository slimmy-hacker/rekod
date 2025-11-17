<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdministrativeUnit extends Model
{
    protected $guarded = [];

    function parent()
    {
        return $this->belongsTo(AdministrativeUnit ::class, 'parent_code', 'code');

    }
}
