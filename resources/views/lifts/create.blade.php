@extends('layouts.app')

@section('title', 'New Lift')

@section('content')
<div id="overlay" class="overlay">
	<div class="content center">
		<form id="lift-new" class="lift new" @submit.prevent="newLift">
			<p>
				<label>Tracker: </label>
				<select name="collarID" required v-model="collarID">
					@foreach ($collars as $collar)
						<option value="{{ $collar->collar_id }}">{{ $collar->collar_id }}</option>
					@endforeach
				</select>
			</p>
			<p>
				<label>Type: </label>
				<select name="liftType" required v-model="liftType">
					@foreach (get_lift_types() as $type)
						<option value="{{ $type->name_display }}">{{ $type->name_display }}</option>
					@endforeach
				</select>
			</p>
			<p>
				<label>Weight: </label>
				<input name="liftWeight" type="number" min="1" required v-model="liftWeight">
			</p>
			<p>
				<label>Reps: </label>
				<input name="liftReps" type="number" min="1" required v-model="maxReps">
			</p>
			<input name="athleteID" type="hidden" value="{{ Auth::id() }}">
			<p>
				<button id="lift-new-submit">Submit</button><br>
				<a href="{{ route('home') }}">Cancel</a>
			</p>
		</form>
	</div>
</div>
<div id="end-lift" class="reset-reps end-lift" v-on:click="endLift">End Lift</div>
<div id="connect_string"></div>
<lift-data :athlete-i-d="{{ Auth::id() }}" :lift-weight="liftWeight" :lift-type="liftType" :collar-active="collarActive" :max-reps="maxReps" :rep-count="repCount" :collar-i-d="collarID" :rfid-collar-i-d="{{ $rfidCollarID }}" v-on:add-athlete="addAthlete" v-on:set-collar-id="setCollarID"></lift-data>
<h1>New Lift</h1>
<div class="flexbox charts-container">
	<div id="chart_div" class="chart"></div>
	<div id="chart_column" class="chart"></div>
</div>
<div id="liftID" class="hidden">
</div>
<div id="velocity_chart" style="width: 100%; height: 500px"></div>
@endsection



@section('pagescripts')

{{-- Google Charts --}}
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript" src="{{ asset('js/charts.js') }}"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.15.1/jquery.validate.js"></script>
<script>
var connectDiv = document.getElementById("connect_string");
var gauge = document.getElementById("chart_div");
var athleteID = "{{ Auth::user()->athlete->athlete_id }}";
// connectDiv.innerHTML="attempting to establish connection...<br>";

// connectDiv.innerHTML=location.port;

// var conn = new WebSocket('ws://52.204.229.101:8080');
// connectDiv.innerHTML="did something...<br>";
// conn.onopen = function(e) {
//     console.log("Connection established!");
//     connectDiv.innerHTML="Connection successful<br>";
// };

// conn.onmessage = function(e) {
// 	var values = JSON.parse(e.data);
// 	if (values.athleteID == athleteID) {
// 		console.log(e.data);
// 		connectDiv.innerHTML=e.data;
// 		updateGauge(values.velocity);
// 		updateLine(values.velocity);
// 		updateColumn(values.power);
// 		updateReps(values.repCount);
// 		updateActive(values.active);
// 	}
// };
// connectDiv.innerHTML="\nDone!";


// // Socket.io listener
// socket.on('lifts', function(data) {
// 	var now = new Date().getTime();
//     console.log(data + ' - time: ' +  now);

//     // Parse data

//     var packet = JSON.parse(data);
    
// });

$('form#lift-new').validate();

// Add delay if athlete is Tim
if (athleteID == 4) {
	@php
		if (Request::get('td')) :
			echo "secDelay = ".Request::get('td').";";
		else :
			echo "secDelay = 0;";
		endif;
	@endphp
}


</script>
@endsection