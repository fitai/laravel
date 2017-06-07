@extends('layouts.app')

@section('title', 'Switch')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading"><h1>Switch</h1></div>
                    @if (count($errors))
                        <ul class="alert-danger">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                <div id="athlete-search">
                    <label for="athlete_name">Search Athlete: </label>
                    <input name="athlete_name" v-model="search"> <button id="search-athlete" class="small">Search</button>
                </div>
                <div class="panel-body">
                    <div id="athlete-switch" class="athlete-switch" >
                        <team @loadteam="getTeam" :filteredteam="filteredteam"></team>
                    </div>               
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('pagescripts')
@endsection
