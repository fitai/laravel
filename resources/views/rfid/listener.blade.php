@extends('layouts.app')

@section('title', 'RFID Listener')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading"><h1>RFID</h1></div>
                <div class="flexbox column flexcenter verticalcenter">
                    <h2>Swipe your bracelet on the device to login.</h2>
                    <div>Listening to collarID:</div>
                    <form id="rfid-login" method="POST" action="/rfid/login">
                        {{ csrf_field() }}
                        <input id="rfid" type="text" name="rfid" class="hidden">
                        <select id="collarID" type="text" name="collarID">
                            <option value=""></option>
                            @foreach ($collars as $collar)
                                <option value="{{ $collar->collar_id }}" @if (isset($collarID) && ($collarID == $collar->collar_id)) selected @endif>{{ $collar->collar_id }}</option>
                            @endforeach
                        </select>
                        <input type="submit" class="hidden">
                    </form>
                </div>
                
            </div>
        </div>
    </div>
</div>
@endsection

@section('pagescripts')
<script>
    console.log('Listening for RFID logins on collarID: {{ $collarID }}');

    // Socket.io listener
    socket.on('rfid', function(data) {
        
        // Parse data
        var packet = JSON.parse(data);
        console.log(packet.collar_id);

        // Check if collar_id being sent matches the one being listened to
        if (packet.collar_id == $('#collarID').val()) {
            // Update form fields
            $('#rfid').val(packet.rfid);
            $('#collarID').val(packet.collar_id);

            // Submit the form
            $('#rfid-login').submit();
        }
        
    });
</script>
@endsection
