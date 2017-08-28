@extends('layouts.app')

@section('title', 'Admin')

@section('content')
<h1>Admin</h1>
<div>
	<h3>Links</h3>
	<ul>
		<li><a href="{{ route('admin.watch') }}">Device Listener</a></li>
		<li><a href="{{ route('admin.athlete.rest') }}">Athlete Rest</a></li>
	</ul>
</div>

<h2>Registered</h2>
<ul class="admin-list">
	<li><span>Athletes:</span> {{ $registered->athletes }}</li>
	<li><span>Coaches:</span> {{ $registered->coaches }}</li>
	<li>
		<span>Teams</span>
		<ul>
			@foreach ($registered->teams as $team => $val)
				<li>{{ ucwords($team) }}: {{ $val }}</li>
			@endforeach
		</ul>
	</li>
	<li><span>Clients:</span> {{ $registered->clients }}</li>
</ul>

<h2>Collected</h2>
<ul class="admin-list">
	<li><span>Lifts:</span> {{ $lifts['total'] }}</li>
	<li>
		<span>Lift Type</span>
		<ul>
			@foreach ($lifts['typeCount'] as $type => $count)
				<li>{{ $type }}: {{ $count }}</li>
			@endforeach
		</ul>
	</li>
</ul>

@endsection