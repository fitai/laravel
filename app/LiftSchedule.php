<?php

namespace App;

use App\Athlete;
use Illuminate\Database\Eloquent\Model;

class LiftSchedule extends Model
{
	protected $guarded = ['id'];

	// Get the athlete
    public function athlete() 
    {

    	return $this->belongsTo('App\Athlete', 'athlete_id', 'athlete_id');
    
    }
   	
    	
}
