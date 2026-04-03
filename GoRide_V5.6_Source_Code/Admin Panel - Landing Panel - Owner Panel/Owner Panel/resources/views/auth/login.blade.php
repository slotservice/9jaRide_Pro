
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title id="app_name">{{ config('app.name', 'GoRide') }}</title>
    <link rel="icon" id="favicon" type="image/x-icon" href="{{ asset('images/favicon.png') }}">
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <!-- Styles -->
    <link href="{{ asset('assets/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/select2/dist/css/select2.min.css') }}" rel="stylesheet">

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
    <?php if (isset($_COOKIE['owner_panel_color'])) { ?>

        <style type="text/css">
            a,
            a:hover,
            a:focus {
                color: <?php    echo $_COOKIE['owner_panel_color']; ?>;
            }
            .login-register {
                background-color: <?php    echo $_COOKIE['owner_panel_color']; ?>;
            }
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
            .form-group.default-admin .crediantials-field>a {
                position: absolute;
                right: 0;
                top: 0;
                bottom: 0;
                margin: auto;
                height: 20px;
            }
            .btn-primary,
            .btn-primary.disabled,
            .btn-primary:hover,
            .btn-primary.disabled:hover {
                background: <?php    echo $_COOKIE['owner_panel_color']; ?>;
                border: 1px solid<?php    echo $_COOKIE['owner_panel_color']; ?>;
            }
            [type="checkbox"]:checked+label::before {
                border-right: 2px solid<?php    echo $_COOKIE['owner_panel_color']; ?>;
                border-bottom: 2px solid<?php    echo $_COOKIE['owner_panel_color']; ?>;
            }
            .form-material .form-control,
            .form-material .form-control.focus,
            .form-material .form-control:focus {
                background-image: linear-gradient(<?php    echo $_COOKIE['owner_panel_color']; ?>, <?php    echo $_COOKIE['owner_panel_color']; ?>
                ), linear-gradient(rgba(120, 130, 140, 0.13), rgba(120, 130, 140, 0.13));
            }

            .btn-primary.active,
            .btn-primary:active,
            .btn-primary:focus,
            .btn-primary.disabled.active,
            .btn-primary.disabled:active,
            .btn-primary.disabled:focus,
            .btn-primary.active.focus,
            .btn-primary.active:focus,
            .btn-primary.active:hover,
            .btn-primary.focus:active,
            .btn-primary:active:focus,
            .btn-primary:active:hover,
            .open>.dropdown-toggle.btn-primary.focus,
            .open>.dropdown-toggle.btn-primary:focus,
            .open>.dropdown-toggle.btn-primary:hover,
            .btn-primary.focus,
            .btn-primary:focus,
            .btn-primary:not(:disabled):not(.disabled).active:focus,
            .btn-primary:not(:disabled):not(.disabled):active:focus,
            .show>.btn-primary.dropdown-toggle:focus {
                background: <?php    echo $_COOKIE['owner_panel_color']; ?>;
                border-color: <?php    echo $_COOKIE['owner_panel_color']; ?>                ;
                box-shadow: 0 0 0 0.2rem<?php    echo $_COOKIE['owner_panel_color']; ?>;
            }
            .error { color: #FF0000; }
        </style>
    <?php } ?>
</style>

<?php
$countries = file_get_contents(public_path('countriesdata.json'));
$countries = json_decode($countries);
$countries = (array) $countries;
$newcountries = [];
$newcountriesjs = [];
foreach ($countries as $keycountry => $valuecountry) {
    $newcountries[$valuecountry->phoneCode] = $valuecountry;
    $newcountriesjs[$valuecountry->phoneCode] = $valuecountry->code;
}
?>
<section id="wrapper">
    <div class="login-register">
        <div class="login-logo text-center py-3">
            <a href="#">
                <img src="{{ asset('images/goride-logo.png') }}" onerror="this.onerror=null; this.src='{{ asset('images/goride-logo.png') }}';" class="dark-logo"> 
            </a>
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
                <div id="form_error" class="error mb-2 text-center"></div>
                <form class="form-horizontal form-material" name="loginwithphon" id="login-with-phone-box" action="#" >
                    @csrf
                    <div class="box-title m-b-20">{{ trans('lang.login') }}</div>
                    <div class="form-group " id="phone-box">
                        <div class="col-xs-12">
                            <select name="country" id="country_selector">
                                @foreach($countries as $country)
                                        <option phoneCode="{{ $country->phoneCode }}" value="{{ $country->code }}"  >
                                            +{{ $country->phoneCode }} {{ $country->countryName }}</option>
                                        @endforeach
                            </select>
                            <input class="form-control" placeholder="Phone" id="phone" type="text"
                                class="form-control" name="phone" value="{{ old('phone') }}" required
                                autocomplete="phone" autofocus>
                            <div id="error2" class="err"></div>
                        </div>
                        @error('phone')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group " id="otp-box" style="display:none;">
                        <input class="form-control" placeholder="OTP" id="verificationcode" type="text"
                            class="form-control" name="otp" value="{{ old('otp') }}" required autocomplete="otp"
                            autofocus>
                    </div>
                    <div id="recaptcha-container" style="display:none;"></div>
                    <div class="form-group text-center m-t-20">
                        <div class="col-xs-12">
                            <button type="button" style="display:none;" onclick="applicationVerifier()"
                                id="verify_btn"
                                class="btn btn-dark btn-lg btn-block text-uppercase waves-effect waves-light btn btn-primary">
                                {{trans('lang.otp_verify')}}
                            </button>
                            <button type="button" onclick="sendOTP()" id="sendotp_btn"
                                class="btn btn-dark btn-lg btn-block text-uppercase waves-effect waves-light btn btn-primary">
                                {{trans('lang.otp_send')}}
                            </button>                                 
                        </div>
                    </div>
                    
                    <div class="mt-2 text-center">  
                        <div id="googleBtn"></div>
                    </div>
                    <div class="or-line mb-4 ">
                        <span>{{trans('lang.or')}}</span>                                
                    </div>
                    <a href="{{ route('register.phone') }}"
                        class="btn btn-dark btn-lg btn-block text-uppercase waves-effect waves-light btn btn-primary"
                        id="signup_phone">
                        <i class="fa fa-phone"> </i> {{trans('lang.signup_with_phone')}}
                    </a>
                </form>
            </div>
        </div>
    </div>
</section>
<script src="{{asset('assets/plugins/jquery/jquery.min.js')}}"></script>
<script src="{{ asset('assets/plugins/select2/dist/js/select2.min.js') }}"></script>
<script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-firestore-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-storage-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-auth-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-database-compat.js"></script>
<script src="https://unpkg.com/geofirestore/dist/geofirestore.js"></script>
<script src="https://cdn.firebase.com/libs/geofire/5.0.1/geofire.min.js"></script>
<script src="https://accounts.google.com/gsi/client" async defer></script>
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
        appLogo = (globalLogoSetting.appLogo) ? globalLogoSetting.appLogo : "{{ asset('images/goride-logo.png') }}";
        ownerPanelLogo = (globalLogoSetting.ownerPanelLogo) ? globalLogoSetting.ownerPanelLogo : "{{ asset('images/goride-logo.png') }}";
        appFavIconLogo = (globalLogoSetting.appFavIconLogo) ? globalLogoSetting.appFavIconLogo : "{{ asset('images/favicon.png') }}";
        $("#favicon").attr("href", appFavIconLogo)
        $(".dark-logo").attr("src", ownerPanelLogo);
        $(".light-logo").attr("src", ownerPanelLogo);
    });

    var newcountriesjs = '<?php echo json_encode($newcountriesjs); ?>';
    var newcountriesjs = JSON.parse(newcountriesjs);

    var documentVerificationEnable=false;
    database.collection('settings').doc("globalValue").get().then(async function(snapshots) {
        var documentVerification=snapshots.data();
        if(documentVerification.isVerifyOwnerDocument) {
            documentVerificationEnable=true;
        }
        if (documentVerification && documentVerification.ownerPanelColor) {
            setCookie('owner_panel_color',documentVerification.ownerPanelColor,365);
        }
    })

    var subscriptionModel=false;
    var businessModel=database.collection('settings').doc("globalValue");
    businessModel.get().then(async function(snapshots) {
        var businessModelSettings=snapshots.data();
        if(businessModelSettings.hasOwnProperty('subscription_model')&&
            businessModelSettings.subscription_model==true) {
            subscriptionModel=true;
        }
    });

    var commisionModel=false;
    var businessModel=database.collection('settings').doc("adminCommission");
    businessModel.get().then(async function(snapshots) {
        var commissionSetting=snapshots.data();
        if(commissionSetting.isEnabled) {
            commisionModel=true;
        }
    });

    jQuery(document).ready(function() {
        jQuery("#country_selector").select2({
            templateResult: formatState,
            templateSelection: formatState2,
            placeholder: "{{trans('lang.select_country')}}",
            allowClear: true
        });
        window.recaptchaVerifier = new firebase.auth.RecaptchaVerifier('recaptcha-container', {
            'size': 'invisible',
            'callback': (response) => {}
        });
    });

    function loginWithPhoneClick() {
        jQuery("#phone-box").show();
        jQuery("#recaptcha-container").show();
        jQuery("#sendotp_btn").show();
        window.recaptchaVerifier=new firebase.auth.RecaptchaVerifier('recaptcha-container',{
            'size': 'invisible',
            'callback': (response) => {}
        });
    }

    async function sendOTP() {

        $("#verificationcode").val('');
        $(".otp_error").html('');
        $("#form_error").html('');

        let phone = $("#phone").val().trim();
        let country = $("#country_selector").val();
        if (!phone) {
            $("#form_error").html("{{trans('lang.please_enter_phone_number')}}");
            return;
        }
        if (!country) {
            $("#form_error").html("{{trans('lang.please_select_country_code')}}");
            return;
        }
        let phoneNumber = '+' + country + phone;
        try {

            let snapshots = await database.collection("owner_users").where("phoneNumber", "==", phone).get();
            if (!snapshots.docs.length) {
                $("#form_error").html("{{trans('lang.user_is_inactive_or_not_found')}}");
                return;
            }
            // Send OTP
            const confirmationResult = await firebase.auth().signInWithPhoneNumber(phoneNumber,window.recaptchaVerifier);
            window.confirmationResult = confirmationResult;
            if (confirmationResult.verificationId) {
                $("#phone-box").hide();
                $("#recaptcha-container").hide();
                $("#otp-box").show();
                $("#verify_btn").show();
            }

        } catch (error) {
            
            console.error(error);

            let msg = error.message;
            switch (error.code) {
                case 'auth/invalid-phone-number':
                    msg = "{{trans('lang.invalid_phone_number')}}";
                    break;
                case 'auth/too-many-requests':
                    msg = "{{trans('lang.too_many_attempts')}}";
                    break;
                case 'auth/quota-exceeded':
                    msg = "{{trans('lang.sms_quota_exceeded')}}";
                    break;
                case 'auth/captcha-check-failed':
                    msg = "{{trans('lang.recaptcha_failed')}}";
                    break;
            }
            $("#form_error").html(msg);
        }
    }

    function saveUserData(user) {

        jQuery('#data-table_processing').show();

        database.collection("owner_users").doc(user.uid).get().then(async function(snapshots_login) {
            var userData=snapshots_login.data();
            if(userData) {
                
                var uid=userData.id;
                var fullName=userData.fullName;
                var phoneNumber=userData.phoneNumber;                    
                var imageURL='';
                var documentVerify=userData.hasOwnProperty('documentVerification')? userData
                    .documentVerification:false;
                setCookie('documentVerify',documentVerify);
                if(subscriptionModel||commisionModel) {
                    if(userData.hasOwnProperty('subscriptionPlanId')&&userData.subscriptionPlanId!='' &&userData.subscriptionPlanId!=null) {
                        var isSubscribed='true';
                    } else {
                        var isSubscribed='false';
                    }
                } else {
                    var isSubscribed='';
                }
                $.ajax({
                    type: 'POST',
                    url: "{{ route('setToken') }}",
                    data: {
                        id: uid,
                        userId: uid,
                        phone: phoneNumber,
                        password: '',
                        fullName: fullName,                           
                        profilePicture: imageURL,
                        provider: "google",
                        isSubscribed:isSubscribed,
                        email:userData.email
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {
                        if(data.access) {
                            jQuery('#data-table_processing').hide();
                            if(userData.hasOwnProperty('subscriptionPlanId')&&
                                userData.subscriptionPlanId!='' &&userData.subscriptionPlanId!=null) {
                                if(documentVerify===true||
                                    documentVerificationEnable===
                                    false) {
                                    window.location="{{ route('dashboard') }}";
                                } else {
                                    window.location="{{ route('owners.document') }}";
                                }
                            } else {
                                if(subscriptionModel||commisionModel) {
                                    window.location=
                                        "{{ route('subscription-plan.show') }}";
                                }  else {
                                    if(documentVerify==true||
                                        documentVerificationEnable==false) {
                                        window.location=
                                            "{{ route('dashboard') }}";
                                    } else {
                                        window.location=
                                            "{{ route('owners.document') }}";
                                    }
                                }
                            }
                        } else {
                            jQuery('#data-table_processing').hide();
                            $(".email_error").hide();
                            $(".password_error").show();
                            $(".password_error").html("");
                            window.scrollTo(0,0);
                            $(".password_error").append(
                                "<p>{{ trans('lang.set_token_error') }}</p>");
                        }
                    },
                    
                    error: function() {
                        jQuery('#data-table_processing').hide();
                        $(".email_error").hide();
                        $(".password_error").show();
                        $(".password_error").html("");
                        window.scrollTo(0,0);
                        $(".password_error").append(
                            "<p>{{ trans('lang.set_token_error') }}</p>");
                    }
                });
             
            } else {
                var loginType='google';
                var phoneNumber=user.phoneNumber||'';
                var displayName = user.displayName || '';
                var fullName = displayName.trim();
                var uuid=user.uid;
                var email=user.email||'';
                var photoURL=user.photoURL||'';
                var redirectUrl = `{{ route('register.phone') }}?uuid=${uuid}&loginType=${loginType}&phoneNumber=${encodeURIComponent(phoneNumber)}&fullName=${encodeURIComponent(fullName)}&email=${email}`;
                jQuery('#data-table_processing').hide();
                window.location.href=redirectUrl;
            }
        }).catch(function(error) {
            console.log(error);
            jQuery('#data-table_processing').hide();
            $(".email_error").hide();
            $(".password_error").show();
            $(".password_error").html("");
            window.scrollTo(0,0);
            $(".password_error").append("<p>"+error.message+"</p>");
        });
    }

    function applicationVerifier() {
        var code = $('#verificationcode').val().trim();
        if( code === "") {
            $('#form_error').html("{{trans('lang.please_enter_otp')}}");
            return;
        }

        var phone = $('#phone').val().trim();
        var countryCode = $('#country_selector').val();
        if(phone==="") {
            $('#form_error').html("{{trans('lang.user_phone_help')}}");
            return;
        }

        var fullPhoneNumber='+'+countryCode+phone;                

        database.collection("owner_users").where('countryCode','==','+'+countryCode).where('phoneNumber','==',phone).get().then(function(snapshots_login) {

            if(snapshots_login.empty) {
                $('#form_error').html("{{trans('lang.no_user_found_with_this_phone_number')}}");
                return;
            }

            var userData=snapshots_login.docs[0].data();
                if(userData) {
                    var uid=userData.id;
                    var fullName=userData.fullName;
                    var phoneNumber=userData.phoneNumber;  
                    var imageURL='';
                    var documentVerify=userData.hasOwnProperty('documentVerification')? userData.documentVerification:false;
                    var email = userData.email;

                    setCookie('documentVerify',documentVerify);
                    var isSubscribed = '';
                    if(subscriptionModel||commisionModel) {
                        if(userData.hasOwnProperty('subscriptionPlanId')&&userData.subscriptionPlanId!='' &&userData.subscriptionPlanId!=null) {
                           isSubscribed='true';
                        } else {
                            isSubscribed='false';
                        }
                    } else {
                       isSubscribed='';
                    }

                    $.ajax({
                        type: 'POST',
                        url: "{{ route('setToken') }}",
                        data: {
                            id: uid,
                            userId: uid,
                            phone: phoneNumber,
                            password: '',
                            fullName: fullName,
                            profilePic: imageURL,
                            isSubscribed:isSubscribed,
                            email:email,
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(data) {
                            if(data.access) {
                                if(userData.hasOwnProperty('subscriptionPlanId')&&  userData.subscriptionPlanId!='' &&userData.subscriptionPlanId!=null) {
                                    if(documentVerify===true||documentVerificationEnable=== false) {
                                        window.location="{{ route('dashboard') }}";
                                    } else {
                                        window.location="{{ route('owners.document') }}";
                                    }
                                } else {

                                    if(subscriptionModel||commisionModel) {
                                        window.location= "{{ route('subscription-plan.show') }}";
                                    } else {

                                        if(documentVerify==true||
                                            documentVerificationEnable==false) {
                                            window.location = "{{ route('dashboard') }}";

                                        } else {
                                            window.location = "{{ route('owners.document') }}";
                                        }
                                    }
                                }
                            } else {
                                $('#form_error').html("{{trans('lang.set_token_error')}}");
                            }

                        },
                        error: function() {
                            $('#form_error').html("{{trans('lang.set_token_error')}}");
                        }

                    });
                } else {
                    $('#form_error').html("{{trans('lang.user_is_inactive_or_not_found')}}");
                }
            })

            .catch(function(error) {
                console.error("Error fetching user data: ",error);
                $('#form_error').html("{{trans('lang.error_occurred_while_verifying_the_code')}}");
            });
        }

        function setCookie(cname,cvalue,exdays) {
            const d=new Date();
            d.setTime(d.getTime()+(exdays*24*60*60*1000));
            let expires="expires="+d.toUTCString();
            document.cookie=cname+"="+cvalue+";"+expires+";path=/";

        }
         function formatState(state) {
                if (!state.id) {
                    return state.text;
                }
                var countryCode = state.element.value.toLowerCase(); // "GB" → "gb"
                var baseUrl = "<?php echo URL::to('/'); ?>/scss/icons/flag-icon-css/flags";
                var $state = $(
                    '<span><img src="' + baseUrl + '/' + countryCode + '.svg' + '" class="img-flag" /> ' + state.text + '</span>'
                );
                return $state;
            }
            function formatState2(state) {
                if (!state.id) {
                    return state.text;
                }
                var countryCode = state.element.value.toLowerCase();
                var baseUrl = "<?php echo URL::to('/'); ?>/scss/icons/flag-icon-css/flags";
                var $state = $(
                    '<span><img class="img-flag" src="' + baseUrl + '/' + countryCode + '.svg' + '" /> <span>' + state.text + '</span></span>'
                );
                return $state;
            }	
        var globalSettingsRef = database.collection('settings').doc("globalValue");

            globalSettingsRef.get().then(function(snapshot) {

                var globalSettings = snapshot.data();

                if (!globalSettings || !globalSettings.defaultCountryCode) {
                    return;
                }

                let defaultCountryCode = globalSettings.defaultCountryCode.toString().trim();
                let $option = null;

                $option = $("#country_selector option[value='" + defaultCountryCode.toUpperCase() + "']");

                if ($option.length === 0) {
                    let phoneCode = defaultCountryCode.replace('+', '');
                    $option = $("#country_selector option[phoneCode='" + phoneCode + "']");
                }

                if ($option.length > 0) {
                    $("#country_selector").val($option.val()).trigger('change');
                } else {
                    console.warn("Default country not found:", defaultCountryCode);
                }

            }).catch(function(error) {
                console.error("Error fetching global settings:", error);
            });

        window.onload = function () {
            google.accounts.id.initialize({
                client_id: "{{env('GOOGLE_CLIENT_ID')}}",
                callback: handleGoogleCredential
            });
            google.accounts.id.renderButton(
                document.getElementById("googleBtn"),
                {
                    theme: "outline",
                    size: "large",                      
                    width: 348,
                    text: "continue_with"
                }
            );
        };

        async function handleGoogleCredential(response) {
            try {
                const idToken = response.credential;
                const payload = JSON.parse(atob(idToken.split('.')[1]));
                const email = payload.email;

                const methods = await firebase.auth().fetchSignInMethodsForEmail(email);
                if (methods.includes('password')) {
                    $("#form_error").show().html("{{trans('lang.account_already_exists_for_email')}}");
                    return;
                }

                const snapshot = await firebase.firestore()
                    .collection('owner_users')
                    .where('email', '==', email)
                    .limit(1)
                    .get();

                if (!snapshot.empty) {
                    const userData = snapshot.docs[0].data();
                    if (userData.loginType !== 'google') {
                        $("#form_error").show().html("{{trans('lang.account_already_exists_for_email')}}");
                        return;
                    }
                }
                const credential = firebase.auth.GoogleAuthProvider.credential(idToken);
                const userCredential = await firebase.auth().signInWithCredential(credential);

                saveUserData(userCredential.user);

            } catch (e) {
                console.error("Google Sign-In Error:", e);
            }
        }

</script>
</body>
</html>
