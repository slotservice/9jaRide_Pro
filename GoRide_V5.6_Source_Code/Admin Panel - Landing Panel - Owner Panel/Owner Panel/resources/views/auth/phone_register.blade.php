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
   /*  .login-register {
        background-color: #000;
    } */
</style>
<?php if (isset($_COOKIE['owner_panel_color'])) { ?>

    <style type="text/css">
        a,
        a:hover,
        a:focus {
            color: <?php echo $_COOKIE['owner_panel_color']; ?>;
        }
        .login-register {
            background-color: <?php echo $_COOKIE['owner_panel_color']; ?>;
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
            background: <?php echo $_COOKIE['owner_panel_color']; ?>;
            border: 1px solid<?php echo $_COOKIE['owner_panel_color']; ?>;
        }
        [type="checkbox"]:checked+label::before {
            border-right: 2px solid<?php echo $_COOKIE['owner_panel_color']; ?>;
            border-bottom: 2px solid<?php echo $_COOKIE['owner_panel_color']; ?>;
        }
        .form-material .form-control,
        .form-material .form-control.focus,
        .form-material .form-control:focus {
            background-image: linear-gradient(<?php echo $_COOKIE['owner_panel_color']; ?>, <?php echo $_COOKIE['owner_panel_color']; ?>), linear-gradient(rgba(120, 130, 140, 0.13), rgba(120, 130, 140, 0.13));
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
            background: <?php echo $_COOKIE['owner_panel_color']; ?>;
            border-color: <?php echo $_COOKIE['owner_panel_color']; ?>;
            box-shadow: 0 0 0 0.2rem<?php echo $_COOKIE['owner_panel_color']; ?>;
        }
        .select2.select2-container {
            width: 31% !important;
        }
        .error { color: #FF0000; }

        .alert.alert-danger ul{
            padding: 0; 
            margin: 0;
            list-style: none;
        }
    </style>
<?php } ?>

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
            <a href="#"><img  src="{{ asset('images/goride-logo.png') }}" onerror="this.onerror=null; this.src='{{ asset('images/goride-logo.png') }}';" class="dark-logo"> </a>
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
                <div id="form_error" class="error mb-2 text-center" style="display:none;"></div>
                <div class="alert alert-success" style="display:none;"></div>
                <form class="form-horizontal form-material" name="loginwithphon" id="login-with-phone-box" action="#">
                    @csrf
                    <div class="box-title m-b-20">{{ trans('lang.sign_up_with_us') }}</div>
                    <div class="form-group" id="fullName_div">
                        <label for="fullName" class="text-dark">{{ trans('lang.full_name') }}</label>
                        <input type="text" placeholder="{{trans('lang.enter_full_name')}}" class="form-control" id="fullName" required>
                        <input type="hidden" id="hidden_fName" />
                    </div>                   
                    <div class="form-group" id="email_div">
                        <label class="text-dark">{{ trans('lang.email') }}</label>
                        <input type="email" placeholder="Enter Email" class="form-control user_email" id="email" required>
                        <input type="hidden" id="hidden_email" />
                    </div>
                    <div class="form-group " id="phone-box">
                        <div class="col-xs-12">
                            <select name="country" id="country_selector" class="country_code">
                                 @foreach($countries as $country)
                                            <option phoneCode="{{ $country->phoneCode }}" value="{{ $country->code }}">
                                                +{{ $country->phoneCode }} {{ $country->countryName }}</option>
                                            @endforeach
                            </select>
                            <input class="form-control user_phone" placeholder="{{trans('lang.user_phone')}}" id="phone" type="phone" name="phone" value="{{ old('phone') }}" required autocomplete="phone" autofocus>
                            <div id="error2" class="err"></div>
                        </div>
                        @error('phone')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group " id="otp-box" style="display:none;">
                        <input class="form-control" placeholder="{{trans('lang.otp')}}" id="verificationcode" type="text" class="form-control" name="otp" value="{{ old('otp') }}" required autocomplete="otp" autofocus>
                    </div>
                    <div id="recaptcha-container" style="display:none;"></div>
                    <div class="form-group text-center m-t-20">
                        <div class="col-xs-12">
                            <button type="button" style="display:none;" onclick="applicationVerifier()" id="verify_btn" class="btn btn-dark btn-lg btn-block text-uppercase waves-effect waves-light btn btn-primary">{{ trans('lang.otp_verify') }}</button>
                            <button type="button" onclick="sendOTP()" id="send-code" class="btn btn-dark btn-lg btn-block text-uppercase waves-effect waves-light btn btn-primary">{{ trans('lang.otp_send') }}</button>
                        </div>
                    </div>
                    <button type="button" style="display:none;" id="google_signup_btn" 
                        class="btn btn-dark btn-lg btn-block text-uppercase waves-effect waves-light btn btn-primary">
                        {{ trans('lang.sign_up_with_us') }}
                    </button>
                    <input type="hidden" id="hidden_uuid" />
                    <input type="hidden" id="hidden_loginType" />

                </form>
                <div class="new-acc d-flex align-items-center justify-content-center mt-4 mb-3">
                    <a href="{{ url('login') }}">
                        <p class="text-center m-0"> {{ trans('lang.already_an_account') }} {{ trans('lang.sign_in') }}</p>
                    </a>
                </div>
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

<script src="{{ asset('js/crypto-js.js') }}"></script>
<script src="{{ asset('js/jquery.cookie.js') }}"></script>
<script src="{{ asset('js/jquery.validate.js') }}"></script>

<script type="text/javascript">

    var database = firebase.firestore();
    var createdAtman = firebase.firestore.Timestamp.fromDate(new Date());
    
    var adminEmail = '';
    var emailSetting = database.collection('settings').doc('emailSetting');

    var documentVerificationEnable = false;
    database.collection('settings').doc("globalValue").get().then(async function(snapshots) {
        var documentVerification=snapshots.data();
        if(documentVerification.isVerifyOwnerDocument) {
            documentVerificationEnable=true;
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
    
    window.recaptchaVerifier = new firebase.auth.RecaptchaVerifier('recaptcha-container', {
        'size': 'invisible',
        'callback': (response) => {}
    });
   
    $('#phone').on('keypress', function(event) {
        if (!(event.which >= 48 && event.which <= 57)) {
            document.getElementById('error2').innerHTML = "{{trans('lang.accept_only_number')}}";
            return false;
        } else {
            document.getElementById('error2').innerHTML = "";
            return true;
        }
    });

    async function sendOTP() {
        $(".otp").val("");
        $("#form_error").hide().html("");
        if (!window.recaptchaVerifier) {
            $("#recaptcha-container").show();
            window.recaptchaVerifier = new firebase.auth.RecaptchaVerifier('recaptcha-container', {
                'size': 'invisible',
                'callback': (response) => {}
            });
        }

        const fullName = $('#fullName').val().trim();
        const email = $('#email').val().trim();
        const phone = $('#phone').val().trim();
        const countryCode = jQuery("#country_selector option:selected").attr('phoneCode');

        if (!fullName) {
            showError("{{ trans('lang.enter_owners_name_error') }}");
            return;
        }
        if (!email) {
            showError("{{ trans('lang.enter_owners_email') }}");
            return;
        }
        if (!phone) {
            showError("{{ trans('lang.enter_owners_phone') }}");
            return;
        }

        const phoneNumber = `+${countryCode}${phone}`;

        try {
            // Check if phone already exists
            const snapshots = await database.collection("owner_users").where('phoneNumber', '==', phone).get();
            if (snapshots.docs.length > 0) {
                alert("{{trans('lang.you_already_have_account_with_this_phone_number')}}");
                return;
            }

            $('#hidden_fName').val(fullName);
            $('#hidden_email').val(email);

            // Send OTP
            const confirmationResult = await firebase.auth().signInWithPhoneNumber(
                phoneNumber,
                window.recaptchaVerifier
            );

            window.confirmationResult = confirmationResult;

            if (confirmationResult.verificationId) {
                $('#fullName_div, #phone-box, #email_div').hide();
                $("#recaptcha-container, #form_error").hide();
                $("#verify_btn, #otp-box").show();
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
            showError(msg || "{{trans('lang.something_went_wrong')}}");
        }
    }

    function showError(message) {
        $("#form_error").show().html(`${message}`);
        window.scrollTo(0, 0);
    }

    function applicationVerifier() {
        var code = $('#verificationcode').val();
        if (!code) {
            $('#form_error').show().html("{{trans('lang.please_enter_otp')}}");
            return;
        }
        window.confirmationResult.confirm(code)
        .then(async function(result) {
            var countryCode = '+' + $(".country_code option:selected").attr('phoneCode');
            var phone = jQuery("#phone").val();
            var fullName = $('#hidden_fName').val();
            var email = $('#hidden_email').val();
            var uuid = result.user.uid;
            var loginType = $('#hidden_loginType').val() || "phone";

            $('#verify_btn').prop('disabled', true);

            $.ajax({
                url: "{{ route('phone.register') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                data: {
                    name: fullName,
                    email: email,
                    phone: phone,
                    uuid: uuid
                },
                success: function(response) {

                    // Firestore write after MySQL success
                    database.collection("owner_users").doc(uuid).set({
                        'email': email,
                        'fullName': fullName,
                        'id': uuid,
                        'countryCode': countryCode,
                        'phoneNumber': phone,
                        'profilePic': "",
                        'createdAt': createdAtman,
                        'loginType': loginType,
                        'documentVerification': false,
                    }).then(() => {
                        // success message and redirect
                        $("#form_error").hide();
                        $(".alert-success").show().html("<p>{{ trans('lang.thank_you_signup_msg') }}</p>");
                        if(subscriptionModel||commisionModel) {
                            window.location.href = "{{ route('subscription-plan.show') }}";
                        }  else {
                            window.location.href = response.redirect || "{{ route('dashboard') }}";
                        }
                    }).catch(function(fireErr) {
                        console.error('Firestore error:', fireErr);
                        $('#verify_btn').prop('disabled', false);
                        $("#form_error").show().html('<div class="alert alert-danger">{{trans("lang.failed_to_save_to_firestore_please_try_again")}}</div>');
                    });
                },
                error: function(xhr) {
                    // log details for debugging
                    console.error('register-phone AJAX error', {
                        status: xhr.status,
                        responseText: xhr.responseText,
                        responseJSON: xhr.responseJSON
                    });
                    $('#verify_btn').prop('disabled', false);

                    if (xhr.status === 422) {
                        // validation errors
                        let errors = (xhr.responseJSON && xhr.responseJSON.errors) ? xhr.responseJSON.errors : {};
                        let errorHtml = '<div class="alert alert-danger"><ul>';
                        $.each(errors, function(key, messages) {
                            errorHtml += '<li>' + messages[0] + '</li>';
                        });
                        errorHtml += '</ul></div>';
                        $("#form_error").show().html(errorHtml);
                        window.scrollTo(0, 0);
                    } else if (xhr.status === 419) {
                        $("#form_error").show().html('<div class="alert alert-warning">{{trans("lang.session_expired_please_refresh_the_page_and_try_again")}}</div>');
                    } else {
                        // fallback: use server message if present
                        let msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Something went wrong, please try again.';
                        $("#form_error").show().html('<div class="alert alert-danger">' + msg + '</div>');
                    }
                }
            });
        })
        .catch((error) => {
            console.error("OTP verification failed ", error);
            $('#form_error').show().html("{{trans('lang.invalid_otp')}}");
        });
    }

 var newcountriesjs = '<?php echo json_encode($newcountriesjs); ?>';
            var newcountriesjs = JSON.parse(newcountriesjs);
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

    $(document).ready(function() {       
        try {
            const urlParams = new URLSearchParams(window.location.search);          

            const fullName = urlParams.get('fullName') ? decodeURIComponent(urlParams.get('fullName')) : '';
            const email = urlParams.get('email') ? decodeURIComponent(urlParams.get('email')) : '';
            const uuid = urlParams.get('uuid') || '';
            const loginType = urlParams.get('loginType') || '';        
            if (fullName) {
                $("#fullName").val(fullName);
                $("#hidden_fName").val(fullName);
            }
            if (email) {
                $("#email").val(email);
                $("#email").attr("readonly", true);
                $("#hidden_email").val(email);
            }
            if (uuid) {
                $("#hidden_uuid").val(uuid);
            }
            if (loginType) {
                $("#hidden_loginType").val(loginType);
            }
            if (loginType === "google") {
                $("#send-code, #verify_btn, #otp-box").hide();
                $("#google_signup_btn").show();
                $("#google_signup_btn").on("click", function() {
                    let fullName = $("#fullName").val();
                    let email = $("#hidden_email").val();
                    let uuid = $("#hidden_uuid").val();
                    let phone = $("#phone").val(); 
                    var countryCode = '+' + jQuery("#country_selector").val();

                    $.ajax({
                        url: "{{ route('phone.register') }}",
                        type: "POST",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: 'json',
                        data: {
                            name: fullName,
                            email: email,
                            phone: phone || null,
                            uuid: uuid
                        },
                        success: function(response) {
                            // Also add to Firestore
                            database.collection("owner_users").doc(uuid).set({
                                'email': email,
                                'fullName': fullName,
                                'id': uuid,
                                'countryCode': countryCode,
                                'phoneNumber': phone || '',
                                'profilePic': "",
                                'createdAt': createdAtman,
                                'loginType': "google",
                                'documentVerification': false,
                            }).then(() => {
                                $("#form_error").hide();
                                $(".alert-success").show().html("<p>{{ trans('lang.thank_you_signup_msg') }}</p>");
                                
                                if(subscriptionModel||commisionModel) {
                                    window.location.href =
                                        "{{ route('subscription-plan.show') }}";
                                }  else {
                                    window.location.href = response.redirect || "{{ route('dashboard') }}";
                                }
                            });
                        },
                        error: function(xhr) {
                            // console.error(xhr.responseText);
                            let errorHtml = '';

                            if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                                $.each(xhr.responseJSON.errors, function (key, messages) {
                                    messages.forEach(function (msg) {
                                        errorHtml += `<p>${msg}</p>`;
                                    });
                                });

                                $("#form_error").show().html(`<div class="alert alert-warning">${errorHtml}</div>`);
                            } else {
                                $("#form_error").show().html(`<div class="alert alert-danger">{{ trans('lang.session_expired_please_refresh_the_page_and_try_again') }}</div>`);
                            }
                        }
                    });
                });
            }
        } catch (err) {
        console.error("Error inside ready():", err);    }
       
        $("#country_selector").select2({
            templateResult: formatState,
            templateSelection: formatState2,
            placeholder: "Select Country",
            allowClear: true
        });
    });
    async function sendEmail(url, subject, message, recipients) {
        var checkFlag = false;
        await $.ajax({
            type: 'POST',
            data: {
                subject: subject,
                message: message,
                recipients: recipients
            },
            url: url,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(data) {
                checkFlag = true;
            },
            error: function(xhr, status, error) {
                checkFlag = true;
            }
        });
        return checkFlag;
    }
</script>
</body>
</html>
