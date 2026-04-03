@extends('layouts.app')

@section('content')
    <div class="page-wrapper">
        <div class="row page-titles">

            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor restaurantTitle">{{ trans('lang.driver_plural') }}</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ trans('lang.dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a href="{!! route('drivers') !!}">{{ trans('lang.driver_plural') }}</a></li>
                    <li class="breadcrumb-item active">{{ trans('lang.driver_details') }}</li>
                </ol>
            </div>

        </div>

        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="row">
                                <div class="col-lg-12 col-md-6">
                                    <div class="card card-block p-card">
                                        <div class="profile-box">
                                            <div class="profile-card rounded">
                                                <img src="https://goride.siswebapp.com/images/default_user.png" alt="profile-bg" class="avatar-100 d-block mx-auto img-fluid mb-3  avatar-rounded user-image">
                                                <h3 class="font-600 text-white text-center user-name"></h3>
                                                <div class="font-600 text-white text-center mb-3 user-total-ratings"></div>                                                                                  

                                            </div>
                                            <div class="pro-content rounded">
                                                <div class="d-flex align-items-center mb-3">
                                                    <div class="p-icon mr-3">
                                                        <i class="fa fa-envelope"></i>
                                                    </div>
                                                    <p class="mb-0 eml user-email"></p>
                                                </div>
                                                <div class="d-flex align-items-center mb-3">
                                                    <div class="p-icon mr-3">
                                                        <i class="fa fa-phone"></i>
                                                    </div>
                                                    <p class="mb-0 user-phone"></p>
                                                </div>
                                            </div>

                                            <div class="personal-detail">
                                                <h3>{{trans('lang.vehicle_information')}}</h3>
                                                <div class="table-responsive user-list-table">
                                                    <table class="table mb-0">
                                                        <tbody id="vehicle_information">
                                                            <tr>
                                                                <td class="py-2 px-0">
                                                                    <span class="font-weight-bold w-100">{{ trans('lang.seats') }}:</span>
                                                                </td>
                                                                <td class="py-2 px-0 seats"></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="py-2 px-0">
                                                                    <span class="font-weight-bold w-100">{{ trans('lang.vehicle_color') }}:</span>
                                                                </td>
                                                                <td class="py-2 px-0 vehicle_color"></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="py-2 px-0">
                                                                    <span class="font-weight-bold w-100">{{ trans('lang.vehicle_number') }}:</span>
                                                                </td>
                                                                <td class="py-2 px-0"><span class="num-plat vehicle_number"></span></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="py-2 px-0">
                                                                    <span class="font-weight-bold w-100">{{ trans('lang.vehicle_type') }}:</span>
                                                                </td>
                                                                <td class="py-2 px-0 vehicle_type"></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="py-2 px-0">
                                                                    <span class="font-weight-bold w-100">{{ trans('lang.zone') }}:</span>
                                                                </td>
                                                                <td class="py-2 px-0 zone_name"></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>                                          

                                            <div class="personal-detail">
                                                <h3>{{ trans('lang.rules') }}</h3>
                                                <div class="rules-list">
                                                    <ul id="driver_rules"></ul>

                                                </div>

                                            </div>

                                            <div class="personal-detail mb-0">
                                                <h3>{{ trans('lang.bankdetails') }}</h3>
                                                <div class="table-responsive user-list-table">
                                                    <table class="table mb-0">
                                                        <tbody id="bank_information">
                                                            <tr>
                                                                <td class="py-2 px-0">
                                                                    <span class="font-weight-bold w-100">{{ trans('lang.bank_name') }}:</span>
                                                                </td>
                                                                <td class="py-2 px-0 bank_name"></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="py-2 px-0">
                                                                    <span class="font-weight-bold w-100">{{ trans('lang.branch_name') }}:</span>
                                                                </td>
                                                                <td class="py-2 px-0 branch_name"></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="py-2 px-0">
                                                                    <span class="font-weight-bold w-100">{{ trans('lang.holder_name') }}:</span>
                                                                </td>
                                                                <td class="py-2 px-0 holder_name"></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="py-2 px-0">
                                                                    <span class="font-weight-bold w-100">{{ trans('lang.account_number') }}:</span>
                                                                </td>
                                                                <td class="py-2 px-0"><span class="num-plat account_number"></span></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="py-2 px-0">
                                                                    <span class="font-weight-bold w-100">{{ trans('lang.other_information') }}:</span>
                                                                </td>
                                                                <td class="py-2 px-0 other_info"></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <div class="personal-detail mb-0">
                                                <h3>{{ trans('lang.referral_info') }}</h3>
                                                <div class="table-responsive user-list-table">
                                                    <table class="table mb-0">
                                                        <tbody id="referral_information">
                                                            <tr>
                                                                <td class="py-2 px-0">
                                                                    <span class="font-weight-bold w-100">{{ trans('lang.referral_code') }}:</span>
                                                                </td>
                                                                <td class="py-2 px-0 referral_code"></td>
                                                            </tr>

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="card card-block card-stretch">
                                        <div class="card-header bg-white">
                                            <ul class="nav nav-pills mb-3" role="tablist">

                                                <li class="nav-item">
                                                    <a class="nav-link ride_list active" data-toggle="pill" href="#ride_list" role="tab">{{ trans('lang.ride_list') }}</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link intercity_ride_list" data-toggle="pill" href="#intercity_ride_list" role="tab">{{ trans('lang.intercity_ride_list') }}</a>
                                                </li>                                               
                                            </ul>
                                        </div>

                                        <div class="card-body">
                                            <div class="tab-content">

                                                <div class="tab-pane active" id="ride_list" role="tabpanel">
                                                    <div class="table-responsive">
                                                        <table class="table table-striped table-valign-middle" id="rideListTable">
                                                            <thead class="table-color-heading">
                                                                <tr class="text-secondary">
                                                                    <th scope="col">{{ trans('lang.ride_id') }}</th>
                                                                    <th scope="col">{{ trans('lang.customer') }}</th>
                                                                    <th scope="col">{{ trans('lang.user_service') }}</th>
                                                                    <th scope="col">{{ trans('lang.date') }}</th>
                                                                    <th scope="col">{{ trans('lang.ride_status') }}</th>
                                                                    <th scope="col">{{ trans('lang.payment_method') }}</th>
                                                                    <th scope="col">{{ trans('lang.payment_status') }}</th>
                                                                    <th scope="col">{{ trans('lang.total_amount') }}</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="ride_list_rows"></tbody>
                                                        </table>
                                                    </div>
                                                </div>

                                                <div class="tab-pane" id="intercity_ride_list" role="tabpanel">
                                                    <div class="table-responsive">
                                                        <table class="table table-striped table-valign-middle" id="intercityRideListTable">
                                                            <thead class="table-color-heading">

                                                                <tr class="text-secondary">
                                                                    <th scope="col">{{ trans('lang.ride_id') }}</th>
                                                                    <th scope="col">{{ trans('lang.customer') }}</th>

                                                                    <th scope="col">{{ trans('lang.user_service') }}</th>
                                                                    <th scope="col">{{ trans('lang.date') }}</th>
                                                                    <th scope="col">{{ trans('lang.ride_status') }}</th>

                                                                    <th scope="col">{{ trans('lang.payment_method') }}</th>
                                                                    <th scope="col">{{ trans('lang.payment_status') }}</th>
                                                                    <th scope="col">{{ trans('lang.total_amount') }}</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="intercity_ride_list_rows"></tbody>
                                                        </table>
                                                    </div>
                                                </div>

                                            </div>

                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group col-12 text-center btm-btn doc-footer">
                    <a href="{!! route('drivers') !!}" class="btn btn-default"><i class="fa fa-undo cancel-btn"></i>{{ trans('lang.cancel') }}</a>
                </div>

            </div>

        </div>

    </div>
   
    <div class="modal fade" id="updateLimitModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered location_modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title locationModalTitle">{{ trans('lang.update_plan_limit') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="">
                        <div class="form-row">
                            <div class="form-group row">
                                <div class="form-group row width-100">
                                    <label class="control-label">{{ trans('lang.maximum_booking_limit') }}</label>
                                    <div class="form-check width-100">
                                        <input type="radio" id="unlimited_booking" name="set_booking_limit" value="unlimited" checked>
                                        <label class="control-label" for="unlimited_booking">{{ trans('lang.unlimited') }}</label>
                                    </div>
                                    <div class="d-flex">
                                        <div class="form-check width-50 limited_booking_div">
                                            <input type="radio" id="limited_booking" name="set_booking_limit" value="limited">
                                            <label class="control-label" for="limited_booking">{{ trans('lang.limited') }}</label>
                                        </div>
                                        <div class="form-check width-50 d-none booking-limit-div">
                                            <input type="number" id="booking_limit" class="form-control" placeholder="{{ trans('lang.ex_1000') }}">
                                        </div>
                                    </div>
                                    <span class="booking_limit_err"></span>
                                </div>

                            </div>
                        </div>
                    </form>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary update-plan-limit">{{ trans('submit') }}</a></button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close">
                            {{ trans('close') }}</a>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        var id = "{{ $driverId }}";
        var database = firebase.firestore();
        var ref = database.collection('driver_users').doc(id);
        var rideRef = database.collection('orders').where("driverId", "==", id).orderBy('createdDate', 'desc');
        var intercityRideRef = database.collection('orders_intercity').where("driverId", "==", id).orderBy('createdDate', 'desc');
        var refCurrency = database.collection('currency').where('enable', '==', true).limit('1');
        var refBankDetails = database.collection('bank_details').doc(id);
        var walletRef = database.collection('wallet_transaction').where("userId", "==", id).orderBy('createdDate', 'desc');
        var refPayoutRequest = database.collection('withdrawal_history').where('userId', '==', id).orderBy('createdDate', 'desc');
        var refSubscriptionHistory = database.collection('subscription_history').where('user_id', '==', id).orderBy('createdAt', 'desc');
        var decimal_degits = 0;
        var symbolAtRight = false;
        var currentCurrency = '';
        var storageRef = firebase.storage().ref('images');
        var storage = firebase.storage();
        var placeholderImage = "{{ asset('/images/default_user.png') }}";
        var driver_details = "{{ trans('lang.driver_details') }}";
        var notFound = "{{ trans('lang.doc_not_found') }}"
        var docsRef = database.collection('documents');
        var docref = database.collection('driver_document').doc(id);
        var reviewRef = database.collection('review_driver').where("driverId", "==", id);
        var referralRef = database.collection('referral').where('id', '==', id);
        var requestUrl = "{{ request()->is('drivers/document/*') }}";
        var back_photo = '';
        var front_photo = '';
        var backFileName = '';
        var frontFileName = '';
        var backFileOld = '';
        var frontFileOld = '';
        var setLanguageCode = getCookie('setLanguage');
        var defaultLanguageCode = getCookie('defaultLanguage');
        refCurrency.get().then(async function(snapshots) {
            var currencyData = snapshots.docs[0].data();
            currentCurrency = currencyData.symbol;
            decimal_degits = currencyData.decimalDigits;
            if (currencyData.symbolAtRight) {
                symbolAtRight = true;
            }
        });
        var commisionModel = false;
        var AdminCommission = '';
        database.collection('settings').doc("adminCommission").get().then(async function(snapshots) {
            var commissionSetting = snapshots.data();
            if (commissionSetting.isEnabled == true) {
                commisionModel = true;
            }
            if (commissionSetting.type == "fix") {
                if (symbolAtRight) {
                    AdminCommission = parseFloat(commissionSetting).amount.toFixed(decimal_degits) + currentCurrency;
                } else {
                    AdminCommission = currentCurrency + parseFloat(commissionSetting.amount).toFixed(decimal_degits);
                }

            } else {
                AdminCommission = commissionSetting.amount + '' + '%';
            }
        });
        var subscriptionModel = false;
        database.collection('settings').doc("globalValue").get().then(async function(snapshots) {
            var businessModelSettings = snapshots.data();
            if (businessModelSettings.hasOwnProperty('subscription_model') && businessModelSettings.subscription_model == true) {
                subscriptionModel = true;
            }
        });

        $(document).ready(async function() {
            if (requestUrl) {
                $('li').removeClass('active');
                $("#documents-tab").addClass('active');
                $("#documents-tab").click();
            } else {
                $(".driver-tab").first().addClass('active');
                $(".driver-tab").first().click();
            }

            $('.driver_sub_menu li').each(function() {

                var url = $(this).find('a').attr('href');

                if (url == document.referrer) {
                    $(this).find('a').addClass('active');
                    $('.driver_menu').addClass('active').attr('aria-expanded', true);

                }
                $('.driver_sub_menu').addClass('in').attr('aria-expanded', true);
            });

            getRideList();

        });



        referralRef.get().then(function(snapshot) {
            if (snapshot.docs.length > 0) {
                var referralData = snapshot.docs[0].data();
                $('.referral_code').html(referralData.referralCode);
            }

        })



        ref.get().then(async function(snapshot) {
            let data = snapshot.data();
            $(".user-name").text(data.fullName);
            $(".user-email").text(shortEmail(data.email));
            data.countryCode = data.countryCode.includes('+') ? data.countryCode.slice(1) : data.countryCode;
            $(".user-phone").text('+' + shortNumber(data.countryCode, data.phoneNumber));

            var rating = 0;
            var reviewsCount = 0;

            if (data.hasOwnProperty('reviewsCount') && data.reviewsCount && data.reviewsCount != "0.0" && data.reviewsCount != null && data.hasOwnProperty('reviewsSum') && data.reviewsSum && data.reviewsSum != "0.0" && data.reviewsSum != null) {

                rating = (parseFloat(data.reviewsSum) / parseFloat(data.reviewsCount));

                rating = (rating * 10) / 10;

                reviewsCount = parseInt(data.reviewsCount);
            }

            $('.user-total-ratings').html('<span class="badge badge-warning text-white dr-review"><i class="fa fa-star"></i>' + (rating).toFixed(1) + '</span>');

            var walletAmount = 0;
            if (data.hasOwnProperty('walletAmount') && data.walletAmount != null) {
                walletAmount = data.walletAmount;
            }
            if (symbolAtRight) {
                $(".user-wallet").text("{{ trans('lang.wallet_Balance') }} : " + parseFloat(walletAmount).toFixed(decimal_degits) + currentCurrency);
            } else {
                $(".user-wallet").text("{{ trans('lang.wallet_Balance') }} : " + currentCurrency + parseFloat(walletAmount).toFixed(decimal_degits));
            }

            if (data.profilePic != null && data.profilePic != '') {
                $(".user-image").attr('src', data.profilePic);
            }

            if (data.hasOwnProperty('vehicleInformation')) {

                if (data.vehicleInformation.seats != "" || data.vehicleInformation.vehicleColor != "" || data.vehicleInformation.vehicleNumber != "" || data.vehicleInformation.vehicleType != "") {

                    if (data.vehicleInformation.seats != "") {
                        $(".seats").text(data.vehicleInformation.seats);
                    }
                    if (data.vehicleInformation.vehicleColor != "") {
                        $(".vehicle_color").text(data.vehicleInformation.vehicleColor);
                    }
                    if (data.vehicleInformation.vehicleNumber != "") {
                        $(".vehicle_number").text(data.vehicleInformation.vehicleNumber);
                    }
                    if (data.vehicleInformation.vehicleType != "" && data.vehicleInformation.vehicleType != null) {
                        var vehicleType = '';
                        if (Array.isArray(data.vehicleInformation.vehicleType)) {
                            var foundItem = data.vehicleInformation.vehicleType.find(item => item.type === setLanguageCode);
                            if (foundItem && foundItem.name != '') {
                                vehicleType = foundItem.name;
                            } else {
                                var foundItem = data.vehicleInformation.vehicleType.find(item => item.type === defaultLanguageCode);
                                if (foundItem && foundItem.name != '') {
                                    vehicleType = foundItem.name;
                                } else {
                                    var foundItem = data.vehicleInformation.vehicleType.find(item => item.type === 'en');
                                    vehicleType = foundItem.name;
                                }
                            }

                        }

                        $(".vehicle_type").text(vehicleType);
                    }
                    if (data.zoneIds.length > 0) {
                        database.collection('zone').where('id', 'in', data.zoneIds).get().then(async function(snapshots) {
                            let zone_name = '';
                            if (snapshots.docs.length > 0) {
                                snapshots.docs.forEach((doc) => {
                                    zone = doc.data();
                                    var name = '';
                                    if (Array.isArray(zone.name)) {
                                        var foundItem = zone.name.find(item => item.type === setLanguageCode);
                                        if (foundItem && foundItem.name != '') {
                                            name = foundItem.name;
                                        } else {
                                            var foundItem = zone.name.find(item => item.type === defaultLanguageCode);
                                            if (foundItem && foundItem.title != '') {
                                                name = foundItem.name;
                                            } else {
                                                var foundItem = zone.name.find(item => item.type === 'en');
                                                name = foundItem.name;
                                            }
                                        }

                                    }

                                    zone_name += name + ', ';
                                });
                            }
                            zone_name = zone_name.replace(/, +$/, '');
                            $(".zone_name").text(zone_name);
                        });
                    }
                } else {
                    $("#vehicle_information").html('<tr><td><span class="font-weight-bold w-100">{{ trans('lang.no_vehicle_information') }}</span></td></tr>');

                }
                var driverhtml = '';
                if (data.vehicleInformation.driverRules != undefined && data.vehicleInformation.driverRules != null) {
                    var rulesLength = data.vehicleInformation.driverRules.length;

                    if (rulesLength > 0) {
                        for (i = 0; i < rulesLength; i++) {
                            name = data.vehicleInformation.driverRules[i].name;
                            var title = '';
                            if (Array.isArray(data.vehicleInformation.driverRules[i].name)) {

                                var foundItem = data.vehicleInformation.driverRules[i].name.find(item => item.type === setLanguageCode);
                                if (foundItem && foundItem.name != '') {
                                    title = foundItem.name;
                                } else {
                                    var foundItem = data.vehicleInformation.driverRules[i].name.find(item => item.type === defaultLanguageCode);
                                    if (foundItem && foundItem.name != '') {
                                        title = foundItem.name;
                                    } else {
                                        var foundItem = data.vehicleInformation.driverRules[i].name.find(item => item.type === 'en');
                                        title = foundItem.name;
                                    }
                                }

                            }

                            image = data.vehicleInformation.driverRules[i].image ||  placeholderImage;

                            driverhtml += '<li>';
                            driverhtml += '<span class="rule-img"><img  style="width:50px" src=" ' + image + ' " /></span>';
                            driverhtml += '<span class="font-weight-bold w-100">' + title + '</span>';
                            driverhtml += '</li>';
                            $("#driver_rules").html(driverhtml);
                        }

                    } else {
                        driverhtml += '<tr><td>{{ trans('lang.no_rules_found') }}</td></tr>';
                        $("#driver_rules").html(driverhtml);
                    }

                } else {
                    $("#driver_rules").html('{{ trans('lang.no_rules_found') }}');
                }
            } else {
                $("#driver_rules").html('{{ trans('lang.no_rules_found') }}');
                $("#vehicle_information").html('<tr><td><span class="font-weight-bold w-100">{{ trans('lang.no_vehicle_information') }}</span></td></tr>');

            }
            if (data.hasOwnProperty('subscriptionExpiryDate') && data.hasOwnProperty('subscriptionPlanId') && data.subscriptionPlanId != '' && data.subscriptionPlanId != null) {
                $(".update-limit-div").show();
                $(".plan_name").html(data.subscription_plan.name);
                $(".plan_type").html(data.subscription_plan.type);
                if (data.subscriptionExpiryDate != null && data.subscriptionExpiryDate != '') {
                    date = data.subscriptionExpiryDate.toDate().toDateString();
                    time = data.subscriptionExpiryDate.toDate().toLocaleTimeString('en-US');
                    $(".plan_expire_at").html(date + ' ' + time);
                    $(".plan_expire_date").html(date);
                } else {
                    $(".plan_expire_at").html("{{ trans('lang.unlimited') }}");
                    $(".plan_expire_date").html("{{ trans('lang.unlimited') }}");
                }
                var number_of_days = data.subscription_plan.expiryDay == "-1" ? '{{ trans('lang.unlimited') }}' : data.subscription_plan.expiryDay + " {{ trans('lang.days') }}";
                $(".number_of_days").html(number_of_days);
                if (symbolAtRight) {
                    $(".plan_price").html(parseFloat(data.subscription_plan.price).toFixed(decimal_degits) + currentCurrency);
                } else {
                    $(".plan_price").html(currentCurrency + parseFloat(data.subscription_plan.price).toFixed(decimal_degits));
                }
                $('.booking_limit').html((data.subscription_plan.bookingLimit == '-1') ? "{{ trans('lang.unlimited') }}" : data.subscription_plan.bookingLimit);
                if (data.subscription_plan.hasOwnProperty('plan_points') && data.subscription_plan.plan_points != null) {
                    var html = `<ul class="pricing-card-list text-dark-2">`;
                    data.subscription_plan.plan_points.map(async (list) => {
                        html += `<li>${list}</li>`
                    });
                    html += `</ul>`;
                    $('.plan-points').html(html);
                }
            } else {
                $('.active_subscription_div').html('{{ trans('lang.no_active_subscription_plan_found') }}')
            }

        });
        refBankDetails.get().then(async function(snapshot) {
            let data = snapshot.data();
            if (data != undefined && (data.bankName != "" || data.branchName != '' || data.accountNumber != "" || data.holderName != "" || data.otherInformation != "")) {
                $(".bank_name").text(data.bankName);
                $(".branch_name").text(data.branchName);
                $(".account_number").text(data.accountNumber);
                $(".holder_name").text(data.holderName);
                $(".other_info").text(data.otherInformation);
            } else {
                $("#bank_information").html('<tr><td><span class="font-weight-bold w-100">{{ trans('lang.no_bank_details') }}</span></td></tr>');

            }


        });
        reviewRef.get().then(async function(docSnapshot) {
            let html = '';
            if (docSnapshot.docs.length > 0) {
                docSnapshot.docs.forEach((docs) => {
                    var data = docs.data();

                    getReviwerInfo(data.customerId);
                    html += '<tr>';

                    html += '<td><a href="/users/edit/' + data.customerId + '" class="reviewer_name"></a></td>';
                    html += '<td>' + data.comment + '</td>';
                    html += '<td>' + getStars(data.rating) + '</td>';
                    html += '</tr>';
                    $("#review_list_rows").html(html);
                });

            } else {
                html += '<tr><td colspan="6" class="text-center font-weight-bold">{{ trans('lang.no_record_found') }}</td></tr>';
                $("#review_list_rows").html(html);
            }
        });

        async function getReviwerInfo(id) {
            await database.collection('users').where('id', '==', id).get().then(async function(snapshots) {
                var user = snapshots.docs[0].data();
                $('.reviewer_name').html(user.fullName)

            });
        }
        $(document).on('click', '.ride_list', function() {
            getRideList();
        });
        $(document).on('click', '.intercity_ride_list', function() {
            getIntercityRideList();
        });
        

        function getRideList() {

            $("#ride_list_rows").html('');

            jQuery("#overlay").show();
            rideRef.get().then(async function(docSnapshot) {
                let html = '';

                html = await buildRidesHtml(docSnapshot);
                if (html != '') {
                    $("#ride_list_rows").html(html);

                }

                var table = $('#rideListTable').DataTable();

                table.destroy();
                //$('#rideListTable').empty();

                table = $('#rideListTable').DataTable({
                    order: [],
                    columnDefs: [{
                        targets: 3,
                        type: 'date',
                        render: function(data) {


                            return data;
                        }
                    }, {
                        orderable: false,
                        targets: [4, 5]
                    }, ],
                    language: {
                        "zeroRecords": "{{ trans('lang.no_record_found') }}",
                        "emptyTable": "{{ trans('lang.no_record_found') }}"
                    },
                    order: [
                        [3, "desc"]
                    ],

                    responsive: true

                });

                jQuery("#overlay").hide();

            });
        }

        async function buildRidesHtml(snapshots) {
            var html = '';
            await Promise.all(snapshots.docs.map(async (listval) => {
                var val = listval.data();
                var getData = await getRidesListData(val);
                html += getData;
            }));
            return html;
        }

        async function getRidesListData(data) {

            var html = '';

            html += '<tr>';

            html += '<td><a href="/rides/show/' + data.id + '" target="_blank">' + data.id.substring(0, 7) + '</a></td>';
           
            var customer = await getUser(data.userId, data.id);
            if (customer != '') {
                html += '<td class="redirecttopage user_name_' + data.id + '"><a href="javascript:void(0)">' + customer + '</a></td>';
            } else {
                html += '<td class="redirecttopage user_name_' + data.id + '">{{ trans('lang.unknown_user') }}</td>';
            }

            var title = '';
            if (Array.isArray(data.service.title)) {
                var serviceName = data.service.title;
                var foundItem = serviceName.find(item => item.type === setLanguageCode);
                if (foundItem && foundItem.title != '') {
                    title = foundItem.title;
                } else {
                    var foundItem = serviceName.find(item => item.type === defaultLanguageCode);
                    if (foundItem && foundItem.title != '') {
                        title = foundItem.title;
                    } else {
                        var foundItem = serviceName.find(item => item.type === 'en');
                        title = foundItem.title;
                    }
                }

            }

            html += '<td>' + title + '</td>';

            var date = data.createdDate.toDate().toDateString();
            var time = data.createdDate.toDate().toLocaleTimeString('en-US');

            html = html + '<td class="dt-time">' + date + ' ' + time + '</td>';


            if (data.status == "Ride Placed") {
                html += '<td><span class="badge badge-primary py-2 px-3">' + data.status + '</span></td>';
            } else if (data.status == "Ride Completed") {
                html += '<td><span  class="badge badge-success py-2 px-3">' + data.status + '</span></td>';
            } else if (data.status == "Ride Active") {
                html += '<td><span class="badge badge-warning py-2 px-3">' + data.status + '</span></td>';
            } else if (data.status == "Ride InProgress") {
                html += '<td><span class="badge badge-info py-2 px-3">' + data.status + '</span></td>';
            } else if (data.status == "Ride Canceled") {
                html += '<td><span class="badge badge-danger py-2 px-3">' + data.status + '</span></td>';
            }

            if (data.hasOwnProperty('paymentType')) {
                var image = await getPaymentImage(data.id.substring(0, 7), data.paymentType);
                html += '<td class="payment_icon ' + data.id.substring(0, 7) + '_' + data.paymentType + '"><img width="80" src="' + image + '" alt="image"></td>';
            } else {
                html += '<td>-</td>';
            }

            if (data.hasOwnProperty('paymentStatus') && data.paymentStatus == true) {
                html += '<td><span class="badge badge-success py-2 px-3">{{ trans('lang.paid') }}</span></td>';
            } else {
                html += '<td><span class="badge badge-warning py-2 px-3">{{ trans('lang.not_paid') }}</span></td>';
            }

            var amount = await getOrderDetails(data);

            if (symbolAtRight) {
                html += '<td>' + amount + currentCurrency + '</td>';

            } else {
                html += '<td>' + currentCurrency + amount + '</td>';

            }

            html += '</tr>';

            return html;
        }

        async function getUser(userId, id) {
            var userName = '';
            await database.collection('users').where('id', '==', userId).get().then(async function(snapshots) {

                if (snapshots.docs.length > 0) {
                    var user = snapshots.docs[0].data();
                    userName = user.fullName;
                }

            });
            return userName;
        }

        function getIntercityRideList() {

            $("#intercity_ride_list_rows").html('');

            jQuery("#overlay").show();
            intercityRideRef.get().then(async function(docSnapshot) {
                let html = '';

                html = await buildIntercityRidesHtml(docSnapshot);
                if (html != '') {
                    $("#intercity_ride_list_rows").html(html);

                }

                var table = $('#intercityRideListTable').DataTable();

                table.destroy();
                //$('#rideListTable').empty();

                table = $('#intercityRideListTable').DataTable({
                    order: [],
                    columnDefs: [{
                        targets: 3,
                        type: 'date',
                        render: function(data) {
                            return data;
                        }
                    }, {
                        orderable: false,
                        targets: [4, 5]
                    }, ],
                    language: {
                        "zeroRecords": "{{ trans('lang.no_record_found') }}",
                        "emptyTable": "{{ trans('lang.no_record_found') }}"
                    },
                    order: [
                        [3, "desc"]
                    ],

                    responsive: true

                });

                jQuery("#overlay").hide();

            });
        }

        async function buildIntercityRidesHtml(snapshots) {
            var html = '';
            await Promise.all(snapshots.docs.map(async (listval) => {
                var val = listval.data();
                var getData = await getIntercityRidesListData(val);
                html += getData;
            }));
            return html;
        }

        async function getIntercityRidesListData(data) {

            var html = '';

            html += '<tr>';

            html += '<td><a href="/intercity-service-rides/view/' + data.id + '" target="_blank">' + data.id.substring(0, 7) + '</a></td>';

           
            var customer = await getUser(data.userId, data.id);
            if (customer != '') {
                html += '<td class="redirecttopage user_name_' + data.id + '"><a href="javascript:void(0)">' + customer + '</a></td>';
            } else {
                html += '<td class="redirecttopage user_name_' + data.id + '">{{ trans('lang.unknown_user') }}</td>';
            }
            var title = '';
            if (Array.isArray(data.intercityService.name)) {
                var serviceName = data.intercityService.name;
                var foundItem = serviceName.find(item => item.type === setLanguageCode);
                if (foundItem && foundItem.name != '') {
                    title = foundItem.name;
                } else {
                    var foundItem = serviceName.find(item => item.type === defaultLanguageCode);
                    if (foundItem && foundItem.name != '') {
                        title = foundItem.name;
                    } else {
                        var foundItem = serviceName.find(item => item.type === 'en');
                        title = foundItem.name;
                    }
                }

            }

            html += '<td>' + title + '</td>';

            var date = '';
            var time = '';
            if (data.hasOwnProperty("createdDate")) {
                try {
                    date = data.createdDate.toDate().toDateString();
                    time = data.createdDate.toDate().toLocaleTimeString('en-US');
                } catch (err) {

                }
                html = html + '<td class="dt-time">' + date + ' ' + time + '</td>';
            } else {
                html = html + '<td></td>';
            }


            if (data.status == "Ride Placed") {
                html += '<td><span class="badge badge-primary py-2 px-3">' + data.status + '</span></td>';
            } else if (data.status == "Ride Completed") {
                html += '<td><span  class="badge badge-success py-2 px-3">' + data.status + '</span></td>';
            } else if (data.status == "Ride Active") {
                html += '<td><span class="badge badge-warning py-2 px-3">' + data.status + '</span></td>';
            } else if (data.status == "Ride InProgress") {
                html += '<td><span class="badge badge-info py-2 px-3">' + data.status + '</span></td>';
            } else if (data.status == "Ride Canceled") {
                html += '<td><span class="badge badge-danger py-2 px-3">' + data.status + '</span></td>';
            }

            if (data.hasOwnProperty('paymentType')) {
                var image = await getPaymentImage(data.id.substring(0, 7), data.paymentType);
                html += '<td class="payment_icon ' + data.id.substring(0, 7) + '_' + data.paymentType + '"><img width="80" src="' + image + '" alt="image"></td>';
            } else {
                html += '<td>-</td>';
            }

            if (data.hasOwnProperty('paymentStatus') && data.paymentStatus == true) {
                html += '<td><span class="badge badge-success py-2 px-3">{{ trans('lang.paid') }}</span></td>';
            } else {
                html += '<td><span class="badge badge-warning py-2 px-3">{{ trans('lang.not_paid') }}</span></td>';
            }

            var amount = await getOrderDetails(data);

            if (symbolAtRight) {
                html += '<td>' + amount + currentCurrency + '</td>';

            } else {
                html += '<td>' + currentCurrency + amount + '</td>';

            }

            html += '</tr>';

            return html;
        }

        async function getOrderDetails(orderData) {

            var amount = 0;
            var total_amount = 0;

            if (orderData.offerRate) {
                amount = parseFloat(orderData.offerRate);

            }
            if (orderData.finalRate) {
                amount = parseFloat(orderData.finalRate);

            }

            total_amount = amount;

            var discount_amount = 0;
            if (orderData.hasOwnProperty('coupon') && orderData.coupon.enable) {
                var data = orderData.coupon;

                if (data.type == "fix") {
                    discount_amount = data.amount;
                } else {
                    discount_amount = (data.amount * amount) / 100;
                }

                total_amount -= parseFloat(discount_amount);

            }


            if (orderData.hasOwnProperty('taxList') && orderData.taxList.length > 0) {
                var taxData = orderData.taxList;

                var tax_amount_total = 0;
                for (var i = 0; i < taxData.length; i++) {

                    var data = taxData[i];

                    if (data.enable) {

                        var tax_amount = data.tax;

                        if (data.type == "percentage") {

                            tax_amount = (data.tax * total_amount) / 100;
                        }

                        tax_amount_total += parseFloat(tax_amount);

                    }
                }
                total_amount += parseFloat(tax_amount_total);


            }
            total_amount = total_amount.toFixed(decimal_degits);

            return total_amount;
        }

        async function getPaymentImage(id, paymentType) {
            await database.collection('settings').doc('payment').get().then(async function(snapshots) {
                var payment = snapshots.data();
                type = paymentType.toLowerCase();
                if (type == "flutterwave") {
                    type = "flutterWave";
                } else if (type == "stripe") {
                    type = "strip";
                } else if (type == "paystack") {
                    type = "payStack";
                } else if (type == "mercadopago") {
                    type = "mercadoPago";
                }
                payment = payment[type];
                payImage = payment.image;
            });
            return payImage;
        }

        function getStars(rating) {
            rating = Math.round(rating * 2) / 2;
            let output = [];
            for (var i = rating; i >= 1; i--)
                output.push('<i class="fa fa-star" aria-hidden="true" style="color: gold;"></i>&nbsp;');
            if (i == .5) output.push('<i class="fa fa-star-half-o" aria-hidden="true" style="color: gold;"></i>&nbsp;');
            for (let i = (5 - rating); i >= 1; i--)
                output.push('<i class="fa fa-star-o" aria-hidden="true" style="color: gold;"></i>&nbsp;');
            return output.join('');
        }
       
        $('input[name="set_booking_limit"]').on('change', function() {
            if ($('#limited_booking').is(':checked')) {
                $('.booking-limit-div').removeClass('d-none');
            } else {
                $('.booking-limit-div').addClass('d-none');
            }
        });
        $("#updateLimitModal").on('shown.bs.modal', function() {
            database.collection('driver_users').where('id', '==', id).get().then(async function(snapshot) {
                var data = snapshot.docs[0].data();
                if (data.subscription_plan.bookingLimit != '-1') {
                    $("#limited_booking").prop('checked', true);
                    $('.booking-limit-div').removeClass('d-none');
                    $('#booking_limit').val(data.subscription_plan.bookingLimit);
                } else {
                    $("#unlimited_booking").prop('checked', true);
                }

            })
        })
        $('.update-plan-limit').click(async function() {

            var set_booking_limit = $('input[name="set_booking_limit"]:checked').val();
            var booking_limit = (set_booking_limit == 'limited') ? $('#booking_limit').val() : '-1';


            if (set_booking_limit == 'limited' && $('#booking_limit').val() == '') {
                $(".booking_limit_err").html("<p>{{ trans('lang.enter_booking_limit') }}</p>");
                return false;
            } else {
                await database.collection('driver_users').doc(id).update({
                    'subscription_plan.bookingLimit': booking_limit,
                    'subscriptionTotalOrders': booking_limit
                }).then(async function(result) {
                    window.location.reload();
                });
            }
        })
    </script>
@endsection
