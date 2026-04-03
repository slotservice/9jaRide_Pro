@extends('layouts.app')
@section('content')
    @php
        $type = 'all';
    @endphp
    <div class="page-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">
                    @if (request()->is('owners/approved'))
                        @php $type = 'approved'; @endphp
                        {{ trans('lang.approved_owners') }}
                    @elseif(request()->is('owners/pending'))
                        @php $type = 'pending'; @endphp
                        {{ trans('lang.approval_pending_owners') }}
                    @else
                        {{ trans('lang.all_owners') }}
                    @endif
                </h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ trans('lang.dashboard') }}</a></li>
                    <li class="breadcrumb-item active">{{ trans('lang.owner_table') }}</li>
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
                                <span class="icon mr-3"><img src="{{ asset('images/users.png') }}"></span>
                                <h3 class="mb-0">{{ trans('lang.owner_table') }}</h3>
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
                                    <h3 class="text-dark-2 mb-2 h4">{{ trans('lang.owner_table') }}</h3>
                                    <p class="mb-0 text-dark-2">{{ trans('lang.owner_table_text') }}</p>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive m-t-10">
                                    <table id="ownerTable" class="display nowrap table table-hover table-striped table-bordered table table-striped" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <?php if (in_array('owner.delete', json_decode(@session('user_permissions')))) { ?>
                                                <th class="delete-all"><input type="checkbox" id="is_active"><label class="col-3 control-label" for="is_active"><a id="deleteAll" class="do_not_delete" href="javascript:void(0)"><i class="fa fa-trash"></i> {{ trans('lang.all') }}</a></label>
                                                </th>
                                                <?php } ?>
                                                <th>{{ trans('lang.user_info') }}</th>
                                                <th>{{ trans('lang.email') }}</th>
                                                <th>{{ trans('lang.phone') }}</th>
                                                <th>{{ trans('lang.document_plural') }}</th>
                                                <th>{{ trans('lang.date') }}</th>
                                                <th>{{ trans('lang.current_plan') }}</th>
                                                <th>{{ trans('lang.expiry_date') }}</th>
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
        const urlParams = new URLSearchParams(window.location.search);
        var type = "{{ $type }}";
        if (urlParams.has('today')) {
            const today = new Date();
            const startOfDay = new Date(today.setHours(0, 0, 0, 0));
            const endOfDay = new Date(today.setHours(23, 59, 59, 999));
            var ref = database.collection('owner_users').where("createdAt", ">=", startOfDay).where("createdAt", "<=", endOfDay);
        } else {
            var ref = database.collection('owner_users');
        }
        if (type == 'pending') {
            ref = database.collection('owner_users').where("documentVerification", "==", false);
        } else if (type == 'approved') {
            ref = database.collection('owner_users').where("documentVerification", "==", true);
        }
        var placeholderImage = "{{ asset('/images/default_user.png') }}";
        var deleteMsg = "{{ trans('lang.delete_alert') }}";
        var deleteSelectedRecordMsg = "{{ trans('lang.selected_delete_alert') }}";
        var setLanguageCode = getCookie('setLanguage');
        var defaultLanguageCode = getCookie('defaultLanguage');
        var user_permissions = '<?php echo @session('user_permissions'); ?>';
        user_permissions = JSON.parse(user_permissions);
        var checkDeletePermission = false;
        if ($.inArray('owner.delete', user_permissions) >= 0) {
            checkDeletePermission = true;
        }
        var checkChatPermission = false;
        if ($.inArray('owners.chat', user_permissions) >= 0) {
            checkChatPermission = true;
        }
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
            $('#ownerTable').DataTable().ajax.reload();
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
                        key: 'createdAt',
                        header: "{{ trans('lang.date') }}"
                    },
                ],
                fileName: "{{ trans('lang.owner_table') }}",
            };
            const table = $('#ownerTable').DataTable({
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
                    const orderableColumns = (checkDeletePermission) ? ['', 'fullName', 'email', 'phone', '', 'createdAt', 'expiryDate', 'activePlanName', 'totalRide', ''] : ['fullName', 'email', 'phone', '', 'createdAt', 'expiryDate', 'activePlanName', 'totalRide', '']; // Ensure this matches the actual column names
                    const orderByField = orderableColumns[orderColumnIndex]; // Adjust the index to match your table
                    if (searchValue.length >= 3 || searchValue.length === 0) {
                        $('#overlay').show();
                    }
                    ref.orderBy('createdAt', 'desc').get().then(async function(querySnapshot) {
                        if (querySnapshot.empty) {
                            $('.total_count').text(0);
                            console.error("No data found in Firestore.");
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
                        // Fetch owner names
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
                            if (childData.hasOwnProperty("subscriptionExpiryDate") && childData.subscriptionExpiryDate != null) {
                                try {
                                    date = childData.subscriptionExpiryDate.toDate().toDateString();
                                    time = childData.subscriptionExpiryDate.toDate().toLocaleTimeString('en-US');
                                } catch (err) {}
                                childData.expiryDate = date + ' ' + time;
                            }
                            if (childData.hasOwnProperty('subscription_plan') && childData.subscription_plan && childData.subscription_plan.name) {
                                childData.activePlanName = childData.subscription_plan.name;
                            } else {
                                childData.activePlanName = '';
                            }
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
                                    (createdAt && createdAt.toString().toLowerCase().indexOf(searchValue) > -1) ||
                                    (childData.phone && childData.phone.toString().includes(searchValue)) ||
                                    (childData.email && childData.email.toString().toLowerCase().includes(searchValue)) ||
                                    (childData.expiryDate && childData.expiryDate.toString().toLowerCase().indexOf(searchValue) > -1) ||
                                    (childData.hasOwnProperty('activePlanName') && childData.activePlanName.toLowerCase().toString().includes(searchValue))
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
                                const totalOrderSnapShot = await database.collection('orders').where('ownerId', '==', childData.id).get();
                                const rides = totalOrderSnapShot.size;
                                const totalIntercityOrderSnapShot = await database.collection('orders_intercity').where('ownerId', '==', childData.id).get();
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
                order: (checkDeletePermission) ? [
                    [5, 'desc']
                ] : [
                    [4, 'desc']
                ],
                columnDefs: [{
                        targets: (checkDeletePermission) ? 5 : 4,
                        type: 'date',
                        render: function(data) {
                            return data;
                        }
                    },
                    {
                        orderable: false,
                        targets: (checkDeletePermission) ? [0, 4, 8, 9] : [3, 7, 8]
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
                            text: '{{trans("lang.export_excel")}}',
                            action: function(e, dt, button, config) {
                                exportData(dt, 'excel', fieldConfig);
                            }
                        },
                        {
                            extend: 'pdfHtml5',
                            text: '{{trans("lang.export_pdf")}}',
                            action: function(e, dt, button, config) {
                                exportData(dt, 'pdf', fieldConfig);
                            }
                        },
                        {
                            extend: 'csvHtml5',
                            text: '{{trans("lang.export_csv")}}',
                            action: function(e, dt, button, config) {
                                exportData(dt, 'csv', fieldConfig);
                            }
                        }
                    ]
                }],
                initComplete: function() {
                    $(".dataTables_filter").append($(".dt-buttons").detach());
                    $('.dataTables_filter input').attr('placeholder', 'Search here...').attr('autocomplete', 'new-password').val('');
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
            var unreadCount = await countUnreadMessages(val.id);
            var html = [];
            newdate = '';
            var id = val.id;
            var route1 = '{{ route('owners.edit', ':id') }}';
            route1 = route1.replace(':id', id);
            var ownerView = '{{ route('owners.view', ':id') }}';
            ownerView = ownerView.replace(':id', id);
            if (checkDeletePermission) {
                html.push('<input type="checkbox" id="is_open_' + id + '" class="is_open" dataId="' + id + '"><label class="col-3 control-label"\n' +
                    'for="is_open_' + id + '" ></label>');
            }
            if (val.profilePic == '' || val.profilePic == null) {
                var userImg = '<img width="100%" style="width:70px;height:70px;" src="' + placeholderImage + '" alt="image">';
            } else {
                var userImg = '<img width="100%" style="width:70px;height:70px;" src="' + val.profilePic + '" alt="image">';
            }
            var verified = '';
            if(val.documentVerification == true){
                verified = '<i class="mdi mdi-verified verified-icon" title="Verified"></i>';
            }
            html.push(userImg + '<a href="' + ownerView + '">' + val.fullName + '</a>' + verified);
            html.push(shortEmail(val.email));
            if (val.countryCode != null && val.countryCode.includes('+')) {
                val.countryCode = val.countryCode.slice(1);
            } else {
                val.countryCode = val.countryCode;
            }
            html.push(val.phone);
            var ownerDocView = '{{ route('owners.document', ':id') }}';
            ownerDocView = ownerDocView.replace(':id', id);
            html.push('<span class="action-btn"><a href="' + ownerDocView + '"><i class="mdi mdi-file"></i></a>');
            if (val.hasOwnProperty("createdAt") && val.createdAt != null && val.createdAt != '') {
                var date = val.createdAt.toDate().toDateString();
                var time = val.createdAt.toDate().toLocaleTimeString('en-US');
                html.push('<span class="dt-time">' + date + ' ' + time + '</span>');
            } else {
                html.push('');
            }
            if (val.hasOwnProperty('subscription_plan') && val.subscription_plan && val.subscription_plan.name) {
                html.push(val.subscription_plan.name);
            } else {
                html.push('');
            }
            if (val.hasOwnProperty('subscriptionExpiryDate') && val.subscriptionExpiryDate != null) {
                html.push(val.expiryDate);
            } else if (val.hasOwnProperty('subscriptionExpiryDate') && val.subscriptionExpiryDate == null) {
                html.push('{{ trans('lang.unlimited') }}');
            } else {
                html.push('');
            }
            
            var trroute1 = '';
            trroute1 = trroute1.replace(':id', 'ownerId=' + id);
           
            html.push(val.total_rides);
            var chatViewRoute = '{{ route('owners.chat', ':id') }}'.replace(':id', val.id);

            var actionHtml = '';

            actionHtml = actionHtml + '<span class="action-btn"><a href="' + ownerView + '"><i class="mdi mdi-eye"></i></a><a href="' + route1 + '"><i class="mdi mdi-lead-pencil"></i></a>';

            if (checkDeletePermission) {
                actionHtml = actionHtml + '<a id="' + val.id + '" name="owner-delete" class="delete-btn" href="javascript:void(0)"><i class="mdi mdi-delete"></i></a>';
            }
            if (checkChatPermission) {
                actionHtml = actionHtml + `<a href="${chatViewRoute}" class="chat-message">
                                    <i class="mdi mdi-wechat mdi-24px"></i>
                                    ${ unreadCount > 0 ? `<span class="unread-count chat-${val.id}">${unreadCount}</span>` : ``}
                                </a>`;
            }
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
            $("#ownerTable .is_open").prop('checked', $(this).prop('checked'));
        });
        $("#deleteAll").click(async function() {
            if ($('#ownerTable .is_open:checked').length) {
                jQuery("#overlay").show();
                if (confirm("{{ trans('lang.selected_delete_alert') }}")) {
                    jQuery("#overlay").show();
                    let deletePromises = [];
                    $('#ownerTable .is_open:checked').each(async function() {
                        var dataId = $(this).attr('dataId');
                        let deletePromise = (async () => {
                            await deleteDocumentWithImage('owner_users', dataId, 'profilePic');
                            await deleteDriversByOwner(dataId);                        
                            await deleteSubscriptionHistory(dataId);
                            await deleteUserData(dataId);
                        })();
                        deletePromises.push(deletePromise);
                    });
                    await Promise.all(deletePromises);
                    setTimeout(function() {
                        window.location.reload();
                    }, 500);
                }else {                    
                    jQuery("#overlay").hide();
                }
            } else {
                alert("{{ trans('lang.select_delete_alert') }}");
            }
        });
       
        async function deleteUserData(userId) {
          
            database.collection('settings').doc("global").get().then(function(snapshot) {
                var settingData = snapshot.data();
                if (settingData && settingData.ownerPanelUrl) {
                    var siteurl = settingData.ownerPanelUrl + "/api/delete-user";
                    var dataObject = {
                        "uuid": userId
                    };
                    jQuery.ajax({
                        url: siteurl,
                        method: 'POST',
                        contentType: "application/json; charset=utf-8",
                        data: JSON.stringify(dataObject),
                        success: function(data) {
                            console.log('Delete user from sql success:', data);
                        },
                        error: function(error) {
                            console.log('Delete user from sql error:', error.responseJSON.message);
                        }
                    });
                }
            });
                
           
            const idToken = await firebase.auth().currentUser.getIdToken();
            return new Promise((resolve, reject) => {
                const dataObject = { data: { uid: userId } };
                const projectId = '<?php echo env('FIREBASE_PROJECT_ID'); ?>';
                jQuery.ajax({
                    url: 'https://us-central1-' + projectId + '.cloudfunctions.net/deleteUser',
                    method: 'POST',
                    crossDomain: true,
                    contentType: "application/json; charset=utf-8",
                    data: JSON.stringify(dataObject),
                    headers: { Authorization: 'Bearer ' + idToken },
                    success: function(data) {
                        console.log('Delete user success:', data.result);
                        resolve(data);
                    },
                    error: function(xhr, status, error) {
                        try {
                            const responseText = JSON.parse(xhr.responseText);
                            console.log('Delete user error:', responseText.error);

                            // Check the actual message instead of "user-not-found"
                            if (responseText.error && responseText.error.message &&
                                responseText.error.message.includes("no user record")) {
                                console.log('User not found in Auth, skipping deletion.');
                                resolve({ skipped: true });
                            } else {
                                reject(new Error(responseText.error.message || error));
                            }
                        } catch (e) {
                            // Fallback for unexpected errors
                            reject(new Error(error));
                        }
                    }
                });
            });
        }

        async function deleteDriversByOwner(ownerId) {
            const driversSnapshot = await database.collection('driver_users').where('ownerId', '==', ownerId).get();
            let driverDeletePromises = [];
            driversSnapshot.forEach((doc) => {
                driverDeletePromises.push(deleteDocumentWithImage('driver_users', doc.id, 'profilePic'));
                driverDeletePromises.push(deleteUserData(doc.id)); // delete from Auth too
            });
            await Promise.all(driverDeletePromises);
            console.log(`Deleted drivers for owner ${ownerId}`);
        }
        async function deleteSubscriptionHistory(userId) {
            const subSnapshot = await database.collection('subscription_history').where('user_id', '==', userId).get();
            let subDeletePromises = [];
            subSnapshot.forEach((doc) => {
                subDeletePromises.push(database.collection('subscription_history').doc(doc.id).delete());
            });
            await Promise.all(subDeletePromises);
            console.log(`Deleted subscription history for user ${userId}`);
        }

        $(document).on("click", "a[name='owner-delete']", async function(e) {
            if (confirm(deleteMsg)) {
                jQuery("#overlay").show();
                var id = this.id;
                await deleteDocumentWithImage('owner_users', id, 'profilePic');
                await deleteDriversByOwner(id);                        
                await deleteSubscriptionHistory(id);
                await deleteUserData(id);
                setTimeout(function() {
                    window.location.reload();
                }, 500);
            }
        });
        async function deleteDriverData(ownerId) {
            await database.collection('order_transactions').where('ownerId', '==', ownerId).get().then(async function(snapshotsOrderTransacation) {
                if (snapshotsOrderTransacation.docs.length > 0) {
                    snapshotsOrderTransacation.docs.forEach((temData) => {
                        var item_data = temData.data();
                        database.collection('order_transactions').doc(item_data.id).delete().then(function() {});
                    });
                }
            });
            await database.collection('owner_payouts').where('ownerID', '==', ownerId).get().then(async function(snapshotsItem) {
                if (snapshotsItem.docs.length > 0) {
                    snapshotsItem.docs.forEach((temData) => {
                        var item_data = temData.data();
                        database.collection('owner_payouts').doc(item_data.id).delete().then(function() {});
                    });
                }
            });
        }
        async function countUnreadMessages(ownerId) {
            var unreadCount = 0;
            await database.collection('chat_admin').doc(ownerId).collection("thread")
                .where("seen", "==", false)
                .where("senderId", "!=", "admin") // Only count messages sent by user
                .onSnapshot(snapshot => {
                    unreadCount = snapshot.size;
                });
            return unreadCount;
        }
    </script>
@endsection
