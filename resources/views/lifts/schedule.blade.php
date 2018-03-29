@extends('layouts.app')

@section('title', 'Schedule Lift')

@section('body-class', 'schedule-lift')

@section('content')
<div class="content center">
	<h1>Schedule a Lift</h1>
	<div class="lift-schedule-container">
		<form id="lift-schedule" class="lift-schedule new" method="POST" action="{{ route('lift.schedule.store') }}">
			{{ csrf_field() }}
			<h3>Date &amp; Time</h3>
			<div class="flexbox wrap">
				<div class="lift-option xs-100 md-45 lg-30">
					<label class="field-title">Date</label>
					<input id="schedule-date" name="scheduleDate" type="text" value="{{ old('scheduleDate') }}" required>
				</div>
				<div class="lift-option xs-100 md-45 lg-30">
					<label class="field-title">Time</label>
					<input id="schedule-time" name="scheduleTime" type="text" value="{{ old('scheduleTime') }}" required>
				</div>
			</div>
			<h3>Athlete</h3>
			<div class="flexbox wrap">
				<div class="lift-option xs-100 md-45 lg-30">
					<label class="field-title">Athlete</label>
					<select name="scheduleAthlete" required>
						@foreach ($athletes as $athlete)
							<option value="{{ $athlete->athlete_id }}" @if(old('scheduleAthlete') == $athlete->athlete_id) {{ 'selected' }} @endif>{{ $athlete->athlete_last_name }}, {{ $athlete->athlete_first_name }}</option>
						@endforeach
					</select>
				</div>
			</div>
			<lift-select :type-options="{{ $typeOptions }}" :variation-options="{{ $variationOptions }}" :equipment-options="{{ $equipmentOptions }}" :options=" {{ $options }}" :type-old="'{{ old('type') }}'" :variation-old="'{{ old('variation') }}'" :equipment-old="'{{ old('equipment') }}'" v-on:updatelifttype="updateLiftType" v-on:getnextlift="getNextLift"></lift-select>
			<h3>Lift Details</h3>
			<div class="flexbox wrap">
				<div class="lift-option xs-100 md-45 lg-30">
					<label class="field-title">Tracker</label>
					<select name="trackerID" required>
						@foreach ($trackers as $tracker)
							<option value="{{ $tracker->tracker_id }}" @if(old('trackerID') == $tracker->tracker_id) {{ 'selected' }} @endif>{{ $tracker->tracker_id }}</option>
						@endforeach
					</select>
				</div>
				<div class="lift-option xs-100 md-45 lg-30">
					<label class="field-title">Weight</label>
					<input name="liftWeight" type="number" min="1" value="{{ old('liftWeight') }}" required>
				</div>
				<div class="lift-option xs-100 md-45 lg-30">
					<label class="field-title">Reps</label>
					<input name="maxReps" type="number" min="1" value="{{ old('maxReps') }}" required>
				</div>
				{{-- <input name="athleteID" type="hidden" value="{{ Auth::id() }}"> --}}
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
    dateFormat: 'yy-mm-dd',
    minDate: 0,
    maxDate: '+3m'
}); 

// init jQuery timepicker
$('#schedule-time').timepicker({
	timeFormat: 'HH:mm:ss',
	interval: 15
});

// Add validation to form
$('form#lift-schedule').validate();

</script>
@endsection