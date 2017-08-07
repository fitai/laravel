<?php

namespace App\Http\Controllers;

use Auth;
use App\Athlete;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\User;

class HomeController extends Controller
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

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }
    public function profile()
    {
        return view('profile');
    }
    public function switch()
    {
        return view('switch');
    }
    public function switchAthlete($id) 
    {
        // Check if athlete_id is in current User's Team
        if ( !(Auth::user()->athlete->team->athletes->contains('athlete_id', $id)) ) :
            $error = 'This athlete is not part of your team!';
            return back()->withErrors($error);
        endif;

        // Get new Athlete
        $athlete = (Athlete::where('athlete_id', $id)->first());

        // Check if athlete exists
        if (!$athlete) :
            $error = 'No such athlete!';
            return back()->withErrors($error);
        endif;

        // Get User from Athlete
        $newUser = User::find($athlete->user_id);

        // Log out as old user and log in as new user
        Auth::logout();
        Auth::login($newUser);
        
        return redirect('home');   
    }
    public function rfidListener(Request $request) 
    {
        // Get all trackers for the User's team
        $trackers = Auth::user()->athlete->team->trackers;

        $trackerID = $request->trackerID;
        return view('rfid/listener', compact('trackers', 'trackerID'));
    }
    public function rfidLogin(Request $request) 
    {

        // Check if RFID string is passed
        if (!$request->rfid) :
            return redirect()->route('rfid.listener');
        endif;

        // Check if RFID belongs to any active User
        $newUser = User::where('rfid', $request->rfid)->first();
        
        if (!$newUser) :
            $error = 'RFID not found. Try another bracelet.';
            return back()->withErrors($error);
        endif;

        $rfidTrackerID = $request->trackerID;

        // Log out as old user and log in as new user
        Auth::logout();
        Auth::login($newUser);
        
        return redirect('/lift?rfidTrackerID='.$rfidTrackerID);
    }
        
        
}
