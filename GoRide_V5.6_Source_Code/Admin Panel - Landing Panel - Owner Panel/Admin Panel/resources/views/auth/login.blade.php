<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title id="app_name">{{ config('app.name', '9jaRide Pro') }}</title>
    <link rel="icon" id="favicon" type="image/x-icon" href="{{ asset('images/favicon.png') }}">
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <!-- Styles -->
    <link href="{{ asset('assets/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    @yield('style')
</head>
<body>
<style type="text/css">
    .form-group.default-admin {
        padding: 10px;
        font-size: 14px;
        color: #000;
        font-weight: 600;
        border-radius: 10px;
        box-shadow: 0 0px 6px 0px rgba(0, 0, 0, 0.5);
        margin: 20px 10px 10px 10px;
    }
    .form-group.default-admin .crediantials-field {
        position: relative;
        padding-right: 15px;
        text-align: left;
        padding-top: 5px;
        padding-bottom: 5px;
    }
    .form-group.default-admin .crediantials-field > a {
        position: absolute;
        right: 0;
        top: 0;
        bottom: 0;
        margin: auto;
        height: 20px;
    }
    .login-register {
        background-color: #1B5E20;
    }
    .btn-dark, .btn-primary {
        background-color: #1B5E20 !important;
        border-color: #1B5E20 !important;
    }
    .btn-dark:hover, .btn-primary:hover {
        background-color: #145218 !important;
        border-color: #145218 !important;
    }
</style>
<section id="wrapper">
    <div class="login-register">
        <div class="login-logo text-center py-3">
            <a href="#"><img
                        src="{{ asset('images/9jaride-pro-logo.png') }}" onerror="this.onerror=null; this.src='{{ asset('images/9jaride-pro-logo.png') }}';" class="dark-logo"> </a>
        </div>
        <div class="login-box card" style="margin-bottom:0%;">
            <div class="card-body">
                @if(count($errors) > 0)
                    @foreach( $errors->all() as $message )
                        <div class="alert alert-danger display-hide">
                            <button class="close" data-close="alert"></button>
                            <span>{{ $message }}</span>
                        </div>
                    @endforeach
                @endif
                <form class="form-horizontal form-material" method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="box-title m-b-20">{{ trans('lang.login') }}</div>
                    <div class="form-group ">
                        <div class="col-xs-12">
                            <input class="form-control" placeholder="{{ trans('lang.email_address') }}" id="email" type="email"
                                   class="form-control @error('email') is-invalid @enderror" name="email"
                                   value="{{ old('email') }}" required autocomplete="email" autofocus></div>
                        @error('email')
                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <div class="col-xs-12">
                            <input id="password" placeholder="{{ trans('lang.password') }}" type="password"
                                   class="form-control @error('password') is-invalid @enderror" name="password" required
                                   autocomplete="current-password"></div>
                        @error('password')
                        <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                        @enderror
                    </div>
                    <div class="form-group text-center m-t-20">
                        <div class="col-xs-12">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember')
                            ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">
                                {{ trans('lang.remember_me') }}
                            </label>
                        </div>
                    </div>
                    <div class="form-group text-center m-t-20 mb-0">
                        <div class="col-xs-12">
                            <button type="submit"
                                    class="btn btn-dark btn-lg btn-block text-uppercase waves-effect waves-light btn btn-primary">
                                {{ trans('lang.login') }}
                            </button>                           
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<script src="{{asset('assets/plugins/jquery/jquery.min.js')}}"></script>
<script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-firestore-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-storage-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-auth-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-database-compat.js"></script>
<script src="https://unpkg.com/geofirestore/dist/geofirestore.js"></script>
<script src="https://cdn.firebase.com/libs/geofire/5.0.1/geofire.min.js"></script>
<script src="{{ asset('js/crypto-js.js') }}"></script>
<script src="{{ asset('js/jquery.cookie.js') }}"></script>
<script src="{{ asset('js/jquery.validate.js') }}"></script>
<script type="text/javascript">
    var database = firebase.firestore();
    var appLogo = '';
    var appFavIconLogo = '';
    var googleApiKey = '';
    let globalLogoRef = database.collection('settings').doc('logo');
    globalLogoRef.get().then(async function (snapshots) {
        var globalLogoSetting = snapshots.data();
        appLogo = (globalLogoSetting.appLogo) ? globalLogoSetting.appLogo : "{{ asset('images/9jaride-pro-logo.png') }}";
        appFavIconLogo = (globalLogoSetting.appFavIconLogo) ? globalLogoSetting.appFavIconLogo : "{{ asset('images/favicon.png') }}";
        $("#favicon").attr("href", appFavIconLogo)
        $(".dark-logo").attr("src", appLogo);
        $(".light-logo").attr("src", appLogo);
    });
</script>
</body>
</html>
