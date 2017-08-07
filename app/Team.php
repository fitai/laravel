<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $primaryKey = 'team_id';

    // Get the athletes on the team
    public function athletes() 
    {
    	return $this->hasMany('App\Athlete', 'team_id', 'team_id');
    
    }

    // Get the trackers on the team
    public function trackers() 
    {
    	return $this->hasMany('App\Tracker', 'team_id', 'team_id');
    
    }
    	
    	
}
