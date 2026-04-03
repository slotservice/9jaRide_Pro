@extends('layouts.app')
@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor plan_title">{{trans('lang.current_subscriber_list_of')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item"><a href="{{url('/subscription-plans')}}">{{trans('lang.subscription_plans')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.current_subscriber_list')}}</li>
            </ol>
        </div>
        <div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs align-items-end card-header-tabs w-100">
                            <li class="nav-item">
                                <a class="nav-link active" href="{!! url()->current() !!}"><i
                                        class="fa fa-list mr-2"></i>{{trans('lang.current_subscriber_list')}}</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive m-t-10">
                            <table id="subscriptionHistoryTable"
                                class="display nowrap table table-hover table-striped table-bordered table table-striped"
                                cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>{{ trans('lang.driver_owner')}}</th>
                                        <th>{{trans('lang.plan_name')}}</th>
                                        <th>{{trans('lang.plan_type')}}</th>
                                        <th>{{trans('lang.plan_expires_at')}}</th>
                                        <th>{{trans('lang.booking_limit')}}</th>
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
@endsection
@section('scripts')
<script>
    var database=firebase.firestore();
    var intRegex=/^\d+$/;
    var floatRegex=/^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;
    var planId='{{$id}}';
    var planFor='';  
   
    var currentCurrency='';
    var currencyAtRight=false;
    var decimal_degits=0;
    var refCurrency=database.collection('currency').where('enable','==',true);
    refCurrency.get().then(async function(snapshots) {
        var currencyData=snapshots.docs[0].data();
        currentCurrency=currencyData.symbol;
        currencyAtRight=currencyData.symbolAtRight;
        if(currencyData.decimal_degits) {
            decimal_degits=currencyData.decimalDigits;
        }
    });
    var append_list='';
    $(document).ready(async function() {
        await database.collection('subscription_plans').where('id','==',planId).get().then(async function(snapshot) {
            var data=snapshot.docs[0].data();
            $('.plan_title').html('{{trans("lang.current_subscriber_list_of")}} - '+data.name);
            planFor = data.planFor;
            if (planFor === 'owner') {
                $("#subscriptionHistoryTable thead tr").append('<th>{{trans("lang.driver_limit")}}</th>');
            }
        })
        $(document.body).on('click','.redirecttopage',function() {
            var url=$(this).attr('data-url');
            window.location.href=url;
        });
        jQuery("#data-table_processing").show();
        const table=$('#subscriptionHistoryTable').DataTable({
            pageLength: 10,
            processing: false,
            serverSide: true,
            responsive: true,          
            ajax: async function (data, callback, settings) {
                const start = data.start;
                const length = data.length;
                const searchValue = data.search.value.toLowerCase();
                const orderColumnIndex = data.order[0].column;
                const orderDirection = data.order[0].dir;
                const orderableColumns = [
                    'driver',
                    'subscription_plan.name',
                    'subscription_plan.type',
                    'subscription_plan.bookingLimit',
                    'subscriptionExpiryDate'
                ];
                if (planFor === 'owner') {
                    orderableColumns.push('subscription_plan.driverLimit');
                }
                const orderByField = orderableColumns[orderColumnIndex];
                $('#data-table_processing').show();

                try {
                    let snapshots = [];

                    if (planFor === 'driver') {
                        snapshots.push(
                            await database.collection('driver_users')
                                .where('subscriptionPlanId', '==', planId)
                                .orderBy('subscriptionExpiryDate', 'desc')
                                .get()
                        );
                    } else if (planFor === 'owner') {
                        snapshots.push(
                            await database.collection('owner_users')
                                .where('subscriptionPlanId', '==', planId)
                                .orderBy('subscriptionExpiryDate', 'desc')
                                .get()
                        );
                    } else if (planFor === 'both') {                        
                        const [driverSnap, ownerSnap] = await Promise.all([
                            database.collection('driver_users')
                                .where('subscriptionPlanId', '==', planId)
                                .orderBy('subscriptionExpiryDate', 'desc')
                                .get(),
                            database.collection('owner_users')
                                .where('subscriptionPlanId', '==', planId)
                                .orderBy('subscriptionExpiryDate', 'desc')
                                .get()
                        ]);
                        snapshots.push(driverSnap, ownerSnap);
                    }

                    let allDocs = [];
                    snapshots.forEach(snap => {
                        snap.forEach(doc => allDocs.push(doc));
                    });

                    if (allDocs.length === 0) {
                        $('#data-table_processing').hide();
                        callback({
                            draw: data.draw,
                            recordsTotal: 0,
                            recordsFiltered: 0,
                            data: []
                        });
                        return;
                    }
                  
                    let filteredRecords = [];
                    await Promise.all(allDocs.map(async (doc) => {
                        let childData = doc.data();
                        childData.driver = childData.fullName;
                        childData.id = doc.id;

                        childData.bookingCreated = (childData.hasOwnProperty('subscriptionTotalOrders') &&
                            childData.subscriptionTotalOrders) ? childData.subscriptionTotalOrders : 0;

                        if (searchValue) {
                            var date = '';
                            var time = '';
                            if (childData.subscriptionExpiryDate?.toDate) {
                                try {
                                    date = childData.subscriptionExpiryDate.toDate().toDateString();
                                    time = childData.subscriptionExpiryDate.toDate().toLocaleTimeString('en-US');
                                } catch (err) {
                                    console.error('Error processing expire_date:', err);
                                }
                            }
                            if (
                                (childData.driver && childData.driver.toLowerCase().includes(searchValue)) ||
                                (childData.subscription_plan?.name && childData.subscription_plan.name.toLowerCase().includes(searchValue)) ||
                                (childData.subscription_plan?.type && childData.subscription_plan.type.toLowerCase().includes(searchValue)) ||
                                (childData.subscription_plan?.bookingLimit && childData.subscription_plan.bookingLimit.toString().includes(searchValue)) ||
                                (childData.subscriptionExpiryDate && date.toLowerCase().includes(searchValue))
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
                        if (orderByField === 'subscriptionExpiryDate') {
                            try {
                                aValue = a[orderByField]?.toDate ? new Date(a[orderByField].toDate()).getTime() : 0;
                                bValue = b[orderByField]?.toDate ? new Date(b[orderByField].toDate()).getTime() : 0;
                            } catch (err) { }
                        }
                        return (orderDirection === 'asc') ? (aValue > bValue ? 1 : -1) : (aValue < bValue ? 1 : -1);
                    });

                    const totalRecords = filteredRecords.length;
                    const paginatedRecords = filteredRecords.slice(start, start + length);

                    let records = [];
                    await Promise.all(paginatedRecords.map(async (childData) => {
                        var getData = await buildHTML(childData);
                        records.push(getData);
                    }));

                    $('#data-table_processing').hide();
                    callback({
                        draw: data.draw,
                        recordsTotal: totalRecords,
                        recordsFiltered: totalRecords,
                        data: records
                    });

                } catch (error) {
                    console.error("Error fetching data from Firestore:", error);
                    $('#data-table_processing').hide();
                    callback({
                        draw: data.draw,
                        recordsTotal: 0,
                        recordsFiltered: 0,
                        data: []
                    });
                }
            },

            order: [3,'asc'],
            columnDefs: [
                {
                    targets: 3,
                    type: 'date',
                    render: function(data) {
                        return data;
                    }
                },
            ],
           "language": datatableLang,
        });
        function debounce(func,wait) {
            let timeout;
            const context=this;
            return function(...args) {
                clearTimeout(timeout);
                timeout=setTimeout(() => func.apply(context,args),wait);
            };
        }
        $('#search-input').on('input',debounce(function() {
            const searchValue=$(this).val();
            if(searchValue.length>=3) {
                $('#data-table_processing').show();
                table.search(searchValue).draw();
            } else if(searchValue.length===0) {
                $('#data-table_processing').show();
                table.search('').draw();
            }
        },300));
    });
    async function buildHTML(val) {
        var html=[];
        var route='{{route("drivers.view", ":id")}}';
        route=route.replace(':id',val.id);
        html.push('<a href="'+route+'" class="redirecttopage" >'+val.driver+'</a>');
        html.push('<span>'+val.subscription_plan.name+'</span>');
        if(val.subscription_plan.type=='free') {
            html.push('<span class="badge badge-success">'+val.subscription_plan.type.toUpperCase()+'</span>');
        } else {
            html.push('<span class="badge badge-danger">'+val.subscription_plan.type.toUpperCase()+'</span>');
        }
        if(val.subscriptionExpiryDate!=null && val.subscriptionExpiryDate!='') {
            var date=val.subscriptionExpiryDate.toDate().toDateString();
            var time=val.subscriptionExpiryDate.toDate().toLocaleTimeString('en-US');
            html.push('<span class="dt-time">'+date+' '+time+'</span>');
        } else {
            html.push('<span class="dt-time">{{trans("lang.unlimited")}}</span>');
        }
        if(val.subscription_plan.bookingLimit=='-1') {
            html.push('<span>{{trans("lang.unlimited")}}</span>')
        } else {
            // var available=parseInt(val.subscription_plan.bookingLimit)-parseInt(val.bookingCreated);
            var available=val.subscriptionTotalOrders || 0;
            html.push('<span>{{trans("lang.total")}} :'+val.subscription_plan.bookingLimit+' </span><br><span>{{trans("lang.available")}} :'+available+' </span>')
        }        
        if (planFor === 'owner') {
            if (val.subscription_plan.driverLimit == '-1') {
                html.push('<span>{{trans("lang.unlimited")}}</span>');
            } else {               
                html.push('<span>{{trans("lang.total")}} : ' + val.subscription_plan.driverLimit +
                    ' </span><br><span>{{trans("lang.available")}} : ' + (val.subscriptionTotalDrivers || 0) + ' </span>');
            }
        }
        return html;
    }
      
</script>
@endsection