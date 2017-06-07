<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;

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
        $athlete = (\App\Athlete::where('athlete_id', $id)->first());

        // Check if athlete exists
        if (!$athlete) :
            $error = 'No such athlete!';
            return back()->withErrors($error);
        endif;

        // Get User from Athlete
        $newUser = \App\User::find($athlete->user_id);

        // Log out as old user and log in as new user
        Auth::logout();
        Auth::login($newUser);
        
        return redirect('home');   
    }
        
}
