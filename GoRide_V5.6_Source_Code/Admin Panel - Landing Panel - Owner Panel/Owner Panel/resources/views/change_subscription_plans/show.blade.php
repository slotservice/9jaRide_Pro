<!doctype html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title id="app_name"><?php echo @$_COOKIE['meta_title']; ?></title>
        <link rel="icon" id="favicon" type="image/x-icon" href="<?php echo str_replace('images/', 'images%2F', @$_COOKIE['favicon']); ?>">
        <!-- Fonts -->
        <link rel="dns-prefetch" href="//fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
        <!-- Styles -->
        <link href="{{ asset('assets/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
        <link href="{{ asset('css/style.css') }}" rel="stylesheet">
        <link href="{{ asset('css/animate.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/plugins/select2/dist/css/select2.min.css') }}" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Urbanist:ital,wght@0,100..900;1,100..900&display=swap"
            rel="stylesheet">
        <!-- @yield('style') -->
    </head>

    <body>

        <div class="page-wrapper py-5 pl-0">
            <div class="container-fluid">
                <div id="data-table_processing" class="page-overlay" style="display:none;">
                    <div class="overlay-text">
                        <img src="{{ asset('images/spinner.gif') }}">
                    </div>
                </div>
                <div class="col-lg-11 ml-lg-auto mr-lg-auto">
                    <div class="title text-center mb-5">
                        <h2 class="text-primary">{{ trans('lang.business_plans') }}</h2>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex top-title-section pb-4 mb-4 justify-content-between">
                                <div class="d-flex top-title-left align-start-center">
                                    <div class="top-title">
                                        <h3 class="mb-0">{{ trans('lang.choose_your_business_plan') }}</h3>
                                        <p class="mb-0 text-dark-2">
                                            {{ trans('lang.choose_your_business_plan_description') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="row" id="default-plan"></div>
                        </div>
                    </div>
                    <div class="row backBtn d-none">
                        <div class="col-12 text-center"><a href="{{ url('/') }}" class="btn btn-primary">{{trans('lang.cancel')}}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="{{ asset('assets/plugins/select2/dist/js/select2.min.js') }}"></script>
        <script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-app-compat.js"></script>
        <script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-firestore-compat.js"></script>
        <script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-storage-compat.js"></script>
        <script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-auth-compat.js"></script>
        <script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-database-compat.js"></script>
        <script src="{{ asset('js/crypto-js.js') }}"></script>
        <script src="{{ asset('js/jquery.cookie.js') }}"></script>
        <script src="{{ asset('js/jquery.validate.js') }}"></script>

        <script type="text/javascript">
            var database=firebase.firestore();
            var currentCurrency='';
            var currencyAtRight=false;
            var decimal_degits=0;
            var userId="{{ $userId }}";

            var createdAt=firebase.firestore.FieldValue.serverTimestamp();
            var vendorId=null;
            var refCurrency=database.collection('currency').where('enable','==',true);
            refCurrency.get().then(async function(snapshots) {
                var currencyData=snapshots.docs[0].data();
                currentCurrency=currencyData.symbol;
                currencyAtRight=currencyData.symbolAtRight;

                if(currencyData.decimal_degits) {
                    decimal_degits=currencyData.decimal_degits;
                }
            });
            var commisionModel=false;
            var AdminCommission='';
            var commissionBusinessModel=database.collection('settings').doc("adminCommission");
            var subscriptionModel=false;
            var subscriptionBusinessModel=database.collection('settings').doc("globalValue");

            var activeSubscriptionId='';
            var subscriptionHistory=database.collection('subscription_history').where('user_id','==',userId).orderBy(
                'createdAt','desc');
            subscriptionHistory.get().then(async function(snapshot) {
                if(snapshot.docs.length>0) {
                    var data=snapshot.docs[0].data();
                    activeSubscriptionId=data.subscription_plan.id;
                }
            });
            database.collection('owner_users').where('id','==',userId).get().then(async function(snapshot) {
                var userData=snapshot.docs[0].data();
                
            });
            var ref=database.collection('settings').doc("globalSettings");

            $(document).ready(async function() {

                jQuery('#data-table_processing').show();
                await commissionBusinessModel.get().then(async function(snapshots) {
                    var commissionSetting=snapshots.data();
                    if(commissionSetting.isEnabled==true) {
                        commisionModel=true;
                    }
                  
                   let amount = parseFloat(commissionSetting.amount) || 0;

                    if (commissionSetting.type.toLowerCase() == "percent" || commissionSetting.type.toLowerCase() == "percentage") {
                        AdminCommission = amount + '%';
                    } else {
                        if (currencyAtRight) {
                            AdminCommission = amount.toFixed(decimal_degits) + currentCurrency;
                        } else {
                            AdminCommission = currentCurrency + amount.toFixed(decimal_degits);
                        }
                    }
                });
                await subscriptionBusinessModel.get().then(async function(snapshots) {
                    var businessModelSettings=snapshots.data();
                    if(businessModelSettings.hasOwnProperty('subscription_model')&&
                        businessModelSettings.subscription_model==true) {
                        subscriptionModel=true;
                    }
                });


                if(commisionModel==false&&subscriptionModel==false) {
                    var isSubscribed="";


                    var url="{{ route('setSubcriptionFlag') }}";
                    $.ajax({

                        type: 'POST',

                        url: url,

                        data: {

                            email: "{{Auth::user()->email}}",
                            isSubscribed: isSubscribed
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },

                        success: function(data) {
                            if(data.access) {
                                window.location="{{ route('home') }}";
                            }
                        }

                    })
                }


                database.collection('subscription_plans').where('isEnable','==',true).where('planFor', 'in', ['both', 'owner']).get().then(async function(
                    snapshots) {

                    let plans=[];
                    snapshots.docs.map(doc => {
                        let data=doc.data();
                        plans.push({
                            ...data
                        }); 
                    });

                    plans.sort((a,b) => a.place-b.place);

                    var html='';
                    var activeClass='';
                    plans.map(async (data) => {
                        
                        var activeClass=(data.id==activeSubscriptionId)?
                        '<span class="badge badge-success">{{ trans('lang.active') }}</span>':
                        '';
                        if(data.id=="J0RwvxCWhZzQQD7Kc2Ll") {

                            if(commisionModel) {


                                html+=`<div class="col-md-3 mb-3 pricing-card pricing-card-commission">
                                            <div class="pricing-card-inner">
                                                <div class="pricing-card-top">
                                                    <div class="d-flex align-items-center pb-4">
                                                        <span class="pricing-card-icon mr-4"><img src="${data.image}"></span>
                                                    </div>
                                                    <div class="pricing-card-price">
                                                        <h3 class="text-dark-2">${data.name} ${activeClass}</h3>
                                                        <span class="price-day">${data.description}</span>
                                                    </div>
                                                </div>
                                                <div class="pricing-card-content pt-3 mt-3 border-top">
                                                    <ul class="pricing-card-list text-dark-2">`;
                                html+=
                                    `<li><span class="mdi mdi-check"></span>{{ trans('lang.pay_commission_of') }} ${AdminCommission} {{ trans('lang.on_each_order') }} </li>`
                                data.plan_points.map(async (list) => {
                                    html+=
                                        `<li><span class="mdi mdi-check"></span>${list}</li>`
                                });
                                html+=
                                    `<li><span class="mdi mdi-check"></span>{{ trans('lang.unlimited') }} {{ trans('lang.orders') }}</li>`
                                html+=
                                    `<li><span class="mdi mdi-check"></span>{{ trans('lang.unlimited') }} {{ trans('lang.products') }}</li>`

                                html+=`</ul>
                                                </div>`;
                                var buttonText=(activeClass=='')?
                                    "{{ trans('lang.select_plan') }}":
                                    "{{ trans('lang.renew_plan') }}";

                                html+=`<div class="pricing-card-btm">
                                                    <a href="javascript:void(0)" onClick="saveSubscriptionPlan('${data.id}')" class="btn rounded-full active-btn btn-primary">${buttonText}</a>
                                                </div>`;

                                html+=`</div>
                                </div>`;
                            }
                        } else {
                            if(subscriptionModel) {
                               
                                var buttonText=(activeClass=='')?
                                    "{{ trans('lang.select_plan') }}":
                                    "{{ trans('lang.renew_plan') }}";

                                if(data.type=="free") {

                                    var routeHtml=
                                        `<a href="javascript:void(0)" onClick="saveSubscriptionPlan('${data.id}')" class="btn rounded-full">${buttonText}</a>`
                                } else {
                                    var route=
                                        "{{ route('subscription-plans.checkout', ':id') }}";
                                    route=route.replace(":id",data.id);
                                    var routeHtml=
                                        `<a href="${route}" class="btn rounded-full">${buttonText}</a>`
                                }


                                html+=`<div class="col-md-3 mb-3  pricing-card pricing-card-subscription ${data.name}">
                                    <div class="pricing-card-inner">
                                        <div class="pricing-card-top">
                                        <div class="d-flex align-items-center pb-4">
                                            <span class="pricing-card-icon mr-4"><img src="${data.image}"></span>
                                            <h2 class="text-dark-2">${data.name} ${activeClass}</h2>
                                        </div>
                                        <p class="text-muted">${data.description}</p>
                                        <div class="pricing-card-price">
                                            <h3 class="text-dark-2">${data.type!=="free"? (currencyAtRight? parseFloat(data.price).toFixed(decimal_degits)+currentCurrency:currentCurrency+parseFloat(data.price).toFixed(decimal_degits)):'<span style="color:red;">Free</span>'}</h3>
                                            <span class="price-day">${data.expiryDay==-1? "{{ trans('lang.unlimited') }}":data.expiryDay} {{trans('lang.days')}}</span>
                                        </div>
                                        </div>
                                        <div class="pricing-card-content pt-3 mt-3 border-top">
                                        <ul class="pricing-card-list text-dark-2">`;
                                        data.plan_points.map(async (list) => {
                                            html += `<li><span class="mdi mdi-check"></span>${list}</li>`
                                        });
                                        html += `<li><span class="mdi mdi-check"></span>${data.bookingLimit==-1? "{{ trans('lang.unlimited') }}":data.bookingLimit} {{ trans('lang.bookings') }}</li>`;

                                        html += `<li><span class="mdi mdi-check"></span>${data.driverLimit==-1? "{{ trans('lang.unlimited') }}":data.driverLimit} {{ trans('lang.driver_plural') }}</li>`
                                           
                                 html +=       `</ul>
                                        </div>`;

                                html+=`<div class="pricing-card-btm">${routeHtml}</div>`;

                                html+=`</div>
                                </div>`;
                            }
                        }
                    });
                    (activeSubscriptionId=='')? $('.backBtn').addClass('d-none'):$('.backBtn').removeClass('d-none')
                    $('#default-plan').append(html);
                    jQuery('#data-table_processing').hide();
                });
            });

            function setCookie(cname,cvalue,exdays) {
                const d=new Date();
                d.setTime(d.getTime()+(exdays*24*60*60*1000));
                let expires="expires="+d.toUTCString();
                document.cookie=cname+"="+cvalue+";"+expires+";path=/";
            }

            var id_order=database.collection('tmp').doc().id;

            async function saveSubscriptionPlan(id) {
                await database.collection('subscription_plans').where('id','==',id).get().then(async function(snapshot) {
                    planData=snapshot.docs[0].data();
                    var currentDate=new Date();
                    if(planData.expiryDay!='-1') {
                        currentDate.setDate(currentDate.getDate()+parseInt(planData.expiryDay));
                        expiryDay=firebase.firestore.Timestamp.fromDate(currentDate);
                    } else {
                        expiryDay=null;
                    }
                    await database.collection('owner_users').doc(userId).update({
                        'subscription_plan': planData,
                        'subscriptionPlanId': id,
                        'subscriptionExpiryDate': expiryDay,
                        'subscriptionTotalOrders':planData.bookingLimit

                    }).then(async function(result) {                       
                        
                        
                        await database.collection('subscription_history').doc(id_order).set({
                            'id': id_order,
                            'user_id': userId,
                            'expiry_date': expiryDay,
                            'createdAt': createdAt,
                            'subscription_plan': planData,
                            'payment_type': 'free'
                        }).then(async function(snapshot) {
                            var url="{{ route('setSubcriptionFlag') }}";

                            $.ajax({

                                type: 'POST',
                                url: url,
                                data: {
                                    email: "<?php echo Auth::user()->email; ?>",
                                    isSubscribed: 'true'
                                },

                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]')
                                        .attr('content')
                                },

                                success: function(data) {
                                    if(data.access) {
                                        window.location.href=
                                            '{{ route('dashboard') }}';
                                    }
                                }
                            });

                        });
                    });
                })

            }

        </script>

    </body>

</html>