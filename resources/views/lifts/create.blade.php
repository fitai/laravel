@extends('layouts.app')

@section('title', 'New Lift')

@section('body-class', 'new-lift')

@section('content')
<div id="lift-overlay" class="lift-overlay">
	<div class="content center">
		<form id="lift-new" class="lift new" @submit.prevent="newLift">
			<lift-select :type-options="{{ $typeOptions }}" :variation-options="{{ $variationOptions }}" :equipment-options="{{ $equipmentOptions }}" :options=" {{ $options }}" v-on:updatelifttype="updateLiftType"></lift-select>
			<h3>Lift Details</h3>
			<div class="flexbox wrap">
				<div class="lift-option xs-30">
					<label class="field-title">Tracker</label>
					<select name="collarID" required v-model="collarID">
						@foreach (get_team_collars() as $collar)
							<option value="{{ $collar->collar_id }}">{{ $collar->collar_id }}</option>
						@endforeach
					</select>
				</div>
				<div class="lift-option xs-30">
					<label class="field-title">Weight</label>
					<input name="liftWeight" type="number" min="1" required v-model="liftWeight">
				</div>
				<div class="lift-option xs-30">
					<label class="field-title">Reps</label>
					<input name="liftReps" type="number" min="1" required v-model="repCount">
				</div>
				<input name="athleteID" type="hidden" value="{{ Auth::id() }}">
				<div class="lift-option xs-100 lift-actions">
					<button id="lift-new-submit" class="lift-new-submit"><span class="button__inner">Submit</span></button>
					<a href="{{ route('home') }}">Cancel</a>
				</div>
			</div>
		</form>
	</div>
</div>
<div id="spinner-overlay" class="spinner-overlay flexbox column flexcenter verticalcenter hidden">
	<div class="spinner">
		<div class="rect1"></div><div class="rect2"></div><div class="rect3"></div><div class="rect4"></div><div class="rect5"></div>
	</div>
</div>
<div id="end-lift" class="reset-reps end-lift" v-on:click="endLift">End Lift</div>
    <ul class="alert alert-warning" v-if='collarActive == false'>
        <li class="center"><b>WARNING:</b> Your selected collar is not active and is not recording data!</li>
    </ul>
<div id="connect_string"></div>
<lift-data :athlete-i-d="{{ Auth::id() }}" :lift-weight="liftWeight" :lift-type="liftType" :collar-active="collarActive" :max-reps="maxReps" :rep-count="repCount" :collar-i-d="collarID" :rfid-collar-i-d="{{ $rfidCollarID }}" v-on:add-athlete="addAthlete" v-on:set-collar-id="setCollarID"></lift-data>
<div class="lift-section velocity-title">
	<h2 class="center">Velocity</h2>
	<div class="label">m/s <sup>2</sup></div>
</div>
<div class="velocity-numbers flexbox">
	<div class="previous-lift">
		<div class="number">0.5</div>
		<div class="label">Previous Lift</div>
	</div>
	<div class="current-lift">
		<div id="current-lift-number" class="number">@{{ currentVelocity }}</div>
		<div class="label">Current Lift</div>
	</div>
	<div class="target">
		<div class="number">0.5</div>
		<div class="label">Target</div>
	</div>
</div>
<div class="flexbox charts-container" style="display: none;">
	<div id="chart_div" class="chart"></div>
	<div id="chart_column" class="chart"></div>
</div>
<div id="liftID" class="hidden">
</div>
<div id="velocity_chart" style="width: 100%; height: 350px"></div>
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