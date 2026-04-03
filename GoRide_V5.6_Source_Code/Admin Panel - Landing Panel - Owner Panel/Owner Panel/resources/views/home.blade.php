@extends('layouts.app')
@section('content')
    <div id="main-wrapper" class="page-wrapper bg-light-gray" style="min-height: 207px;">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">{{ trans('lang.welcome_admin_note') }},{{ Auth::user()->name }}!</h3>
                <p>{{ trans('lang.welcome_admin_note2') }}</p>
            </div>
        </div>
        <div class="container-fluid">
            <div class="top-filter">
                <div class="row">
                    <div class="col-lg-12">

                        <div class="sis-card-head-select-box d-flex align-items-center gap-2 mb-4">
                            <div class="head-select-box">
                                <label class="mb-0 text-dark-2">{{ trans('lang.filter_by') }}</label>
                                <select id="viewFilter" name="view" class="form-control">
                                    <option value="">{{ trans('lang.all') }}</option>
                                    <option value="year">{{ trans('lang.view_full_year') }}</option>
                                    <option value="month">{{ trans('lang.view_by_month') }}</option>
                                    <option value="custom">{{ trans('lang.custom_date_range') }}</option>
                                </select>
                            </div>

                            <div id="monthYearFilters" class="head-select-box" style="display:inline-block;">
                                <select id="monthFilter" name="month" class="form-control" style="display: none;">
                                    <option value="1">{{ trans('lang.january') }}</option>
                                    <option value="2">{{ trans('lang.february') }}</option>
                                    <option value="3">{{ trans('lang.march') }}</option>
                                    <option value="4">{{ trans('lang.april') }}</option>
                                    <option value="5">{{ trans('lang.may') }}</option>
                                    <option value="6">{{ trans('lang.june') }}</option>
                                    <option value="7">{{ trans('lang.july') }}</option>
                                    <option value="8">{{ trans('lang.august') }}</option>
                                    <option value="9">{{ trans('lang.september') }}</option>
                                    <option value="10">{{ trans('lang.october') }}</option>
                                    <option value="11">{{ trans('lang.november') }}</option>
                                    <option value="12">{{ trans('lang.december') }}</option>

                                </select>
                                <select id="yearFilter" name="year" class="form-control">

                                </select>
                            </div>

                            <div id="customDateFilters" class="head-select-box" style="display: none;">
                                <input class="form-control" type="date" name="start_date" id="startDate" value="">
                                <input class="form-control" type="date" name="end_date" id="endDate" value="">
                            </div>
                            <button type="button" id="applyFilterBtn" class="btn btn-primary">{{trans('lang.apply_filter')}}</button>
                            <a href="{!! route('dashboard') !!}" class="btn btn-secondary">{{trans('lang.clear_filter')}}</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="row">

                        <div class="col-md-4" onclick="location.href='{!! route('rides') !!}'">
                            <div class="card card-box-with-icon border-0">
                                <div class="card-body p-3">
                                    <span class="box-icon ab"><img src="{{ asset('images/total_ride.png') }}"></span>
                                    <div class="card-box-with-content mt-3">
                                        <h4 class="card-left-title text-dark font-medium">
                                            {{trans('lang.dashboard_total_orders')}} ({{ trans('lang.intercity') }})
                                        </h4>
                                        <h3 class="m-b-0 text-dark-2 font-bold mb-2 total_earning" id="total_rides">00</h3>
                                        <h6 id="rides_percent" class="green up-down-list font-semibold"></h6>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="col-md-4" onclick="location.href='{!! route('drivers') !!}'">
                            <div class="card card-box-with-icon border-0">
                                <div class="card-body p-3">
                                    <span class="box-icon ab"><img src="{{ asset('images/total_driver.png') }}"></span>
                                    <div class="card-box-with-content mt-3">

                                        <h4 class="card-left-title text-dark font-medium">
                                            {{trans('lang.dashboard_total_drivers')}} ({{ trans('lang.intercity') }})
                                        </h4>
                                        <h3 class="m-b-0 text-dark-2 font-bold mb-2 total_earning" id="driver_count">
                                            00</h3>

                                        <h6 id="driver_percent" class="green up-down-list font-semibold"></h6>

                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">

                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <input type="hidden" name="earnings_count" id="earnings_count">
                            <input type="hidden" name="earnings_count_intercity" id="earnings_count_intercity">
                            <input type="hidden" name="admincommission_count" id="admincommission_count">
                            <input type="hidden" name="admincommission_count_intercity"
                                id="admincommission_count_intercity">
                            <div class="card border">
                                <div class="card-header no-border">
                                    <div class="d-flex justify-content-between">
                                        <h3 class="card-title">{{ trans('lang.dashboard_total_sales') }}</h3>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="position-relative">
                                        <canvas id="sales-chart" height="280"></canvas>
                                    </div>

                                    <div class="d-flex flex-row justify-content-end">
                                        <span class="mr-2"> <i class="fa fa-square" style="color:#80b140"></i>
                                            {{ trans('lang.dashboard_this_year') }} </span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

                <div class="col-md-4">
                    <div class="card border">
                        <div class="card-header no-border">
                            <h3 class="card-title">{{ trans('lang.total_earning_admin_commission') }}</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="total-earnings-chart" height="350"></canvas>

                            <div class="chart-legend mt-3 d-flex justify-content-around">
                                <div class="legend-item">
                                    <span class="legend-color" style="background-color: #4e73df;"></span>
                                    <span class="legend-text">{{ trans('lang.dashboard_total_sales') }}: <span
                                            id="legend-total-earnings">0</span></span>
                                </div>
                                <div class="legend-item">
                                    <span class="legend-color" style="background-color: #f6c23e;"></span>
                                    <span class="legend-text">{{ trans('lang.admin_commission') }}: <span
                                            id="legend-admin-commission">0</span></span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>


            </div>

            <div class="row">

                <div class="col-lg-6">
                    <div class="card border">
                        <div class="card-header no-border">
                            <div class="d-flex justify-content-between">
                                <h3 class="card-title">{{ trans('lang.dashboard_service_overview') }}</h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="flex-row">
                                <canvas id="service-overview" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card border">
                        <div class="card-header no-border">
                            <div class="d-flex justify-content-between">
                                <h3 class="card-title">{{ trans('lang.dashboard_sales_overview') }}</h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="flex-row">
                                <canvas id="sales-overview" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="row daes-sec-sec">
                <div class="col-md-8 col-lg-8">
                    <div class="card border">
                        <div class="card-header no-border d-flex justify-content-between">
                            <h3 class="card-title">{{ trans('lang.dashboard_recent_rides') }}</h3>
                            <a href="{!! route('rides') !!}">View all</a>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped table-valign-middle">
                                <thead>
                                    <tr>
                                        <th style="text-align:center">{{ trans('lang.order_id') }}</th>
                                        <th>{{ trans('lang.dashboard_user') }}</th>
                                        <th>{{ trans('lang.dashboard_driver') }}</th>
                                        <th>{{ trans('lang.pickup_location') }}</th>
                                        <th>{{ trans('lang.dropup_location') }}</th>
                                    </tr>
                                </thead>
                                <tbody id="append_list_recent_rides">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-4">
                    <div class="card border">
                        <div class="card-header no-border d-flex justify-content-between">
                            <h3 class="card-title">{{ trans('lang.dashboard_top_drivers') }}</h3>
                            <a href="{!! route('drivers') !!}">{{trans('lang.view_all')}}</a>

                        </div>
                        <div class="card-body">
                            <table class="table table-striped table-valign-middle">
                                <thead>
                                    <tr>
                                        <th>{{ trans('lang.driver_name') }}</th>
                                        <th>{{ trans('lang.rating') }}</th>
                                        <th>{{ trans('lang.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody id="append_list_top_drivers">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                </div>
            </div>

        </div>

    </div>
@endsection

@section('scripts')

    <script src="{{ asset('js/chart.js') }}"></script>
    
    <script type="text/javascript">
    
        var ownerId = "{{$id}}"
    
        jQuery("#overlay").show();

        var database = firebase.firestore();
        let salesChartInstance = null;
        let salesOverviewChart = null;
        let serviceOverviewChart = null;
        let totalEarningsChart = null;

        var currency = database.collection('settings');
        const todayDate = new Date();
        todayDate.setHours(0, 0, 0, 0);

        var rides_data = [];
        var intercity_data = [];
        var currentCurrency = '';
        var currencyAtRight = false;
        var decimal_degits = 0;
        var refCurrency = database.collection('currency').where('enable', '==', true);
        refCurrency.get().then(async function (snapshots) {
            var currencyData = snapshots.docs[0].data();
            currentCurrency = currencyData.symbol;
            currencyAtRight = currencyData.symbolAtRight;
            if (currencyData.decimalDigits) {
                decimal_degits = currencyData.decimalDigits;
            }
        });
        $(document).ready(function () {

            jQuery("#overlay").show();

            //todal records
            loadDashboardData(null);
            getTotalEarnings(null);
            loadRecentRidesAndTopDrivers(null);

            const yearFilter = $('#yearFilter');
            const currentYear = new Date().getFullYear();
            const numberOfYears = 5;
            yearFilter.empty();

            for (let i = 0; i <= numberOfYears; i++) {
                yearFilter.append(`<option value="${currentYear - i}">${currentYear - i}</option>`);
            }
            
            $('#viewFilter').on('change', function () {
                const selected = $(this).val();
                if (selected === 'year') {
                    $('#monthFilter').hide();
                    $('#yearFilter').show();
                    $('#monthYearFilters').show();
                    $('#customDateFilters').hide();
                } else if (selected === 'month') {
                    $('#monthFilter').show();
                    $('#yearFilter').show();
                    $('#monthYearFilters').show();
                    $('#customDateFilters').hide();
                } else if (selected === 'custom') {
                    $('#monthYearFilters').hide();
                    $('#customDateFilters').show();
                }else{
                    $('#monthFilter').hide();
                    $('#yearFilter').hide();
                    $('#monthYearFilters').hide();
                    $('#customDateFilters').hide();
                }
            });

            $('#viewFilter').trigger('change');
            
        });
        
        function loadRecentRidesAndTopDrivers(filterType = 'year', year = null, month = null, startDate = null, endDate = null) {
            var offset = 1;
            var pagesize = 5;
            var limit = parseInt(offset) * parseInt(pagesize);

            // --- Recent Rides ---
            var append_list_recent_rides = document.getElementById('append_list_recent_rides');
            append_list_recent_rides.innerHTML = '';

            database.collection('orders').where('ownerId','==',ownerId)
            .orderBy('createdDate', 'desc')
            .limit(limit)
            .get()
            .then((snapshots) => {
                let html = buildRidesHTML(snapshots);
                if (html != '') {
                    append_list_recent_rides.innerHTML = html;
                }
            });

            var append_listtop_drivers = document.getElementById('append_list_top_drivers');
            append_listtop_drivers.innerHTML = '';
            let driversQuery = database.collection('driver_users').where('ownerId','==',ownerId);

            driversQuery.get().then((snapshots) => {
                let drivers = snapshots.docs.map(doc => {
                    let d = doc.data();
                    d.id = doc.id;
                    return d;
                });
                drivers.sort((a, b) => (b.reviewsCount || 0) - (a.reviewsCount || 0));
                drivers = drivers.slice(0, limit);
                if (drivers.length === 0) {
                    append_listtop_drivers.innerHTML = `
                        <tr>
                            <td colspan="3" class="text-center text-muted p-3">
                               {{trans('lang.no_record_found')}}
                            </td>
                        </tr>`;
                } else {
                    append_listtop_drivers.innerHTML = buildDriverHTML({ docs: drivers });
                }
            });
        }

        $('#applyFilterBtn').on('click', function (e) {
            e.preventDefault();
            const view = $('#viewFilter').val();
            const year = parseInt($('#yearFilter').val());
            const month = parseInt($('#monthFilter').val());
            const startDate = $('#startDate').val();
            const endDate = $('#endDate').val();

            if (view === 'year') {
                loadDashboardData('year', year);
                getTotalEarnings('year', year);
                getTotalEarningsIntercity('year', year);
                loadRecentRidesAndTopDrivers('year', year);
            } else if (view === 'month') {
                loadDashboardData('month', year, month);
                getTotalEarnings('month', year, month);
                getTotalEarningsIntercity('month', year, month);
                loadRecentRidesAndTopDrivers('month', year, month);

            } else if (view === 'custom') {
                loadDashboardData('custom', null, null, startDate, endDate);
                getTotalEarnings('custom', null, null, startDate, endDate);
                getTotalEarningsIntercity('custom', null, null, startDate, endDate);
                loadRecentRidesAndTopDrivers('custom', null, null, startDate, endDate);

            }else{
                loadDashboardData(null);
                getTotalEarnings(null);
                getTotalEarningsIntercity(null);
                loadRecentRidesAndTopDrivers(null);
            }
        });

        function buildRidesHTML(snapshots) {
            var html = '';
            snapshots.docs.forEach((listval) => {
                val = listval.data();
                val.id = listval.id;
                var ride_id = val.id.substring(0, 7);

                var ride_route = '<?php echo route('rides.show', ':id'); ?>';
                ride_route = ride_route.replace(':id', val.id);

                html = html + '<tr>';

                html = html + '<td><a href="' + ride_route + '">' + ride_id + '</a></td>';
                if (val.userId != null) {
                    getUserName(val.userId, ride_id);
                }
                html = html + '<td class="user_name_' + ride_id + '"></td>';

                if (val.driverId != null) {
                    getDriverName(val.driverId, ride_id);
                }
                html = html + '<td class="driver_name_' + ride_id + '"></td>';

                html = html + '<td>' + val.sourceLocationName + '</td>';
                html = html + '<td>' + val.destinationLocationName + '</td>';
                html = html + '</tr>';
            });
            return html;
        }

        function buildDriverHTML(snapshots) {
            var html = '';
            snapshots.docs.forEach((val, index) => {
                var driverroute = '<?php echo route('drivers.view', ':id'); ?>'.replace(':id', val.id);
                var placeholderImage = "{{ asset('/images/default_user.png') }}";
                var rating = 0;

                if (val.reviewsCount && val.reviewsSum) {
                    rating = (parseFloat(val.reviewsSum) / parseFloat(val.reviewsCount));
                    rating = (rating * 10) / 10;
                }

                html += '<tr>';
                let profileSrc = val.profilePic ? val.profilePic : placeholderImage;

                html += '<td class="redirecttopage text-center"><div class="top-driver-name">' +
                    '<img class="img-circle img-size-32" style="width:30px;height:30px; margin-right:5px;" src="' + profileSrc + '" alt="image">' +
                    '<a href="' + driverroute + '">' + val.fullName + '</a>' +
                    '</div></td>';

                html += '<td class="redirecttopage"><div class="reviews-members-header"><div class="star-rating"><div class="d-inline-block" style="font-size: 14px;"> <ul class="rating" data-rating="' +
                    parseInt(Math.round(rating)) +
                    '"><li class="rating__item"></li><li class="rating__item"></li><li class="rating__item"></li><li class="rating__item"></li><li class="rating__item"></li></ul></div></div></div></td>';

                html += '<td class="redirecttopage"><a href="' + driverroute + '" ><span class="mdi mdi-lead-pencil"></span></a></td>';
                html += '</tr>';
            });
            return html;
        }


        async function getUserName(userId, id) {
            await database.collection('users').doc(userId).get().then(async function (snapshots) {
                var user = snapshots.data();
                if (user && user.fullName) {                 
                    $('.user_name_' + id).html('<a href="javascript:void(0)">' + user.fullName + '</a>');
                }
            });
        }

        async function getDriverName(driverId, id) {
            await database.collection('driver_users').doc(driverId).get().then(async function (snapshots) {
                if(snapshots.exists){
                    var driver = snapshots.data();
                    var driver_view = '{{ route('drivers.view', ':id') }}';
                    driver_view = driver_view.replace(':id', driverId);
                    $('.driver_name_' + id).html('<a href="' + driver_view + '">' + driver.fullName + '</a>');
                }
            });
        }

        function loadDashboardData(filterType, year = null, month = null, startDate = null, endDate = null) {
            let now = new Date();

            // Default ranges
            let startOfThisPeriod = new Date();
            let endOfThisPeriod = new Date();
            let startOfLastPeriod = null;
            let endOfLastPeriod = null;

            if (filterType === 'year' && year) {
                startOfThisPeriod = new Date(year, 0, 1);
                endOfThisPeriod = new Date(year, 11, 31, 23, 59, 59);
                startOfLastPeriod = new Date(year - 1, 0, 1);
                endOfLastPeriod = new Date(year - 1, 11, 31, 23, 59, 59);
            } else if (filterType === 'month' && year && month) {
                startOfThisPeriod = new Date(year, month - 1, 1);
                endOfThisPeriod = new Date(year, month, 0, 23, 59, 59);
                startOfLastPeriod = new Date(year, month - 2, 1);
                endOfLastPeriod = new Date(year, month - 1, 0, 23, 59, 59);
            } else if (filterType === 'custom' && startDate && endDate) {
                startOfThisPeriod = new Date(startDate);
                endOfThisPeriod = new Date(endDate);
                startOfLastPeriod = null; // optional: could calculate previous equivalent period
                endOfLastPeriod = null;
            }

            // Convert to Firestore Timestamps
            const startThisTS = firebase.firestore.Timestamp.fromDate(startOfThisPeriod);
            const endThisTS = firebase.firestore.Timestamp.fromDate(endOfThisPeriod);
            const startLastTS = startOfLastPeriod ? firebase.firestore.Timestamp.fromDate(startOfLastPeriod) : null;
            const endLastTS = endOfLastPeriod ? firebase.firestore.Timestamp.fromDate(endOfLastPeriod) : null;

            Promise.all([
                // All-time totals
                database.collection('orders').where('ownerId','==',ownerId).get(),
                database.collection('orders_intercity').where('ownerId','==',ownerId).get(),
                database.collection('driver_users').where('ownerId','==',ownerId).get(),

                // Current period
                database.collection('orders').where('createdDate', '>=', startThisTS).where('createdDate', '<=', endThisTS).where('ownerId','==',ownerId).get(),
                database.collection('orders_intercity').where('createdDate', '>=', startThisTS).where('createdDate', '<=', endThisTS).where('ownerId','==',ownerId).get(),
                database.collection('driver_users').where('createdAt', '>=', startThisTS).where('createdAt', '<=', endThisTS).where('ownerId','==',ownerId).get(),

              
                startLastTS ? database.collection('orders').where('createdDate', '>=', startLastTS).where('createdDate', '<=', endLastTS).where('ownerId','==',ownerId).get() : Promise.resolve({ docs: [] }),
                startLastTS ? database.collection('orders_intercity').where('createdDate', '>=', startLastTS).where('createdDate', '<=', endLastTS).where('ownerId','==',ownerId).get() : Promise.resolve({ docs: [] }),
             
                startLastTS ? database.collection('driver_users').where('createdAt', '>=', startLastTS).where('createdAt', '<=', endLastTS).where('ownerId','==',ownerId).get() : Promise.resolve({ docs: [] })
            ]).then(([allOrders, allIntercity,  allDrivers, ordersCurr, intercityCurr, driversCurr, ordersLast, intercityLast, driversLast]) => {

                // Totals
                let totalRides = allOrders.docs.length + allIntercity.docs.length;
                let totalDrivers = allDrivers.docs.length;

                let totalRidesCurr = filterType ? (ordersCurr.docs.length + intercityCurr.docs.length) : totalRides;
                let totalRidesLast = ordersLast.docs.length + intercityLast.docs.length;
                
                let totalDriversCurr = filterType ? driversCurr.docs.length : totalDrivers;
                let totalDriversLast = driversLast.docs.length;

                // Percent change
                let ridesPercent = totalRidesLast === 0
                    ? (totalRidesCurr === 0 ? 0 : 100)
                    : ((totalRidesCurr - totalRidesLast) / totalRidesLast) * 100;

                let driversPercent = totalDriversLast === 0
                    ? (totalDriversCurr === 0 ? 0 : 100)
                    : ((totalDriversCurr - totalDriversLast) / totalDriversLast) * 100;

                let ridesInfo = getArrowAndClass(ridesPercent);
                let driversInfo = getArrowAndClass(driversPercent);

                // Update DOM
                jQuery("#total_rides").text(totalRidesCurr);
                jQuery("#driver_count").text(totalDriversCurr);

                if(filterType !== null){
                    jQuery("#rides_percent").html(`<i class="fa ${ridesInfo.arrow}"></i> ${Math.abs(ridesPercent).toFixed(2)}% {{trans('lang.vs_last_month')}}`).removeClass('green red').addClass(ridesInfo.className);
                    jQuery("#driver_percent").html(`<i class="fa ${driversInfo.arrow}"></i> ${Math.abs(driversPercent).toFixed(2)}% {{trans('lang.vs_last_month')}}`).removeClass('green red').addClass(driversInfo.className);
                }else{
                    jQuery("#rides_percent").hide();
                    jQuery("#driver_percent").hide();
                }
                
            }).catch(err => console.error(err));
        }

        async function getTotalEarnings(filterType = null, year = null, month = null, startDate = null, endDate = null) {
            
            let v01 = 0, v02 = 0, v03 = 0, v04 = 0, v05 = 0, v06 = 0, v07 = 0, v08 = 0, v09 = 0, v10 = 0, v11 = 0, v12 = 0;

            let earningsQuery = database.collection('orders').where('ownerId','==',ownerId).where('status', 'in', ["Ride Completed"]);

            if (filterType === 'year' && year) {
                let start = firebase.firestore.Timestamp.fromDate(new Date(year, 0, 1));
                let end = firebase.firestore.Timestamp.fromDate(new Date(year, 11, 31, 23, 59, 59));
                earningsQuery = earningsQuery.where('createdDate', '>=', start).where('createdDate', '<=', end);
            } else if (filterType === 'month' && year && month) {
                let start = firebase.firestore.Timestamp.fromDate(new Date(year, month - 1, 1));
                let end = firebase.firestore.Timestamp.fromDate(new Date(year, month, 0, 23, 59, 59));
                earningsQuery = earningsQuery.where('createdDate', '>=', start).where('createdDate', '<=', end);
            } else if (filterType === 'custom' && startDate && endDate) {
                let start = firebase.firestore.Timestamp.fromDate(new Date(startDate));
                let end = firebase.firestore.Timestamp.fromDate(new Date(endDate + ' 23:59:59'));
                earningsQuery = earningsQuery.where('createdDate', '>=', start).where('createdDate', '<=', end);
            }

            let orderSnapshots = await earningsQuery.get();
            var paymentData = orderSnapshots.docs;
            var totalEarning = 0;
            var adminCommission = 0;
            var discount = 0;

            paymentData.forEach((order) => {

                var orderData = order.data();
                var price = 0;

                if (orderData.finalRate != null && orderData.finalRate != '' && orderData
                    .finalRate != undefined) {
                    price = orderData.finalRate;
                } else {
                    price = orderData.offerRate;
                }

                if (orderData.coupon != undefined && orderData.coupon.amount != null) {
                    var data = orderData.coupon;
                    if (data.type == "fix") {
                        discount_amount = data.amount;
                    } else {
                        discount_amount = (parseFloat(data.amount) * parseFloat(price)) / 100;
                    }

                    price = parseFloat(price) - parseFloat(discount_amount);

                }


                var adminCommissionPrice = price;
                tax = 0;
                if (orderData.taxList != undefined && $.isArray(orderData.taxList)) {
                    for (let i = 0; i < orderData.taxList.length; i++) {
                        let taxData = orderData.taxList[i];
                        if (taxData.type == "percentage") {
                            tax = tax + (parseFloat(taxData.tax) * parseFloat(price)) / 100;
                        } else {
                            tax = tax + parseFloat(taxData.tax);
                        }
                    }
                }

                if (!isNaN(tax)) {
                    price = parseFloat(price) + parseFloat(tax);
                }

                if (orderData.adminCommission != undefined && orderData.adminCommission.type !=
                    undefined && orderData.adminCommission.amount > 0 && price > 0) {
                    var commission = 0;
                    if (orderData.adminCommission.type == "percentage") {
                        commission = (adminCommissionPrice * parseFloat(orderData.adminCommission.amount)) /
                            100;
                    } else {
                        commission = parseFloat(orderData.adminCommission.amount);
                    }
                    adminCommission = commission + adminCommission;
                }

                if (orderData.hasOwnProperty('totalHoldingCharges') && orderData.totalHoldingCharges != "" && orderData.totalHoldingCharges != null) {

                    var holdingCharges = parseFloat(orderData.totalHoldingCharges);

                    price = price + holdingCharges;

                }

                totalEarning = parseFloat(totalEarning) + parseFloat(price);

                if (orderData.createdDate) {
                    var orderMonth = orderData.createdDate.toDate().getMonth() + 1;
                    switch (parseInt(orderMonth)) {
                        case 1:
                            v01 = parseFloat(v01) + parseFloat(price);
                            break;
                        case 2:
                            v02 = parseFloat(v02) + parseFloat(price);
                            break;
                        case 3:
                            v03 = parseFloat(v03) + parseFloat(price);
                            break;
                        case 4:
                            v04 = parseFloat(v04) + parseFloat(price);
                            break;
                        case 5:
                            v05 = parseFloat(v05) + parseFloat(price);
                            break;
                        case 6:
                            v06 = parseFloat(v06) + parseFloat(price);
                            break;
                        case 7:
                            v07 = parseFloat(v07) + parseFloat(price);
                            break;
                        case 8:
                            v08 = parseFloat(v08) + parseFloat(price);
                            break;
                        case 9:
                            v09 = parseFloat(v09) + parseFloat(price);
                            break;
                        case 10:
                            v10 = parseFloat(v10) + parseFloat(price);
                            break;
                        case 11:
                            v11 = parseFloat(v11) + parseFloat(price);
                            break;
                        default:
                            v12 = parseFloat(v12) + parseFloat(price);
                            break;
                    }
                }
            });

            // Format currency
            if (currencyAtRight) {
                totalEarning = parseFloat(totalEarning).toFixed(decimal_degits) + currentCurrency;
                adminCommission = parseFloat(adminCommission).toFixed(decimal_degits) + currentCurrency;
            } else {
                totalEarning = currentCurrency + parseFloat(totalEarning).toFixed(decimal_degits);
                adminCommission = currentCurrency + parseFloat(adminCommission).toFixed(decimal_degits);
            }

            $("#earnings_count").empty().append(totalEarning).val(totalEarning);
            $("#admincommission_count").empty().append(adminCommission).val(adminCommission);

            let rides_data = [v01, v02, v03, v04, v05, v06, v07, v08, v09, v10, v11, v12];
            await getTotalEarningsIntercity(filterType, year, month, startDate, endDate, rides_data);
            jQuery("#overlay").hide();
        }


        async function getTotalEarningsIntercity(filterType = 'year', year = null, month = null, startDate = null, endDate = null, rides_data) {
            var v01 = 0, v02 = 0, v03 = 0, v04 = 0, v05 = 0, v06 = 0, v07 = 0, v08 = 0, v09 = 0, v10 = 0, v11 = 0, v12 = 0;
            
            // Build Firestore query
            let intercityQuery = database.collection('orders_intercity').where('ownerId','==',ownerId).where('status', '==', 'Ride Completed');

            // Apply filters
            if (filterType === 'year' && year) {
                let start = firebase.firestore.Timestamp.fromDate(new Date(year, 0, 1));
                let end = firebase.firestore.Timestamp.fromDate(new Date(year, 11, 31, 23, 59, 59));
                intercityQuery = intercityQuery.where('createdDate', '>=', start).where('createdDate', '<=', end);
            } else if (filterType === 'month' && year && month) {
                let start = firebase.firestore.Timestamp.fromDate(new Date(year, month - 1, 1));
                let end = firebase.firestore.Timestamp.fromDate(new Date(year, month, 0, 23, 59, 59));
                intercityQuery = intercityQuery.where('createdDate', '>=', start).where('createdDate', '<=', end);
            } else if (filterType === 'custom' && startDate && endDate) {
                let start = firebase.firestore.Timestamp.fromDate(new Date(startDate));
                let end = firebase.firestore.Timestamp.fromDate(new Date(endDate + ' 23:59:59'));
                intercityQuery = intercityQuery.where('createdDate', '>=', start).where('createdDate', '<=', end);
            }

            let orderSnapshots = await intercityQuery.get();

            var paymentData = orderSnapshots.docs;
            var totalEarning = 0;
            var adminCommission = 0;
            var discount = 0;

            paymentData.forEach((order) => {

                var orderData = order.data();
                var price = 0;

                if (orderData.finalRate != null && orderData.finalRate != '' && orderData
                    .finalRate != undefined) {
                    price = orderData.finalRate;
                } else {
                    price = orderData.offerRate;
                }

                if (orderData.coupon != undefined && orderData.coupon.amount != null) {
                    var data = orderData.coupon;
                    if (data.type == "fix") {
                        discount_amount = data.amount;
                    } else {
                        discount_amount = (parseFloat(data.amount) * parseFloat(price)) / 100;
                    }

                    price = parseFloat(price) - parseFloat(discount_amount);

                }


                var adminCommissionPrice = price;

                tax = 0;
                if (orderData.taxList != undefined && $.isArray(orderData.taxList)) {
                    for (let i = 0; i < orderData.taxList.length; i++) {
                        let taxData = orderData.taxList[i];
                        if (taxData.type == "percentage") {
                            tax = parseFloat(tax) + (parseFloat(taxData.tax) * parseFloat(price)) / 100;
                        } else {
                            tax = parseFloat(tax) + parseFloat(taxData.tax);
                        }
                    }
                }
                if (!isNaN(tax)) {
                    price = parseFloat(price) + parseFloat(tax);
                }


                if (orderData.adminCommission != undefined && orderData.adminCommission.type !=
                    undefined && orderData.adminCommission.amount > 0 && price > 0) {
                    var commission = 0;
                    if (orderData.adminCommission.type == "percentage") {
                        commission = (adminCommissionPrice * parseFloat(orderData.adminCommission.amount)) /
                            100;
                    } else {
                        commission = parseFloat(orderData.adminCommission.amount);
                    }
                    adminCommission = commission + adminCommission;
                }

                totalEarning = parseFloat(totalEarning) + parseFloat(price);

                if (orderData.createdDate) {
                    var orderMonth = orderData.createdDate.toDate().getMonth() + 1;
                    
                    switch (parseInt(orderMonth)) {
                        case 1:
                            v01 = parseFloat(v01) + parseFloat(price);
                            break;
                        case 2:
                            v02 = parseFloat(v02) + parseFloat(price);
                            break;
                        case 3:
                            v03 = parseFloat(v03) + parseFloat(price);
                            break;
                        case 4:
                            v04 = parseFloat(v04) + parseFloat(price);
                            break;
                        case 5:
                            v05 = parseFloat(v05) + parseFloat(price);
                            break;
                        case 6:
                            v06 = parseFloat(v06) + parseFloat(price);
                            break;
                        case 7:
                            v07 = parseFloat(v07) + parseFloat(price);
                            break;
                        case 8:
                            v08 = parseFloat(v08) + parseFloat(price);
                            break;
                        case 9:
                            v09 = parseFloat(v09) + parseFloat(price);
                            break;
                        case 10:
                            v10 = parseFloat(v10) + parseFloat(price);
                            break;
                        case 11:
                            v11 = parseFloat(v11) + parseFloat(price);
                            break;
                        default:
                            v12 = parseFloat(v12) + parseFloat(price);
                            break;
                    }
                }

            })
            // Format currency
            if (currencyAtRight) {
                totalEarning = parseFloat(totalEarning).toFixed(decimal_degits) + currentCurrency;
                adminCommission = parseFloat(adminCommission).toFixed(decimal_degits) + currentCurrency;
            } else {
                totalEarning = currentCurrency + parseFloat(totalEarning).toFixed(decimal_degits);
                adminCommission = currentCurrency + parseFloat(adminCommission).toFixed(decimal_degits);
            }

            $("#earnings_count_intercity").val(totalEarning);
            $("#admincommission_count_intercity").val(adminCommission);
            $("#earnings_count_intercity").text("{{ trans('lang.intercity') }} / " + totalEarning);
            $("#admincommission_count_intercity").text("{{ trans('lang.intercity') }} / " + adminCommission);
            intercity_data = [v01, v02, v03, v04, v05, v06, v07, v08, v09, v10, v11, v12];

            var labels = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];
            var $salesChart = $('#sales-chart');

            salesChart($salesChart, rides_data, intercity_data, labels);
            serviceOverview();
            salesOverview();
            totalearningChart();
            jQuery("#overlay").hide();
        }



        function salesChart(chartNode, rides_data, intercity_data, labels) {
            var ticksStyle = {
                fontColor: '#666',
                fontStyle: 'bold'
            };

            var mode = 'index';
            var intersect = true;

            if (salesChartInstance) {
                salesChartInstance.destroy();
            }

            salesChartInstance = new Chart(chartNode, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: "{{ trans('lang.order_plural') }}",
                        backgroundColor: '#6366F1',
                        data: rides_data
                    },
                    {
                        label: "{{ trans('lang.intercity') }}",
                        backgroundColor: '#10B981',
                        data: intercity_data
                    }
                    ]
                },
                options: {
                    maintainAspectRatio: false,
                    tooltips: {
                        mode: mode,
                        intersect: intersect,
                        callbacks: {
                            label: function (tooltipItem, data) {
                                let value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
                                return currentCurrency + parseFloat(value).toFixed(decimal_degits);
                            }
                        },
                    },
                    hover: {
                        mode: mode,
                        intersect: intersect
                    },
                    legend: {
                        display: true
                    },
                    scales: {
                        yAxes: [{
                            // display: false,
                            gridLines: {
                                display: true,
                                lineWidth: '4px',
                                color: 'rgba(0, 0, 0, .2)',
                                zeroLineColor: 'transparent'
                            },
                            ticks: $.extend({
                                beginAtZero: true,
                                callback: function (value, index, values) {
                                    return currentCurrency + value.toFixed(decimal_degits);
                                }


                            }, ticksStyle)
                        }],
                        xAxes: [{
                            display: true,
                            gridLines: {
                                display: false
                            },
                            ticks: ticksStyle
                        }]
                    }
                }
            })

            return salesChartInstance;
        }

        function serviceOverview() {
            const data = {
                labels: [
                    "{{ trans('lang.dashboard_total_orders') }}",
                    "{{ trans('lang.dashboard_total_drivers') }}",
                    "{{ trans('lang.dashboard_ride_placed') }}",
                    "{{ trans('lang.dashboard_ride_active') }}",
                    "{{ trans('lang.dashboard_ride_completed') }}",
                    "{{ trans('lang.dashboard_ride_canceled') }}",
                ],
                datasets: [{
                    data: [
                        parseInt(jQuery("#total_rides").text()) || 0,
                        parseInt(jQuery("#driver_count").text()) || 0,
                        parseInt(jQuery("#placed_count").text()) || 0,
                        parseInt(jQuery("#active_count").text()) || 0,
                        parseInt(jQuery("#completed_count").text()) || 0,
                        parseInt(jQuery("#canceled_count").text()) || 0,
                    ],
                    backgroundColor: [
                        '#6366F1',
                        // '#F59E0B',
                        '#10B981',
                        '#EAB308',
                        '#3B82F6',
                        '#EF4444',
                        '#E5E7EB',
                    ],
                    hoverOffset: 4
                }]
            };

            // Destroy previous chart if it exists
            if (serviceOverviewChart) {
                serviceOverviewChart.destroy();
            }

            serviceOverviewChart = new Chart('service-overview', {
                type: 'doughnut',
                data: data,
                options: {
                    maintainAspectRatio: false,
                }
            });

            return serviceOverviewChart;
        }

        function salesOverview() {
            const data = {
                labels: [
                    "{{ trans('lang.dashboard_total_earnings') }}",
                    "{{ trans('lang.dashboard_admin_commission') }}",
                    "{{ trans('lang.dashboard_total_earnings_intercity') }}",
                    "{{ trans('lang.dashboard_admin_commission_intercity') }}",
                ],
                datasets: [{
                    data: [
                        jQuery("#earnings_count").text().replace(currentCurrency, ""),
                        jQuery("#admincommission_count").text().replace(currentCurrency, ""),
                        jQuery("#earnings_count_intercity").text().match(/\d+/)[0],
                        jQuery("#admincommission_count_intercity").text().match(/\d+/)[0],],
                    backgroundColor: [
                        '#6366F1',
                        '#F59E0B',
                        '#10B981',
                        '#EF4444',
                    ],
                    hoverOffset: 4
                }]
            };

            if (salesOverviewChart) {
                salesOverviewChart.destroy();
            }

            salesOverviewChart = new Chart('sales-overview', {
                type: 'doughnut',
                data: data,
                options: {
                    maintainAspectRatio: false,
                    tooltips: {
                        callbacks: {
                            label: function (tooltipItems, data) {
                                return data.labels[tooltipItems.index] + ': ' + currentCurrency + data.datasets[
                                    0].data[tooltipItems.index];
                            }
                        }
                    }
                }
            });

            return salesOverviewChart;
        }
        function totalearningChart() {
            var earnings = parseFloat(jQuery("#earnings_count").text().replace(/[^0-9.-]+/g, '')) || 0;
            var earningsIntercity = parseFloat(jQuery("#earnings_count_intercity").val().replace(/[^0-9.-]+/g, '')) || 0;
            var adminCommission = parseFloat(jQuery("#admincommission_count").val().replace(/[^0-9.-]+/g, '')) || 0;
            var adminCommissionIntercity = parseFloat(jQuery("#admincommission_count_intercity").val().replace(/[^0-9.-]+/g, '')) || 0;

            var totalEarnings = earnings + earningsIntercity;
            var totalAdminCommission = adminCommission + adminCommissionIntercity;

            $("#legend-total-earnings").text(currentCurrency + parseFloat(totalEarnings).toFixed(decimal_degits));
            $("#legend-admin-commission").text(currentCurrency + parseFloat(totalAdminCommission).toFixed(decimal_degits));

            var ctx = document.getElementById('total-earnings-chart').getContext('2d');

            if (totalEarningsChart) {
                totalEarningsChart.destroy();
            }

            totalEarningsChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ["{{trans('lang.dashboard_total_earnings')}}", "{{trans('lang.admin_commission')}}"],
                    datasets: [{
                        label: 'Earnings Overview',
                        data: [totalEarnings, totalAdminCommission],
                        backgroundColor: ['#6366F1', '#F59E0B'],
                        borderColor: ['#ffffff', '#ffffff'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    legend: {
                        position: 'bottom',
                    },
                    tooltips: {
                        callbacks: {
                            label: function (tooltipItem, data) {
                                var label = data.labels[tooltipItem.index] || '';
                                var value = data.datasets[0].data[tooltipItem.index] || 0;
                                return label + ': ' + currentCurrency + parseFloat(value).toFixed(decimal_degits);
                            }
                        }
                    }
                }
            });
        }

        function getArrowAndClass(percent) {
            return {
                arrow: percent > 0 ? 'fa-arrow-up' : 'fa-arrow-down',
                className: percent > 0 ? 'green' : 'red'
            };
        }

    </script>
@endsection