<?php

namespace App\Http\Controllers;

use Auth;
use App\Lift;
use App\LiftType;
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
        // $ssh = new SSH2('52.15.200.179');
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
    public function create()
    {
        $typeOptions = LiftType::select('type')->groupBy('type')->get();
        $variationOptions = LiftType::select('variation')->groupBy('variation')->get();
        $equipmentOptions = LiftType::select('equipment')->groupBy('equipment')->get();
        $options = LiftType::select('type', 'variation', 'equipment')->groupBy('type', 'variation', 'equipment')->get();

        return view('lifts/create'); // Working device
        // return view('lifts/create-numbers', compact('typeOptions', 'variationOptions', 'equipmentOptions', 'options')); // New design device

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
            "collar_id" => $request->collarID,
            "athlete_id" => Auth::user()->athlete->athlete_id,
            "lift_id" => 'None',
            "lift_start" => "None",
            "lift_type" => $request->liftType,
            "lift_weight" => $request->liftWeight,
            "weight_units" => "lbs",
            "init_num_reps" => $request->repCount,
            "calc_reps" => 0,
            "threshold" => "None",
            "curr_state" => 'rest',
            "active" => True
        );


        $exec = "/home/kyle/virtualenvs/fitai/bin/python /opt/fitai_controller/comms/update_redis.py -v -j '".json_encode($pythonArray)."'";

        // dd($exec);

        // // Run on local build
        // $pythonExec = $this->ssh->exec("/home/kyle/virtualenvs/fitai/bin/python /opt/fitai_controller/comms/update_redis.py -v -j '".json_encode($pythonArray)."'");

        // $pythonExec = explode(PHP_EOL, $pythonExec); // Create array by exploding end of line
        // $liftID = explode(": ", $pythonExec[4]); // Explode the line with "lift_id: ###"


        // Run on AWS
        $pythonExec = exec("/home/kyle/virtualenvs/fitai/bin/python /opt/fitai_controller/comms/update_redis.py -v -j '".json_encode($pythonArray)."'", $pythonReturn); 

        return array(
            "exec" => $exec, 
            "python" => $pythonExec, 
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
        $lift = Lift::find($id);

        if (!$lift) {
            return ("Lift not found");
        }

        $pythonArray = array(
            "collar_id" => $lift->collar_id,
            "lift_id" => $id,
            "active" => false
        );

        $pythonResponse = $this->ssh->exec("/home/kyle/virtualenvs/fitai/bin/python /opt/fitai_controller/comms/update_redis.py -j '".json_encode($pythonArray)."'");

        // Get LiftTypes
        $liftTypes = LiftType::all();

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
            'prop' => 'required',
            'val' => 'required'
        ]);


        // Get Lift
        $lift = Lift::find($request->lift_id);

        if (!$lift) {
            return ("Lift not found");
        }

        // Update column in Lift
        switch ($request->prop) {
            case 'liftComments' :
                $lift->user_comment = $request->val;
                break;
            case 'repCount' :
                $lift->final_num_reps = $request->val;
                break;
            case 'liftWeight' :
                $lift->lift_weight = $request->val;
                break;
            case 'liftType' :
                $lift->lift_type = $request->val;
                break;
        }

       $lift->save();

       return "Lift $request->lift_id updated successfully";
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
            "collar_id" => $request->collarID,
            "active" => false
        );

        $exec = "execution string: /home/kyle/virtualenvs/fitai/bin/python /opt/fitai_controller/comms/update_redis.py -v -j '".json_encode($pythonArray)."'";

        // // Run on local build
        // $pythonExec = $this->ssh->exec("/home/kyle/virtualenvs/fitai/bin/python /opt/fitai_controller/comms/update_redis.py -v -j '".json_encode($pythonArray)."'");

        // Run on AWS
        $pythonExec = exec("/home/kyle/virtualenvs/fitai/bin/python /opt/fitai_controller/comms/update_redis.py -v -j '".json_encode($pythonArray)."'"); 

        return array($exec, $pythonExec);
    }

    public function killLift($id) 
    {
        $pythonArray = array(
            "collar_id" => $id,
            "active" => false
        );

        $exec = "execution string: /home/kyle/virtualenvs/fitai/bin/python /opt/fitai_controller/comms/update_redis.py -v -j '".json_encode($pythonArray)."'";

        // Run on local build
        // $pythonExec = $this->ssh->exec("/home/kyle/virtualenvs/fitai/bin/python /opt/fitai_controller/comms/update_redis.py -v -j '".json_encode($pythonArray)."'");

        // Run on AWS
        $pythonExec = exec("/home/kyle/virtualenvs/fitai/bin/python /opt/fitai_controller/comms/update_redis.py -v -j '".json_encode($pythonArray)."'"); 

        return array($exec, $pythonExec);
    }
        
}
