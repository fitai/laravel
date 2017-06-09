@extends('layouts.app')

@section('title', 'Lift Summary')

@section('content')
<h1>Lift Summary</h1>
<lift-summary :summary="{{ $lift }}" :lift-weight="liftWeight" :lift-type="liftType" :lift-comments="liftComments" :rep-count="repCount" v-on:addlift="addLift"></lift-summary>

@endsection