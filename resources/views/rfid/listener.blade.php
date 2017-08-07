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
                    <div>Listening to trackerID:</div>
                    <form id="rfid-login" method="POST" action="/rfid/login">
                        {{ csrf_field() }}
                        <input id="rfid" type="text" name="rfid" class="hidden">
                        <select id="trackerID" type="text" name="trackerID">
                            <option value=""></option>
                            @foreach ($trackers as $tracker)
                                <option value="{{ $tracker->tracker_id }}" @if (isset($trackerID) && ($trackerID == $tracker->tracker_id)) selected @endif>{{ $tracker->tracker_id }}</option>
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
    console.log('Listening for RFID logins on trackerID: {{ $trackerID }}');

    // Socket.io listener
    socket.on('rfid', function(data) {
        
        // Parse data
        var packet = JSON.parse(data);
        console.log(packet.tracker_id);

        // Check if tracker_id being sent matches the one being listened to
        if (packet.tracker_id == $('#trackerID').val()) {
            // Update form fields
            $('#rfid').val(packet.rfid);
            $('#trackerID').val(packet.tracker_id);

            // Submit the form
            $('#rfid-login').submit();
        }
        
    });
</script>
@endsection
