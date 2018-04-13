@extends('layouts.app')

@section('title', 'Schedule View')

@section('body-class', 'schedule-view')

@section('content')
<div class="content center">
	<h1>Currently Scheduled Lifts</h1>
	<div class="lift-option xs-100 md-45 lg-30">
		{{-- <label class="field-title">Athlete Filter</label> --}}
{{-- 		<select name="scheduleAthlete" required>
			@foreach ($athletes as $athlete)
				<option value="{{ $athlete->athlete_id }}" @if(old('scheduleAthlete') == $athlete->athlete_id) {{ 'selected' }} @endif>{{ $athlete->athlete_last_name }}, {{ $athlete->athlete_first_name }}</option>
			@endforeach
		</select> --}}
	</div>
	<div class="schedule-view-container">
		<table class="schedule-view-table">
			<tr class="table-headers">
				<th class="id">ID</th>
				<th class="start">Start Date</th>
				<th class="athlete">Athlete</th>
				<th class="type">Type</th>
				<th class="weight">Weight</th>
				<th class="reps">Reps</th>
				<th class="tracker">Tracker</th>
				<th class="delete">Delete</th>
			</tr>
			@foreach($schedules as $schedule)
				<tr class="lift">
					<td class="id">{{ $schedule->id }}</td>
					<td class="start">{{ $schedule->start_time }}</td>
					<td class="athlete">{{ $schedule->athlete->athlete_last_name }}, {{ $schedule->athlete->athlete_first_name }}</td>
					<td class="type">{{ $schedule->lift_variation}} {{ $schedule->lift_type}} - {{ $schedule->lift_equipment}}</td>
					<td class="weight">{{ $schedule->lift_weight}}</td>
					<td class="reps">{{ $schedule->reps}}</td>
					<td class="tracker">{{ $schedule->tracker_id}}</td>
					<td class="delete"><a href="{{ route('lift.schedule.delete', ['id' => $schedule->id]) }}">Delete</a></td>
				</tr>
			@endforeach
		</table>
	</div>
</div>
<div id="spinner-overlay" class="spinner-overlay flexbox column flexcenter verticalcenter hidden">
	<div class="spinner">
		<div class="rect1"></div><div class="rect2"></div><div class="rect3"></div><div class="rect4"></div><div class="rect5"></div>
	</div>
</div>

@endsection


@section('pagescripts')

@endsection