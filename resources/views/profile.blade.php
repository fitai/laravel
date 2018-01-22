@extends('layouts.app')

@section('title', 'Home')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Profile</div>

                <div class="panel-body">
                    {{-- @php $athlete = get_athlete(); @endphp --}}
                    Athlete Name: {{ get_athlete_name() }}<br> 
                    Age: {{ get_athlete_age() }}<br>   
                    Gender: {{ get_athlete_gender() }}<br> 
{{--                     School: {{ get_athlete_school() }}<br>
                    Coach: {{ get_coach_name() }}<br>
                    Team: {{ get_athlete_team_name() }}<br>
                    Team ID: {{ get_athlete_team_id() }}<br>
                    Next Lift: {{ print_r(get_next_lift()) }} --}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
