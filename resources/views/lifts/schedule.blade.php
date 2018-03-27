@extends('layouts.app')

@section('title', 'Schedule Lift')

@section('body-class', 'schedule-lift')

@section('content')
<div class="content center">
	<h1>Schedule a Lift</h1>
	<div class="lift-schedule-container">
		<form id="lift-schedule" class="lift new" @submit.prevent="newLift">
			<h3>Schedule</h3>
			<div class="flexbox wrap">
				<div class="lift-option xs-100 md-45 lg-30">
					<label class="field-title">Date</label>
					<input id="schedule-date" name="scheduleDate" type="text" required v-model="scheduleDate">
				</div>
				<div class="lift-option xs-100 md-45 lg-30">
					<label class="field-title">Time</label>
					<input id="schedule-time" name="scheduleTime" type="text" required v-model="scheduleTime">
				</div>
			</div>
			<lift-select :type-options="{{ $typeOptions }}" :variation-options="{{ $variationOptions }}" :equipment-options="{{ $equipmentOptions }}" :options=" {{ $options }}" v-on:updatelifttype="updateLiftType" v-on:getnextlift="getNextLift"></lift-select>
			<h3>Lift Details</h3>
			<div class="flexbox wrap">
				<div class="lift-option xs-100 md-45 lg-30">
					<label class="field-title">Tracker</label>
					<select name="trackerID" required v-model="trackerID">
						@foreach ($trackers as $tracker)
							<option value="{{ $tracker->tracker_id }}">{{ $tracker->tracker_id }}</option>
						@endforeach
					</select>
				</div>
				<div class="lift-option xs-100 md-45 lg-30">
					<label class="field-title">Weight</label>
					<input name="liftWeight" type="number" min="1" required v-model="liftWeight">
				</div>
				<div class="lift-option xs-100 md-45 lg-30">
					<label class="field-title">Reps</label>
					<input name="maxReps" type="number" min="1" required v-model="maxReps">
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

@endsection


@section('pagescripts')


<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.15.1/jquery.validate.js"></script>

{{-- jQuery timepicker --}}
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>

<script>

// init jQuery datepicker
$('#schedule-date').datepicker({
	minDate: 0
	maxDate: '+3m'
}); 

// init jQuery timepicker
$('#schedule-time').timepicker({
	timeFormat: 'HH:mm:ss',
	interval: 5
});

// Add validation to form
$('form#lift-schedule').validate();

</script>
@endsection