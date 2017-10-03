<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lift extends Model
{
    protected $primaryKey = 'lift_id';

    // Get the LiftType
    public function typeData() 
    {
    	return $this->belongsTo('App\LiftType', 'lift_type', 'name_display');
    
    }
}

