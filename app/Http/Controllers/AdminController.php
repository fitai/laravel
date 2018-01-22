<?php

namespace App\Http\Controllers;

use App\Lift;
use App\Team;
use App\Coach;
use App\Client;
use App\Athlete;
use App\LiftType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        // Get all trackers for the User's team
        $trackers = Auth::user()->athlete->team->trackers;

        return view('admin/watch', compact('trackers'));
    }

     // Admin - Watch
    public function athleteRest() 
    {
        $query = "
        WITH user_lifts AS (
                SELECT
                        lift_id
                        , athlete_id
                        , lift_type
                        , lift_weight::TEXT || ' '::TEXT || weight_units::TEXT AS weight
                        , COALESCE(init_num_reps, final_num_reps) AS num_reps
                        , calc_reps
                        , created_at AS start_time
                FROM lifts
                WHERE athlete_id = 3
                ORDER BY created_at DESC
        )
        , lift_lengths AS (
                SELECT
                        MAX(timepoint) - MIN(timepoint) AS duration_s
                        , lift_id
                FROM lift_data
                WHERE lift_id IN (SELECT lift_id FROM user_lifts)
                GROUP BY lift_id
        )
        , lift_tot AS (
                SELECT
                        ll.duration_s
                        , ul.*
                FROM lift_lengths AS ll
                INNER JOIN user_lifts AS ul
                        ON ll.lift_id = ul.lift_id
        )
        SELECT
                lt1.athlete_id
                , a.athlete_last_name || ', ' || a.athlete_first_name AS athlete_name
                , lt1.lift_type
                , lt1.weight
                , lt1.num_reps
                , lt1.lift_id AS prev_lift_id
                , lt1.start_time AS prev_lift_start
                , lt2.lift_id AS next_lift_id
                , lt2.start_time AS next_lift_start
                , EXTRACT(EPOCH FROM lt2.start_time - (lt1.start_time + lt1.duration_s * interval '1 second'))::INT AS rest_s
        FROM lift_tot AS lt1
        INNER JOIN lift_tot AS lt2
                ON lt1.lift_id+1 = lt2.lift_id
        INNER JOIN athletes AS a
                ON a.athlete_id = lt1.athlete_id
        ORDER BY lt1.start_time DESC";

        $results = DB::select(DB::raw($query));

        return $results;
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
        $liftTypes = LiftType::select('name_display')->groupBy('name_display')->get()->sortBy('name_display');
        $typeCount = array();
        $realLifts = Lift::where('test_lift', '=', null)->get();
        $editedRepCount = $realLifts->sum('final_num_reps');
        $naturalRepCount = $realLifts->where('final_num_reps', '=', null)->sum('init_num_reps');
        $repCount = $editedRepCount + $naturalRepCount;

        foreach($liftTypes as $liftType) :
            $name = $liftType->name_display;
            $typeCount[$name] = Lift::whereLiftType($name)->where('test_lift', '=', null)->count();
        endforeach;


        // Create array to return data
        $lifts = array(
            'total' => Lift::where('test_lift', '=', null)->count(),
            'typeCount' => $typeCount,
            'repCount' => $repCount
        );
        return $lifts;
    }
        
}
