<?php

namespace App;

use App\Lift;
use Carbon\Carbon;
use App\LiftSchedule;
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

    // Get the athlete's next scheduled lift
    public function nextLift()
    {   
        $nextLift = LiftSchedule::where([
            ['athlete_id', '=', $this->athlete_id], 
            ['end_time', '=', null],
            ['start_time', '>=', Carbon::now()]
        ])
        ->orderBy('start_time', 'desc')
        ->first();

        return $nextLift;
    }
        
    	
    	

}
