<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Athlete extends Model
{
    protected $primaryKey = 'athlete_id';

    // Get the athlete's team
    public function team() 
    {
    	return $this->belongsTo('App\Team', 'team_id');
    
    }
    	

}
