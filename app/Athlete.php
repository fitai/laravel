<?php

namespace App;

use App\Lift;
use Illuminate\Database\Eloquent\Model;

class Athlete extends Model
{
    protected $primaryKey = 'athlete_id';

    // Get the athlete's team
    public function team() 
    {
    	return $this->belongsTo('App\Team', 'team_id');
    
    }

    // Get the athlete's last lift
    public function lastLift() 
    {
    	$lastLift = Lift::where([
    		['athlete_id', '=', $this->athlete_id], 
    		['ended_at', '<>', null]
    	])
    	->orderBy('ended_at', 'desc')
    	->first();

    	return $lastLift;

    }
    	
    	

}
