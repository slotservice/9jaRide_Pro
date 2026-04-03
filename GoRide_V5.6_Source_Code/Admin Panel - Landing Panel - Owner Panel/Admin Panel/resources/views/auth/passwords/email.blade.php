<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', '9jaRide Pro') }} - Reset Password</title>
    <link rel="icon" id="favicon" type="image/x-icon" href="{{ asset('images/favicon.png') }}">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link href="{{ asset('assets/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>
<body>
<style type="text/css">
    .login-register {
        background-color: #1B5E20;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .btn-primary {
        background-color: #D4AF37 !important;
        border-color: #D4AF37 !important;
        color: #fff !important;
    }
    .btn-primary:hover {
        background-color: #c9a130 !important;
        border-color: #c9a130 !important;
    }
</style>
<section id="wrapper">
    <div class="login-register">
        <div>
            <div class="login-logo text-center py-3">
                <a href="{{ url('/') }}" style="display:inline-block;background:#fff;border-radius:12px;padding:15px 25px;box-shadow:0 4px 15px rgba(0,0,0,0.15);"><img src="{{ asset('images/9jaride-pro-logo.png') }}?v=2" class="dark-logo" style="max-width:200px;display:block;border:none;"></a>
            </div>
            <div class="login-box card" style="margin-bottom:0%; min-width: 400px;">
                <div class="card-body">
                    <div class="box-title m-b-20">{{ trans('lang.reset_password') }}</div>

                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if(count($errors) > 0)
                        @foreach($errors->all() as $message)
                            <div class="alert alert-danger">
                                <span>{{ $message }}</span>
                            </div>
                        @endforeach
                    @endif

                    <form method="POST" action="{{ route('password.email') }}" class="form-horizontal form-material">
                        @csrf
                        <div class="form-group">
                            <div class="col-xs-12">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                    name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                                    placeholder="{{ trans('lang.email_address') }}">
                            </div>
                        </div>
                        <div class="form-group text-center m-t-20 mb-0">
                            <div class="col-xs-12">
                                <button type="submit" class="btn btn-lg btn-block text-uppercase waves-effect waves-light btn btn-primary">
                                    {{ trans('lang.send_password_reset_link') }}
                                </button>
                            </div>
                        </div>
                        <div class="form-group text-center m-t-20">
                            <a href="{{ route('login') }}" style="color: #fff;">{{ trans('lang.login') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
</body>
</html>
