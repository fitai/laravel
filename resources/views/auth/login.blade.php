<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login | Fit A.I</title>

    <link rel="stylesheet" type="text/css" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/webfont/webfont.css') }}" />
    <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">

</head>
<body>
<div id="body-content" class="body-login" >
    <div id="main">
        <div id="login">
            <div class="login-logo">
                <img src="{{ asset('images/fitai-logo-white.svg') }}">
            </div>
            <form action="{{ route('login') }}" method="POST" name="login">
                {{ csrf_field() }}
                <div class="{{ $errors->has('email') ? ' form-error' : '' }}">
                    @if ($errors->has('email'))
                        <span class="help-block">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="form-field">
                    <i class="dripicons-user"></i>
                    <input type="text" name="email" placeholder="Username" value="{{ old('email') }}" required autofocus />
                </div>
                <div class="form-field">
                    <i class="dripicons-lock"></i>
                    <input type="password" name="password" placeholder="Password" required /><br>
                </div>
                <div class="form-field submit">
                    <input name="submit" type="submit" value="Get Started" />
                </div>
                <div class="login-help">
                    <a href="">Need Help?</a>
                </div>
            </form>
            <!--<p>Not registered yet? <a href='registration.php'>Register Here</a></p>-->
        </div>
    </div> <!-- #main -->
</div> <!-- #body-content -->
</body>
</html>