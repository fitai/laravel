<?php

namespace App\Http\Controllers;

use Auth;
use App\Lift;
use App\Athlete;
use App\LiftType;
use Carbon\Carbon;
use App\LiftSchedule;
use phpseclib\Net\SSH2;
use phpseclib\Crypt\RSA;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class LiftController extends Controller
{
    private $ssh;

    public function __construct()
    {
        $this->middleware('auth');

        // // Connect to AWS server via SSH with key
        // $ssh = new SSH2('18.221.103.145');
        // $key = new RSA();
        // $key->loadKey(file_get_contents('/home/vagrant/.ssh/fitai-dev.pem'));
        // if (!$ssh->login('patrick', $key)) {
        //     exit('Login Failed');
        // }

        // $this->ssh = $ssh;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // Get all trackers for the User's team
        $trackers = Auth::user()->athlete->team->trackers;

        // Set default $rfidTrackerID
        $rfidTrackerID = 0;

        // Get lift options
        $typeOptions = LiftType::select('type')->groupBy('type')->get();
        $variationOptions = LiftType::select('variation')->groupBy('variation')->get();
        $equipmentOptions = LiftType::select('equipment')->groupBy('equipment')->get();
        $options = LiftType::select('type', 'variation', 'equipment')->groupBy('type', 'variation', 'equipment')->get();

        // Check if trackerID is passed by RFID
        if ($request->rfidTrackerID) :
            $rfidTrackerID = $request->rfidTrackerID;
        endif;

        return view('lifts/create', compact('rfidTrackerID', 'typeOptions', 'variationOptions', 'equipmentOptions', 'options', 'trackers')); // Working device

    }
    public function test(Request $request)
    {
        // Get all trackers for the User's team
        $trackers = Auth::user()->athlete->team->trackers;

        $typeOptions = LiftType::select('type')->groupBy('type')->get();
        $variationOptions = LiftType::select('variation')->groupBy('variation')->get();
        $equipmentOptions = LiftType::select('equipment')->groupBy('equipment')->get();
        $options = LiftType::select('type', 'variation', 'equipment')->groupBy('type', 'variation', 'equipment')->get();

        // Set default $rfidTrackerID
        $rfidTrackerID = 0;

        // Check if trackerID is passed by RFID
        if ($request->rfidTrackerID) :
            $rfidTrackerID = $request->rfidTrackerID;
        endif;

        return view('lifts/create-numbers', compact('typeOptions', 'variationOptions', 'equipmentOptions', 'options')); // New design device

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        // Create array to send to Python
        $pythonArray = array(
            "tracker_id" => $request->trackerID,
            "athlete_id" => Auth::user()->athlete->athlete_id,
            "lift_id" => 'None',
            "lift_start" => "None",
            "lift_type" => $request->liftType,
            "lift_weight" => $request->liftWeight,
            "weight_units" => "lbs",
            "init_num_reps" => $request->maxReps,
            "calc_reps" => 0,
            "threshold" => "None",
            "curr_state" => 'rest',
            "active" => True
        );


        $exec = "/home/kyle/virtualenvs/fitai/bin/python /opt/fitai_controller/comms/update_redis.py -v -j '".json_encode($pythonArray)."'";

        // dd($exec);

        // // Run on local build
        // $pythonExec = $this->ssh->exec("/home/kyle/virtualenvs/fitai/bin/python /opt/fitai_controller/comms/update_redis.py -v -j '".json_encode($pythonArray)."'");
        // $python = explode(PHP_EOL, $pythonExec); // Create array by exploding end of line


        // Run on AWS
        $pythonExec = exec("/home/kyle/virtualenvs/fitai/bin/python /opt/fitai_controller/comms/update_redis.py -v -j '".json_encode($pythonArray)."'", $python); 

        // dd($python);
        
        // Get liftID from python response  
        try {
                  
            $liftID = explode(": ", $python[5]); // Explode the line with "lift_id: ###"


        } catch (Exception $e) {

            // return response()->json(['message' => $e->getMessage()]);
            return response($e->getMessage(), $e->getStatusCode());
        }

        return array(
            "exec" => $exec, 
            "python" => $python, 
            "liftID" => $liftID[1]
        );

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Lift  $lift
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $lift = Lift::where('lift_id', $id)->with('typeData')->first();

        if (!$lift) {
            return redirect()->back()->withErrors('Lift not found');
        }

        $pythonArray = array(
            "tracker_id" => $lift->tracker_id,
            "lift_id" => $id,
            "active" => false
        );

        // // Run on local build
        // $pythonExec = $this->ssh->exec("/home/kyle/virtualenvs/fitai/bin/python /opt/fitai_controller/comms/update_redis.py -j '".json_encode($pythonArray)."'");
        // $pythonExplode= explode(PHP_EOL, $pythonExec);
        // $pythonResponse = $pythonExplode[2];

        // Run on AWS
        $pythonResponse = exec("/home/kyle/virtualenvs/fitai/bin/python /opt/fitai_controller/comms/update_redis.py -j '".json_encode($pythonArray)."'");

        // Get LiftTypes
        $liftTypes = LiftType::all()->sortBy('name_display');

        return view('lifts/summary', compact('lift', 'pythonResponse', 'liftTypes'));
    }

       

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Lift  $lift
     * @return \Illuminate\Http\Response
     */
    public function edit(Lift $lift)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Lift  $lift
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $this->validate($request, [
            'lift_id' => 'required|numeric',
            'lift_type' => 'required',
            'lift_weight' => 'required|numeric',
            'final_num_reps' => 'required|numeric',
            'user_comment' => ''
        ]);


        // $lift = Lift::find($request->lift_id);

        // if (!$lift) :
        //     // return response()->json(['message' => 'Lift not found']);
        //     return response('Lift not found', 500);
        // endif;

        try {

            $lift = Lift::find($request->lift_id);

        } catch (Exception $e) {

            return response($e->getMessage(), $e->getStatusCode());

        }


        // Update columns
        $lift->lift_type = $request->lift_type;
        $lift->lift_weight = $request->lift_weight;
        $lift->final_num_reps = $request->final_num_reps;
        $lift->user_comment = $request->user_comment;

        // // Update column in Lift
        // switch ($request->prop) {
        //     case 'liftComments' :
        //         $lift->user_comment = $request->val;
        //         break;
        //     case 'repCount' :
        //         $lift->final_num_reps = $request->val;
        //         break;
        //     case 'liftWeight' :
        //         $lift->lift_weight = $request->val;
        //         break;
        //     case 'liftType' :
        //         $lift->lift_type = $request->val;
        //         break;
        // }

        // Save changes to $lift
        try {

            $lift->save();
            return "Lift $request->lift_id updated successfully";

        } catch (Exception $e) {

            return response($e->getMessage(), $e->getStatusCode());

        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Lift  $lift
     * @return \Illuminate\Http\Response
     */
    public function destroy(Lift $lift)
    {
        //
    }

    public function endLift(Request $request) 
    {
        $pythonArray = array(
            "tracker_id" => $request->trackerID,
            "active" => false
        );

        $exec = "execution string: /home/kyle/virtualenvs/fitai/bin/python /opt/fitai_controller/comms/update_redis.py -v -j '".json_encode($pythonArray)."'";

        // // Run on local build
        // $pythonExec = $this->ssh->exec("/home/kyle/virtualenvs/fitai/bin/python /opt/fitai_controller/comms/update_redis.py -v -j '".json_encode($pythonArray)."'");

        // Run on AWS
        $pythonExec = exec("/home/kyle/virtualenvs/fitai/bin/python /opt/fitai_controller/comms/update_redis.py -v -j '".json_encode($pythonArray)."'"); 

        // Add timestamp to ended_at
        Lift::where('lift_id', $request->liftID)->update(array(
            'ended_at' => Carbon::now(),
            'calc_reps' => $request->calcReps
        ));

        // Mark lift as test if required
        if ($request->testLift == true) :
            Lift::where('lift_id', $request->liftID)->update(array('test_lift' => true));
        endif;

        // Mark scheduledLift complete if required
        if ($request->scheduledLiftID) :
            LiftSchedule::whereId($request->scheduledLiftID)->update(array('end_time' => Carbon::now()));
        endif;

        return array($exec, $pythonExec, $request->testLift);
    }

    public function killLift($id) 
    {
        $pythonArray = array(
            "tracker_id" => $id,
            "active" => false
        );

        $exec = "execution string: /home/kyle/virtualenvs/fitai/bin/python /opt/fitai_controller/comms/update_redis.py -v -j '".json_encode($pythonArray)."'";

        // // Run on local build
        // $pythonExec = $this->ssh->exec("/home/kyle/virtualenvs/fitai/bin/python /opt/fitai_controller/comms/update_redis.py -v -j '".json_encode($pythonArray)."'");

        // Run on AWS
        $pythonExec = exec("/home/kyle/virtualenvs/fitai/bin/python /opt/fitai_controller/comms/update_redis.py -v -j '".json_encode($pythonArray)."'"); 

        return array($exec, $pythonExec);
    }

    // Get the LiftType data
    public function getTypeData(Request $request) 
    {
        $nameSafe = $request->nameSafe;

        $type = LiftType::where('name_safe', $nameSafe)->first();

        return $type;
    }

    // Get the last lift of designated Athlete
    public function getLastLift($id = null) 
    {
        if ($id) :
            $lift = Athlete::find($id)->lastLift();
        else:
            $lift = Auth::user()->athlete->lastLift();
        endif;

        return $lift;
    }

    // Get the next lift of designated Athlete
    public function getNextLift($id = null) 
    {
        // Check for scheduled lifts
        if ($id) :
            $scheduled = Athlete::find($id)->nextLift();
        else:
            $scheduled = Auth::user()->athlete->nextLift();
        endif;

        return $scheduled;
    }
    
    // UI for creating a new scheduled lift 
    public function schedule()  
    {
        // Get all athletes for User's team
        $athletes = Auth::user()->athlete->team->athletes->sortBy('athlete_last_name');

         // Get all trackers for the User's team
        $trackers = Auth::user()->athlete->team->trackers;

        // Set default $rfidTrackerID
        $rfidTrackerID = 0;

        // Get lift options
        $typeOptions = LiftType::select('type')->groupBy('type')->get();
        $variationOptions = LiftType::select('variation')->groupBy('variation')->get();
        $equipmentOptions = LiftType::select('equipment')->groupBy('equipment')->get();
        $options = LiftType::select('type', 'variation', 'equipment')->groupBy('type', 'variation', 'equipment')->get();

        return view('lifts/schedule', compact('athletes', 'trackers', 'typeOptions', 'variationOptions', 'equipmentOptions', 'options'));
    }

    // Save scheduled lift to DB
    public function storeSchedule(Request $request) 
    {
        // Validate fields
        $validator = $this->validate($request, [
            'scheduleDate' => 'required|date',
            'scheduleTime' => 'required|date_format:"H:i:s"',
            'scheduleAthlete' => 'required|numeric',
            'type' => 'required',
            'variation' => 'required',
            'equipment' => 'required',
            'trackerID' => 'required',
            'liftWeight' => 'required|numeric',
            'maxReps' => 'required|numeric'
        ]);

        // if ($validator->fails()) {
        //     return redirect()->back()
        //                 ->withErrors($validator)
        //                 ->withInput();
        // }

        $start = $request->scheduleDate." ".$request->scheduleTime;

        // Save changes to $lift
        try {

            $schedule = LiftSchedule::create([
                'athlete_id' => $request->scheduleAthlete,
                'start_time' => $start,
                'lift_type' => $request->type,
                'lift_variation' => $request->variation,
                'lift_equipment' => $request->equipment,
                'lift_weight' => $request->liftWeight,
                'tracker_id' => $request->trackerID,
                'reps' => $request->maxReps

            ]);

            // Get athlete name
            $athlete = Athlete::find($request->scheduleAthlete);
            $name = $athlete->athlete_first_name." ".$athlete->athlete_last_name;

            return redirect()->back()->with('message', 'Lift scheduled for '.$name.' successfully!');

        } catch (Exception $e) {

            return response($e->getMessage(), $e->getStatusCode());

        }

        // // Check if date and time are valid
        // function validateDate($date, $format = 'Y-m-d H:i:s')
        // {
        //     $d = \DateTime::createFromFormat($format, $date);
        //     return $d && $d->format($format) == $date;
        // }

        // $start = $request->scheduleDate." ".$request->scheduleTime;

        // if (validateDate($start)) :

        // else
    }
    
    // Display schedule of team's full schedule
    public function viewSchedule() 
    {
        // Get all athlete IDs for User's team
        $athletes = Auth::user()->athlete->team->athletes()->get(['athlete_id']);

        // Get all future scheduled lifts for team mates
        $schedules = LiftSchedule::whereIn('athlete_id', $athletes)
        ->where([
            ['end_time', '=', null],
            ['start_time', '>=', Carbon::now()]
        ])
        ->orderBy('start_time', 'desc')
        ->with('athlete')
        ->get();

        return view('lifts/schedule-view', compact('schedules'));
        
    }

    // Remove a scheduled lift
    public function deleteSchedule($id) 
    {
        $schedule = LiftSchedule::find($id);

        // Make sure LiftSchedule exists
        if (!$schedule) :

            return redirect()->back()->withErrors(['Scheduled Lift '.$id.' does not exist.']);

        // Make sure user is authorized to delete the LiftSchedule
        elseif ($schedule->athlete->team->team_id !== Auth::user()->athlete->team->team_id) :

            return redirect()->back()->withErrors(['You are not authorized to edit Scheduled Lift '.$id.'.']);

        endif;


        // Try to remove LiftSchedule from database
        try {

            $delete = LiftSchedule::find($id)->delete();

            return redirect()->back()->with('message', 'Scheduled Lift '.$id.' deleted succesfully.');

        } catch (Exception $e) {

            return response($e->getMessage(), $e->getStatusCode());

        }
        
    }
        
        
        
        
}
