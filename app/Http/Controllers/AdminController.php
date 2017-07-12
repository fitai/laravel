<?php

namespace App\Http\Controllers;

use App\Lift;
use App\Team;
use App\Coach;
use App\Client;
use App\Athlete;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {

        $registered = (object)array(
            'athletes' => $this->getAthleteCount(),
            'coaches' => $this->getCoachCount(),
            'teams' => $this->getTeamCount(),
            'clients' => $this->getClientCount()
        );

        $lifts = $this->getLiftCount();

        return view('admin/home', compact('registered', 'lifts'));
    }

    // Admin - Watch
    public function watch() 
    {
        // Get all collars for the User's team
        $collars = Auth::user()->athlete->team->collars;

        return view('admin/watch', compact('collars'));
    }
        

    // Get current number of registered athletes
    public function getAthleteCount() 
    {
        return Athlete::count();
    }

    // Get current number of registered coaches
    public function getCoachCount() 
    {
        return Coach::count();
    }

    // Get current number of registered teams
    public function getTeamCount() 
    {
        $teams = array(
            'total' => Team::count(),
            'soccer' => Team::whereSport('Soccer')->count(),
            'football' => Team::whereSport('Football')->count(),
            'basketball' => Team::whereSport('Basketball')->count(),
        );
        return $teams;
    }

    // Get current number of registered clients
    public function getClientCount() 
    {
        return Client::count();
    }

    // Get current number of Lifts
    public function getLiftCount() 
    {
        return Lift::count();
    }
        
}
