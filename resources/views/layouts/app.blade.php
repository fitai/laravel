<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') | {{ config('app.name', 'Fit A.I') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/webfont/webfont.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Montserrat|Open+Sans|Roboto" rel="stylesheet">


    <!-- Scripts -->
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>
</head>
<body>
    <div id="app">
        <div id="body-content" class="body @yield('body-class')">
            <nav id="nav-menu">
                <div class="container">
                    <div id="close-menu" class="close-menu">
                        &times;
                    </div>
                    <div class="nav-items">
                        <div class="{{ (\Request::route()->getName() == 'home') ? 'active' : '' }}">
                            <a href="{{ route('home') }}">
                                <i class="dripicons-home"></i>Home
                            </a>
                        </div>
                        <div class="{{ (\Request::route()->getName() == 'lift') ? 'active' : '' }}">
                            <a href="{{ route('lift') }}">
                                <i class="dripicons-clock"></i><span>Lift</span>
                            </a>
                        </div>
                        <div class="{{ (\Request::route()->getName() == 'profile') ? 'active' : '' }}">
                            <a href="{{ route('profile') }}">
                                <i class="dripicons-user-id"></i>Profile
                            </a>
                        </div>
                        <div class="{{ (\Request::route()->getName() == 'export') ? 'active' : '' }}">
                            <a href="{{ route('export') }}">
                                <i class="dripicons-export"></i>Export
                            </a>
                        </div>
                        <div class="{{ (\Request::route()->getName() == 'settings') ? 'active' : '' }}">
                            <a href="{{ route('settings') }}">
                                <i class="dripicons-gear"></i>Settings
                            </a>
                        </div>
                        <div class="{{ (\Request::route()->getName() == 'switch') ? 'active' : '' }}">
                            <a href="{{ route('switch') }}">
                                <i class="dripicons-user-group"></i>Switch
                            </a>
                        </div>
                        <div class="{{ (\Request::route()->getName() == 'rfid.listener') ? 'active' : '' }}">
                            <a href="{{ route('rfid.listener') }}">
                                <i class="dripicons-user-group"></i>RFID
                            </a>
                        </div>
                        @if (Auth::user()->admin)
                            <div class="{{ (\Request::route()->getName() == 'admin') ? 'active' : '' }}">
                                <a href="{{ route('admin') }}">
                                    <i class="dripicons-code"></i>Admin
                                </a>
                            </div>
                        @endif
                    </div>
                    <div class="nav-footer">
                        <div id="time-since-last-lift" class="time-since">You completed your last lift:<br><span>@{{ timeSinceLastLift }}</span></div>
                        <div id="menu-login" class="menu-login">
                            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Log Out</a><br>
                            {{ get_athlete_name() }}
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        </div>
                    </div>
                </div>
            </nav>
            <div id="main">
                <header>
                    <a href="{{ route('home') }}">
                        <img id="logo" src="/images/fitai-logo-black.svg">
                    </a>    
                    <div id="nav-hamburger" class="nav-hamburger hamburger-menu">
                        <i class="dripicons-menu"></i>
                    </div>
                </header>
                <div id="dynamic-content">
                    @if (count($errors))
                        <ul class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                    <div id="time-since-last-lift-mobile" class="time-since-mobile">You completed your last lift:<br><span>@{{ timeSinceLastLift }}</span></div>
                    @yield('content')
                    <div id="js-errors" class="js-errors" v-if="jsErrors" v-cloak>
                        <div class="alert alert-danger" @click="clearError">
                            @{{ jsErrors }}
                        </div>
                    </div>
                </div>
            </div> <!-- #main -->
        </div> <!-- #body-content -->
    </div> <!-- #app -->

    <!-- Scripts -->

    {{-- Socket.io --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.0.1/socket.io.js"></script>
    <script>
        // var socket = io('http://192.168.10.10:3000'); // Connect to local server
        var socket = io('http://dev.fitai.co:3000'); // Connect to AWS

        if (socket) {
            console.log('Socket.io connected');
        }
    </script>

    {{-- Project Scripts --}}
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/global.js') }}"></script>
    <script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    {{-- Page specific scripts --}}
    @yield('pagescripts')
</body>
</html>
