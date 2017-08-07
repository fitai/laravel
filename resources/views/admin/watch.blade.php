@extends('layouts.app')

@section('title', 'Admin - Watch')

@section('content')
<h1>Admin - Watch</h1>
{{-- <div id="overlay" class="overlay">
	<div class="content center">
		<form id="lift-new" class="lift new" @submit.prevent="newLift">
			<p>
				<label>Tracker: </label>
				<select name="trackerID" required v-model="trackerID">
					@foreach ($trackers as $tracker)
						<option value="{{ $tracker->tracker_id }}">{{ $tracker->tracker_id }}</option>
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
				<input name="liftReps" type="number" min="1" required v-model="repCount">
			</p>
			<input name="athleteID" type="hidden" value="{{ Auth::id() }}">
			<p>
				<button id="lift-new-submit">Submit</button><br>
				<a href="{{ route('home') }}">Cancel</a>
			</p>
		</form>
	</div>
</div> --}}
<div id="connect_string"></div>
<lift-data :athlete-i-d="{{ Auth::id() }}" :lift-weight="liftWeight" :lift-type="liftType" :tracker-active="trackerActive" :max-reps="maxReps" :rep-count="repCount" :tracker-i-d="trackerID" :rfid-tracker-i-d="0" v-on:add-athlete="addAthlete" v-on:set-tracker-id="setTrackerID"></lift-data>
<div class="center currently-watching">
	Currently Watching Tracker: 
	<select name="trackerID" required v-model="trackerID" v-on:change="setAdminTracker">
		@foreach ($trackers as $tracker)
			<option value="{{ $tracker->tracker_id }}">{{ $tracker->tracker_id }}</option>
		@endforeach
	</select>
</div>
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

</script>
<script type="text/javascript"></script>
@endsection