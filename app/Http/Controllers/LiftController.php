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
        $data = [
            'event' => 'Test',
            'data' => [
                'username' => Auth::user()->name,
                'message' => 'testeroni'
            ]
        ];

        Redis::publish('lifts', json_encode($data));

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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Lift  $lift
     * @return \Illuminate\Http\Response
     */
    public function show(Lift $lift)
    {
        //
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
}
