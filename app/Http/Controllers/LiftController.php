<?php

namespace App\Http\Controllers;

use Auth;
use App\Lift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class LiftController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
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
        // Publish event with Redis
        // $data = [
        //     'event' => 'Test',
        //     'data' => [
        //         'username' => Auth::user()->name,
        //         'message' => 'testeroni'
        //     ]
        // ];

        // Redis::publish('lifts', json_encode($data));

        return view('lifts/create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        // Pass data to Redis
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

        // dd(json_encode($pythonArray));

        // $arg = "/home/kyle/virtualenvs/fitai/bin/python /opt/fitai_controller/comms/update_redis.py -v -j ".escapeshellarg(json_encode($pythonArray));

        // $cmd = "ssh -i /home/vagrant/.ssh/fitai-dev.pem patrick@52.15.200.179 ".escapeshellarg($arg);

        // $pythonCmd = escapeshellcmd($cmd);

        // dd($cmd);

        // // Run on local build
        // $pythonExec = exec($pythonCmd);

        // Run on AWS
        $pythonExec = exec("/home/kyle/virtualenvs/fitai/bin/python /opt/fitai_controller/comms/update_redis.py -v -j '".json_encode($pythonArray)."'"); 

        return $pythonExec;

        // dd('ssh -i /home/vagrant/.ssh/fitai-dev.pem patrick@52.15.200.179 "/home/kyle/virtualenvs/fitai/bin/python /opt/fitai_controller/comms/update_redis.py -v -j \''.json_encode($pythonArray).'\'"');

        // dd('ssh -i /home/vagrant/.ssh/fitai-dev.pem patrick@52.15.200.179 "/home/kyle/virtualenvs/fitai/bin/python /opt/fitai_controller/comms/update_redis.py -v -j '.escapeshellarg(json_encode($pythonArray)).'"');
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

        $pythonResponse = "";

        // $pythonExec = exec("/home/jbrubaker/anaconda2/envs/fitai/bin/python /var/opt/python/fitai_controller/comms/update_redis.py -j '".json_encode($lift)."'", $pythonResponse);

        return view('lifts/summary', compact('lift'));
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
    public function update(Request $request, Lift $lift)
    {
        //
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

        $pythonExec = exec("/home/kyle/virtualenvs/fitai/bin/python /opt/fitai_controller/comms/update_redis.py -v -j '".json_encode($pythonArray)."'"); 

        return $pythonExec;
    }
        
}
