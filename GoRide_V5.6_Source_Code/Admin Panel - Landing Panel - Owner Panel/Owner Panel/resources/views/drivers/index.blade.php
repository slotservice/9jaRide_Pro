@extends('layouts.app')
@section('content')
    @php
        $type = 'all';
    @endphp
    <div class="page-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">                  
                    {{ trans('lang.all_drivers') }}                   
                </h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ trans('lang.dashboard') }}</a></li>
                    <li class="breadcrumb-item active">{{ trans('lang.driver_table') }}</li>
                </ol>
            </div>
            <div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="admin-top-section">
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex top-title-section pb-4 justify-content-between">
                            <div class="d-flex top-title-left align-self-center">
                                <span class="icon mr-3"><img src="{{ asset('images/driver.png') }}"></span>
                                <h3 class="mb-0">{{ trans('lang.driver_table') }}</h3>
                                <span class="counter ml-3 total_count"></span>
                            </div>
                            <div class="d-flex top-title-right align-self-center">
                                <div class="select-box pl-3 d-none">
                                    <select class="form-control status_selector filteredRecords">
                                        <option value="">{{ trans('lang.status') }}</option>
                                        <option value="active">{{ trans('lang.active') }}</option>
                                        <option value="inactive">{{ trans('lang.in_active') }}</option>
                                    </select>
                                </div>
                                <div class="select-box pl-3">
                                    <div id="daterange"><i class="fa fa-calendar"></i>&nbsp;
                                        <span></span>&nbsp; <i class="fa fa-caret-down"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-list">
                <div class="row">
                    <div class="col-12">
                        <div class="card border">
                            <div class="card-header d-flex justify-content-between align-items-center border-0">
                                <div class="card-header-title">
                                    <h3 class="text-dark-2 mb-2 h4">{{ trans('lang.driver_table') }}</h3>
                                    <p class="mb-0 text-dark-2">{{ trans('lang.driver_table_text') }}</p>
                                </div>

                                <div class="card-header-right d-flex align-items-center">
                                    <div class="card-header-btn mr-3">
                                        <a class="btn-primary btn rounded-full create-driver-btn"  href="javascript:void(0)"><i class="mdi mdi-plus mr-2"></i>{{trans('lang.create_driver')}}</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive m-t-10">
                                    <table id="driverTable" class="display nowrap table table-hover table-striped table-bordered table table-striped" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>                                                
                                                <th class="delete-all"><input type="checkbox" id="is_active"><label class="col-3 control-label" for="is_active"><a id="deleteAll" class="do_not_delete" href="javascript:void(0)"><i class="fa fa-trash"></i> {{ trans('lang.all') }}</a></label>
                                                </th>                                                
                                                <th>{{ trans('lang.user_info') }}</th>
                                                <th>{{ trans('lang.email') }}</th>
                                                <th>{{ trans('lang.phone') }}</th>                                               
                                                <th>{{ trans('lang.date') }}</th>                                             
                                                <th>{{ trans('lang.service') }}</th>
                                                <th>{{ trans('lang.dashboard_total_orders') }}</th>
                                                <th>{{ trans('lang.actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody id="append_list1">
                                        </tbody>
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
@endsection
@section('scripts')
    <script type="text/javascript">
        var database = firebase.firestore();
        var ownerId = "{{ $id }}";
        const urlParams = new URLSearchParams(window.location.search);
        var type = "{{ $type }}";
        if (urlParams.has('today')) {
            const today = new Date();
            const startOfDay = new Date(today.setHours(0, 0, 0, 0));
            const endOfDay = new Date(today.setHours(23, 59, 59, 999));
            var ref = database.collection('driver_users').where('ownerId','==', ownerId).where("createdAt", ">=", startOfDay).where("createdAt", "<=", endOfDay);
        } else {
            var ref = database.collection('driver_users').where('ownerId','==', ownerId);
        }
      
        var placeholderImage = "{{ asset('/images/default_user.png') }}";
        var deleteMsg = "{{ trans('lang.delete_alert') }}";
        var deleteSelectedRecordMsg = "{{ trans('lang.selected_delete_alert') }}";
        var setLanguageCode = getCookie('setLanguage');
        var defaultLanguageCode = getCookie('defaultLanguage');
      
        $('.status_selector').select2({
            placeholder: '{{ trans('lang.status') }}',
            minimumResultsForSearch: Infinity,
            allowClear: true
        });
        $('select').on("select2:unselecting", function(e) {
            var self = $(this);
            setTimeout(function() {
                self.select2('close');
            }, 0);
        });

        function setDate() {
            $('#daterange span').html('{{ trans('lang.select_range') }}');
            $('#daterange').daterangepicker({
                autoUpdateInput: false,
            }, function(start, end) {
                $('#daterange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                $('.filteredRecords').trigger('change');
            });
            $('#daterange').on('apply.daterangepicker', function(ev, picker) {
                $('#daterange span').html(picker.startDate.format('MMMM D, YYYY') + ' - ' + picker.endDate.format('MMMM D, YYYY'));
                $('.filteredRecords').trigger('change');
            });
            $('#daterange').on('cancel.daterangepicker', function(ev, picker) {
                $('#daterange span').html('{{ trans('lang.select_range') }}');
                $('.filteredRecords').trigger('change');
            });
        }
        setDate();
        var initialRef = ref;
        $('.filteredRecords').change(async function() {
            var daterangepicker = $('#daterange').data('daterangepicker');
            filterRef = initialRef;
            if ($('#daterange span').html() != '{{ trans('lang.select_range') }}' && daterangepicker) {
                var from = moment(daterangepicker.startDate).toDate();
                var to = moment(daterangepicker.endDate).toDate();
                if (from && to) {
                    var fromDate = firebase.firestore.Timestamp.fromDate(new Date(from));
                    filterRef = filterRef.where('createdAt', '>=', fromDate);
                    var toDate = firebase.firestore.Timestamp.fromDate(new Date(to));
                    filterRef = filterRef.where('createdAt', '<=', toDate);
                }
            }
            ref = filterRef;
            $('#driverTable').DataTable().ajax.reload();
        });
        var append_list = '';
        $(document).ready(function() {
            jQuery("#overlay").show();
            $(document).on('click', '.dt-button-collection .dt-button', function() {
                $('.dt-button-collection').hide();
                $('.dt-button-background').hide();
            });
            $(document).on('click', function(event) {
                if (!$(event.target).closest('.dt-button-collection, .dt-buttons').length) {
                    $('.dt-button-collection').hide();
                    $('.dt-button-background').hide();
                }
            });
            var fieldConfig = {
                columns: [{
                        key: 'fullName',
                        header: "{{ trans('lang.user_info') }}"
                    },
                    {
                        key: 'email',
                        header: "{{ trans('lang.email') }}"
                    },
                    {
                        key: 'phone',
                        header: "{{ trans('lang.phone') }}"
                    },
                    {
                        key: 'serviceName',
                        header: "{{ trans('lang.service') }}"
                    },
                    {
                        key: 'vehicleType',
                        header: "{{ trans('lang.vehicle_type') }}"
                    },
                    {
                        key: 'createdAt',
                        header: "{{ trans('lang.date') }}"
                    },
                ],
                fileName: "{{ trans('lang.driver_table') }}",
            };
            const table = $('#driverTable').DataTable({
                pageLength: 10, // Number of rows per page
                processing: false, // Show processing indicator
                serverSide: true, // Enable server-side processing
                responsive: true,
                ajax: async function(data, callback, settings) {
                    const start = data.start;
                    const length = data.length;
                    const searchValue = data.search.value.toLowerCase();
                    const orderColumnIndex = data.order[0].column;
                    const orderDirection = data.order[0].dir;
                    const orderableColumns = ['', 'fullName', 'email', 'phone','createdAt', 'serviceName', 'totalRide', ''] ; // Ensure this matches the actual column names
                    const orderByField = orderableColumns[orderColumnIndex]; // Adjust the index to match your table
                    if (searchValue.length >= 3 || searchValue.length === 0) {
                        $('#overlay').show();
                    }
                    ref.orderBy('createdAt', 'desc').get().then(async function(querySnapshot) {
                        if (querySnapshot.empty) {
                            $('.total_count').text(0);
                            $('#overlay').hide(); // Hide loader
                            callback({
                                draw: data.draw,
                                recordsTotal: 0,
                                recordsFiltered: 0,
                                filteredData: [],
                                data: [] // No data
                            });
                            return;
                        }
                        let records = [];
                        let filteredRecords = [];
                        let serviceNames = {};
                        // Fetch driver names
                        const servicDocs = await database.collection('service').get();
                        servicDocs.forEach(doc => {
                            serviceNames[doc.id] = doc.data().title;
                        });
                        await Promise.all(querySnapshot.docs.map(async (doc) => {
                            childData = doc.data();
                            childData.id = doc.id; // Ensure the document ID is included in the data              
                            var serviceName = serviceNames[childData.serviceId] || '';
                            var title = '';
                            if (Array.isArray(serviceName)) {
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
                            childData.serviceName = title;
                           
                            childData.phone = childData.countryCode && childData.phoneNumber ? shortNumber(childData.countryCode, childData.phoneNumber) : "";
                           
                            if (searchValue) {
                                var date = '';
                                var time = '';
                                if (childData.hasOwnProperty("createdAt")) {
                                    try {
                                        date = childData.createdAt.toDate().toDateString();
                                        time = childData.createdAt.toDate().toLocaleTimeString('en-US');
                                    } catch (err) {}
                                }
                                var createdAt = date + ' ' + time;
                                childData.createDate = createdAt;
                                if (
                                    (childData.fullName && childData.fullName.toLowerCase().toString().includes(searchValue)) ||
                                    (childData.serviceName && childData.serviceName.toLowerCase().toString().includes(searchValue)) ||
                                    (createdAt && createdAt.toString().toLowerCase().indexOf(searchValue) > -1) ||
                                    (childData.phone && childData.phone.toString().includes(searchValue)) ||
                                    (childData.email && childData.email.toString().toLowerCase().includes(searchValue))  
                                ) {
                                    filteredRecords.push(childData);
                                }
                            } else {
                                filteredRecords.push(childData);
                            }
                        }));
                        filteredRecords.sort((a, b) => {
                            let aValue = a[orderByField] ? a[orderByField].toString().toLowerCase() : '';
                            let bValue = b[orderByField] ? b[orderByField].toString().toLowerCase() : '';
                            if (orderByField === 'createdAt') {
                                aValue = a[orderByField] ? new Date(a[orderByField].toDate()).getTime() : 0;
                                bValue = b[orderByField] ? new Date(b[orderByField].toDate()).getTime() : 0;
                            }
                            if (orderByField === 'subscriptionExpiryDate') {
                                aValue = a[orderByField] ? new Date(a[orderByField].toDate()).getTime() : 0;
                                bValue = b[orderByField] ? new Date(b[orderByField].toDate()).getTime() : 0;
                            }
                            if (orderDirection === 'asc') {
                                return (aValue > bValue) ? 1 : -1;
                            } else {
                                return (aValue < bValue) ? 1 : -1;
                            }
                        });
                        const totalRecords = filteredRecords.length;
                        $('.total_count').text(totalRecords);
                        const paginatedRecords = filteredRecords.slice(start, start + length);
                        await Promise.all(paginatedRecords.map(async (childData) => {
                            if (childData.id) {
                                const totalOrderSnapShot = await database.collection('orders').where('driverId', '==', childData.id).get();
                                const rides = totalOrderSnapShot.size;
                                const totalIntercityOrderSnapShot = await database.collection('orders_intercity').where('driverId', '==', childData.id).get();
                                const intercity = totalIntercityOrderSnapShot.size;
                                childData.total_rides = rides + intercity;
                            } else {
                                childData.total_rides = 0;
                            }
                            var getData = await buildHTML(childData);
                            records.push(getData);
                        }));
                        $('#overlay').hide(); // Hide loader
                        callback({
                            draw: data.draw,
                            recordsTotal: totalRecords,
                            recordsFiltered: totalRecords,
                            filteredData: filteredRecords,
                            data: records
                        });
                    }).catch(function(error) {
                        console.error("Error fetching data from Firestore:", error);
                        $('#overlay').hide(); // Hide loader
                        callback({
                            draw: data.draw,
                            recordsTotal: 0,
                            recordsFiltered: 0,
                            filteredData: [],
                            data: []
                        });
                    });
                },
                order:  [
                    [5, 'desc']
                ] ,
                columnDefs: [{
                        targets: 4 ,
                        type: 'date',
                        render: function(data) {
                            return data;
                        }
                    },
                    {
                        orderable: false,
                        targets:  [0, 7]
                    },
                ],
                "language": datatableLang,
                dom: 'lfrtipB',
                buttons: [{
                    extend: 'collection',
                    text: '<i class="mdi mdi-cloud-download"></i> {{trans("lang.export_as")}}',
                    className: 'btn btn-info',
                    buttons: [{
                            extend: 'excelHtml5',
                            text: "{{trans('lang.export_excel')}}",
                            action: function(e, dt, button, config) {
                                exportData(dt, 'excel', fieldConfig);
                            }
                        },
                        {
                            extend: 'pdfHtml5',
                            text: "{{trans('lang.export_pdf')}}",
                            action: function(e, dt, button, config) {
                                exportData(dt, 'pdf', fieldConfig);
                            }
                        },
                        {
                            extend: 'csvHtml5',
                            text: "{{trans('lang.export_csv')}}",
                            action: function(e, dt, button, config) {
                                exportData(dt, 'csv', fieldConfig);
                            }
                        }
                    ]
                }],
                initComplete: function() {
                    $(".dataTables_filter").append($(".dt-buttons").detach());
                    $('.dataTables_filter input').attr('placeholder', "{{trans('lang.search_here')}}").attr('autocomplete', 'new-password').val('');
                    $('.dataTables_filter label').contents().filter(function() {
                        return this.nodeType === 3;
                    }).remove();
                }
            });
            table.columns.adjust().draw();

            function debounce(func, wait) {
                let timeout;
                const context = this;
                return function(...args) {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(context, args), wait);
                };
            }
            $('#search-input').on('input', debounce(function() {
                const searchValue = $(this).val();
                if (searchValue.length >= 3) {
                    $('#overlay').show();
                    table.search(searchValue).draw();
                } else if (searchValue.length === 0) {
                    $('#overlay').show();
                    table.search('').draw();
                }
            }, 300));
        });
        async function buildHTML(val) {
            var html = [];
            newdate = '';
            var id = val.id;
            var route1 = '{{ route('drivers.edit', ':id') }}';
            route1 = route1.replace(':id', id);
            var driverView = '{{ route('drivers.view', ':id') }}';
            driverView = driverView.replace(':id', id);
            html.push('<input type="checkbox" id="is_open_' + id + '" class="is_open" dataId="' + id + '"><label class="col-3 control-label"\n' + 'for="is_open_' + id + '" ></label>');
            var rating = 0;

            if (
                val.hasOwnProperty('reviewsCount') && val.reviewsCount && val.reviewsCount != "0.0" && val.reviewsCount != null &&
                val.hasOwnProperty('reviewsSum') && val.reviewsSum && val.reviewsSum != "0.0" && val.reviewsSum != null
            ) {
                rating = (parseFloat(val.reviewsSum) / parseFloat(val.reviewsCount));
                rating = (rating * 10) / 10;
            }

            var userImg = (val.profilePic == '' || val.profilePic == null)
                ? '<img width="100%" style="width:70px;height:70px;" src="' + placeholderImage + '" alt="image">'
                : '<img width="100%" style="width:70px;height:70px;" src="' + val.profilePic + '" alt="image">';

            html.push(
                userImg +
                '<a href="' + driverView + '">' + val.fullName + '</a>' +
                '<div class="reviews-members-header">' +
                    '<div class="star-rating">' +
                        '<div class="d-inline-block" style="font-size: 14px;">' +
                            '<ul class="rating" data-rating="' + parseInt(Math.round(rating)) + '">' +
                                '<li class="rating__item"></li>' +
                                '<li class="rating__item"></li>' +
                                '<li class="rating__item"></li>' +
                                '<li class="rating__item"></li>' +
                                '<li class="rating__item"></li>' +
                            '</ul>' +
                        '</div>' +
                    '</div>' +
                '</div>'
            );

            html.push(shortEmail(val.email));
            if (val.countryCode != null && val.countryCode.includes('+')) {
                val.countryCode = val.countryCode.slice(1);
            } else {
                val.countryCode = val.countryCode;
            }
            html.push(val.phone);
           
            if (val.hasOwnProperty("createdAt") && val.createdAt != null && val.createdAt != '') {
                var date = val.createdAt.toDate().toDateString();
                var time = val.createdAt.toDate().toLocaleTimeString('en-US');
                html.push('<span class="dt-time">' + date + ' ' + time + '</span>');
            } else {
                html.push('');
            }
           
            html.push(val.serviceName);
            var trroute1 = '';
            trroute1 = trroute1.replace(':id', 'driverId=' + id);
           
            html.push(val.total_rides);

            var actionHtml = '';

            actionHtml = actionHtml + '<span class="action-btn"><a href="' + driverView + '"><i class="mdi mdi-eye"></i></a><a href="' + route1 + '"><i class="mdi mdi-lead-pencil"></i></a>';

          
            actionHtml = actionHtml + '<a id="' + val.id + '" name="driver-delete" class="delete-btn" href="javascript:void(0)"><i class="mdi mdi-delete"></i></a>';
           
           
            actionHtml += '</span>';
            html.push(actionHtml);
            return html;
        }
        $(document).on("click", "input[name='isActive']", function(e) {
            var ischeck = $(this).is(':checked');
            var id = this.id;
            if (ischeck) {
                database.collection('users').doc(id).update({
                    'isActive': true
                }).then(function(result) {});
            } else {
                database.collection('users').doc(id).update({
                    'isActive': false
                }).then(function(result) {});
            }
        });
        $("#is_active").click(function() {
            $("#driverTable .is_open").prop('checked', $(this).prop('checked'));
        });
        $("#deleteAll").click(async function() {
            if ($('#driverTable .is_open:checked').length) {
                jQuery("#overlay").show();
                if (confirm("{{ trans('lang.selected_delete_alert') }}")) {
                    jQuery("#overlay").show();
                    let deletePromises = [];
                    $('#driverTable .is_open:checked').each(async function() {
                        var dataId = $(this).attr('dataId');
                        let deletePromise = (async () => {
                            await deleteDocumentWithImage('driver_users', dataId, 'profilePic')
                            await deleteUserData(dataId);
                        })();
                        deletePromises.push(deletePromise);
                    });
                    await Promise.all(deletePromises);
                    setTimeout(function() {
                        window.location.reload();
                    }, 2000);
                }
            } else {
                alert("{{ trans('lang.selected_delete_alert') }}");
            }
        });
        async function deleteUserData(userId) {
            // Delete user from authentication
            const idToken = await firebase.auth().currentUser.getIdToken();
            return new Promise((resolve, reject) => {
                var dataObject = {
                    "data": {
                        "uid": userId
                    }
                };
                var projectId = '<?php echo env('FIREBASE_PROJECT_ID'); ?>';
                jQuery.ajax({
                    url: 'https://us-central1-' + projectId + '.cloudfunctions.net/deleteUser',
                    method: 'POST',
                    crossDomain: true,
                    contentType: "application/json; charset=utf-8",
                    data: JSON.stringify(dataObject),
                    headers: {
                        Authorization: 'Bearer ' + idToken
                    },
                    success: function(data) {
                        console.log('Delete user success:', data.result);
                        resolve(data);
                    },
                    
                    error: function (xhr, status, error) {
                        let errorMsg = 'Unknown error';

                        try {
                            let parsed = (typeof xhr.responseText === 'string') 
                                ? JSON.parse(xhr.responseText) 
                                : xhr.responseText;

                            if (parsed.error) {
                                errorMsg = parsed.error;
                            } else if (parsed.message) {
                                errorMsg = parsed.message;
                            } else {
                                errorMsg = JSON.stringify(parsed);
                            }
                        } catch (e) {
                            errorMsg = xhr.responseText || error || status;
                        }

                        console.warn('Delete user error:', errorMsg);
                       
                        if (errorMsg.includes('There is no user record')) {
                            resolve({ message: errorMsg, skipped: true });
                        } else {
                            reject(new Error(errorMsg));
                        }
                    }


                });
            });
        }
        $(document).on("click", "a[name='driver-delete']", async function(e) {
            if (confirm(deleteMsg)) {
                jQuery("#overlay").show();
                var id = this.id;
                await deleteDocumentWithImage('driver_users', id, 'profilePic');
                await deleteUserData(id);
                setTimeout(function() {
                    window.location.reload();
                }, 2000);
            }
        });
        async function deleteDriverData(driverId) {
            await database.collection('order_transactions').where('driverId', '==', driverId).get().then(async function(snapshotsOrderTransacation) {
                if (snapshotsOrderTransacation.docs.length > 0) {
                    snapshotsOrderTransacation.docs.forEach((temData) => {
                        var item_data = temData.data();
                        database.collection('order_transactions').doc(item_data.id).delete().then(function() {});
                    });
                }
            });
            await database.collection('driver_payouts').where('driverID', '==', driverId).get().then(async function(snapshotsItem) {
                if (snapshotsItem.docs.length > 0) {
                    snapshotsItem.docs.forEach((temData) => {
                        var item_data = temData.data();
                        database.collection('driver_payouts').doc(item_data.id).delete().then(function() {});
                    });
                }
            });
        }
        $(document).on("click", ".create-driver-btn", async function (e) {
            e.preventDefault();

            try {     
                var settingsDoc = await database.collection('settings')
                    .doc('globalValue')
                    .get();

                if (settingsDoc.exists) {
                    var settingsData = settingsDoc.data();
                    if (settingsData.isVerifyOwnerDocument === true) {                       
                        var ownerSnap = await database.collection('owner_users')
                            .where('id', '==', ownerId)
                            .get();

                        if (!ownerSnap.empty) {
                            var ownerData = ownerSnap.docs[0].data();
                            if (ownerData.documentVerification === false) {
                                alert("{{trans('lang.you_are_not_allowed_to_create_driver_until_your_documents_are_verified')}}");
                                return; 
                            }
                        }
                    }
                }         
                var snapshots = await database.collection('subscription_history')
                    .where('user_id', '==', ownerId)
                    .orderBy('createdAt', 'desc')
                    .limit(1)
                    .get();

                if (snapshots.empty) {
                    alert("{{trans('lang.no_active_subscription_found_purchase_plan_first')}}");
                    return;
                }

                var subscriptionData = snapshots.docs[0].data();
                var activePlanId = subscriptionData.subscription_plan.id;
               
                var planSnap = await database.collection('subscription_plans')
                    .where('isEnable', '==', true)
                    .where('id', '==', activePlanId)
                    .get();

                if (planSnap.empty) {
                    alert("{{trans('lang.active_subscription_plan_not_found_or_disabled')}}");
                    return;
                }

                var planData = planSnap.docs[0].data();
                var driverLimit = planData.driverLimit ? parseInt(planData.driverLimit) : null;
           
                var driverSnap = await database.collection('driver_users')
                    .where('ownerId', '==', ownerId)
                    .get();

                var driverCount = driverSnap.size;

                if (driverLimit && driverCount >= driverLimit) {
                    alert("{{trans('lang.you_cannot_create_more_drivers_your_plan_allows_only')}} " + driverLimit + " {{trans('lang.driver_plural')}}.");
                    return;
                }
              
                window.location.href = "{{ route('drivers.create') }}";

            } catch (error) {
                console.error("Error checking subscription/driver limit:", error);                
            }
        });
       
        
    </script>
@endsection
