<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    //
    protected $fillable = [
        'startDate',
        'deadline',
        'endDate',
        'title',
        'description',
        'status',
        'owner'       
    ];
    
}
