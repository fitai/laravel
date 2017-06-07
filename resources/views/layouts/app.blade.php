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
    <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">

    <!-- Scripts -->
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>
</head>
<body>
    <div id="app">
        <div id="body-content" class="body">
            <nav id="nav-menu">
                <div class="container">
                    <div id="close-menu" class="close-menu">
                        &times;
                    </div>
                    <div class="nav-items">
                        <div>
                            <a href="{{ route('home') }}">
                                <i class="dripicons-home"></i>Home
                            </a>
                        </div>
                        <div>
                            <a href="{{ route('now') }}">
                                <i class="dripicons-clock"></i><span>Now</span>
                            </a>
                        </div>
                        <div>
                            <a href="{{ route('profile') }}">
                                <i class="dripicons-user-id"></i>Profile
                            </a>
                        </div>
                        <div>
                            <a href="{{ route('export') }}">
                                <i class="dripicons-export"></i>Export
                            </a>
                        </div>
                        <div>
                            <a href="{{ route('settings') }}">
                                <i class="dripicons-gear"></i>Settings
                            </a>
                        </div>
                        <div>
                            <a href="{{ route('switch') }}">
                                <i class="dripicons-user-group"></i>Switch
                            </a>
                        </div>
                        @if (Auth::user()->admin)
                            <div>
                                <a href="{{ route('admin') }}">
                                    <i class="dripicons-code"></i>Admin
                                </a>
                            </div>
                        @endif
                    </div>
                    <div class="nav-footer">
                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Log Out</a><br>
                        {{ get_athlete_name() }}
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            {{ csrf_field() }}
                        </form>
                    </div>
                </div>
            </nav>
            <div id="main">
                <header>
                    <img id="logo" src="/images/fitai-logo-black.svg">
                    <div id="nav-hamburger" class="nav-hamburger hamburger-menu">
                        <i class="dripicons-menu"></i>
                    </div>
                </header>
                <div id="dynamic-content">
                    @yield('content')
                </div>
            </div> <!-- #main -->
        </div> <!-- #body-content -->
    </div> <!-- #app -->

    <!-- Scripts -->

    {{-- Socket.io --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.0.1/socket.io.js"></script>
    <script>
        // var socket = io('http://192.168.10.10:3000'); // Connect to local server
        var socket = io('http://52.204.229.101:3000'); // Connect to AWS

        // // Sample of socket.io without Redis communication
        // socket.on('news', function(data) {
        //     console.log(data);
        //     socket.emit('my other event', { my: 'data' });
        // });

        socket.on('lifts', function(data) {
            console.log(data);
        });
    </script>

    {{-- Project Scripts --}}
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/global.js') }}"></script>

    {{-- Page specific scripts --}}
    @yield('pagescripts')
</body>
</html>
