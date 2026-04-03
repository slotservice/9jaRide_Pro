@extends('layouts.app')
@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{ trans('lang.service_plural') }}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ trans('lang.dashboard') }}</a></li>
                <li class="breadcrumb-item active">{{ trans('lang.service_table') }}</li>
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
                            <span class="icon mr-3"><img src="{{ asset('images/category.png') }}"></span>
                            <h3 class="mb-0">{{trans('lang.service_plural')}}</h3>
                            <span class="counter ml-3 total_count"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card border">
                    <div class="card-header d-flex justify-content-between align-items-center border-0">
                        <div class="card-header-title">
                            <h3 class="text-dark-2 mb-2 h4">{{trans('lang.service_table')}}</h3>
                            <p class="mb-0 text-dark-2">{{trans('lang.service_table_text')}}</p>
                        </div>
                        <div class="card-header-right d-flex align-items-center">
                            <div class="card-header-btn mr-3">
                                <a class="btn-primary btn rounded-full" href="{{ route('services.create') }}"><i
                                        class="mdi mdi-plus mr-2"></i>{{trans('lang.service_create')}}</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="data-table_processing" class="dataTables_processing panel panel-default"
                            style="display: none;">{{ trans('lang.processing') }}
                        </div>
                        <div class="table-responsive m-t-10">
                            <table id="taxTable"
                                class="display nowrap table table-hover table-striped table-bordered table table-striped"
                                cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <?php if (in_array('service.delete', json_decode(@session('user_permissions')))) { ?>
                                            <th class="delete-all"><input type="checkbox" id="is_active"><label
                                                    class="col-3 control-label" for="is_active"><a id="deleteAll"
                                                        class="do_not_delete" href="javascript:void(0)"><i
                                                            class="mdi mdi-delete"></i> {{ trans('lang.all') }}</a></label>
                                            </th>
                                        <?php } ?>
                                        <th>{{ trans('lang.title') }}</th>
                                        <th>{{ trans('lang.basic') }} <span class="global_basic_label"></span> </th>
                                        <th>{{ trans('lang.basic') }} <span class="global_basic_label"></span> {{ trans('lang.amount') }}</th>
                                        <th>{{ trans('lang.ac_charges') }}</th>
                                        <th>{{ trans('lang.nonac_charges') }}</th>
                                        <th>{{ trans('lang.enable_offer_rate') }}</th>
                                        <th>{{ trans('lang.status') }}</th>
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

    var database=firebase.firestore();
    var offest=1;
    var pagesize=10;
    var end=null;
    var endarray=[];
    var start=null;
    var user_number=[];
    var ref=database.collection('service');
    var defaultImg="{{ asset('/images/default_user.png') }}";
    var append_list='';
    var deleteMsg="{{ trans('lang.delete_alert') }}";
    var deleteSelectedRecordMsg="{{ trans('lang.selected_delete_alert') }}";
    var refCurrency=database.collection('currency').where('enable','==',true).limit('1');
    var user_permissions='<?php echo @session('user_permissions') ?>';
    user_permissions=JSON.parse(user_permissions);
    var checkDeletePermission=false;
    if($.inArray('service.delete',user_permissions)>=0) {
        checkDeletePermission=true;
    }
    var setLanguageCode=getCookie('setLanguage');
    var defaultLanguageCode=getCookie('defaultLanguage');
    var decimal_degits=0;
    var symbolAtRight=false;
    var currentCurrency='';
    refCurrency.get().then(async function(snapshots) {
        var currencyData=snapshots.docs[0].data();
        currentCurrency=currencyData.symbol;
        decimal_degits=currencyData.decimalDigits;
        if(currencyData.symbolAtRight) {
            symbolAtRight=true;
        }
    });

    $(document).ready(function() {
        $(document.body).on('click','.redirecttopage',function() {
            var url=$(this).attr('data-url');
            window.location.href=url;
        });
        var inx=parseInt(offest)*parseInt(pagesize);
        jQuery("#overlay").show();
        append_list=document.getElementById('append_list1');
        append_list.innerHTML='';
        ref.get().then(async function(snapshots) {
            html='';
            $(".total_count").text(snapshots.docs.length);
            if(snapshots.docs.length>0) {
                html=await buildHTML(snapshots);
            }
            if(html!='') {
                append_list.innerHTML=html;
                start=snapshots.docs[snapshots.docs.length-1];
                endarray.push(snapshots.docs[0]);
                if(snapshots.docs.length<pagesize) {
                    jQuery("#overlay").hide();
                }
            }
            if(checkDeletePermission) {
                $('#taxTable').DataTable({
                    order: [
                        [1,'asc']
                    ],
                    columnDefs: [{
                        orderable: false,
                        targets: [0,6,7,8]
                    },
                    ],
                    order: [
                        ['1','asc']
                    ],
                    "language": datatableLang,
                    responsive: true
                });
            } else {
                $('#taxTable').DataTable({
                    order: [
                        [0,'asc']
                    ],
                    columnDefs: [{
                        orderable: false,
                        targets: [2,6,7]
                    },
                    ],
                    order: [
                        ['0','asc']
                    ],
                    "language": datatableLang,
                    responsive: true
                });
            }
            jQuery("#overlay").hide();
        });
    });

    $('.filteredRecords').change(async function() {
        var status=$('.status_selector').val();
        ref=database.collection('service');
        if(status) {
            ref=(status=="active")? ref.where('enable','==',true):ref.where('enable','==',false);
        }
        $('#taxTable').DataTable().ajax.reload();
    });
    
    async function buildHTML(snapshots) {
        var html='';
        await Promise.all(snapshots.docs.map(async (listval) => {
            var val=listval.data();
            var getData=await getListData(val);
            html+=getData;
        }));
        return html;
    }
    
    async function getListData(val) {
        var html='';
        html=html+'<tr>';
        newdate='';
        var id=val.id;
        var route1='{{ route("services.edit",":id") }}';
        route1=route1.replace(':id',id);
        var trroute1='';
        trroute1=trroute1.replace(':id',id);
        if(checkDeletePermission) {
            html=html+'<td class="delete-all"><input type="checkbox" id="is_open_'+id+
                '" class="is_open" dataId="'+id+'"><label class="col-3 control-label"\n'+
                'for="is_open_'+id+'" ></label></td>';
        }
        if(val.image=='') {
            imageHtml='<img width="100%" style="width:70px;height:70px;" src="'+defaultImg+'" alt="image">';
        } else {
            imageHtml='<img width="100%" style="width:70px;height:70px;" src="'+val.image+'" alt="image">';
        }
        
        var title='';
        if(Array.isArray(val.title)) {
            var foundItem=val.title.find(item => item.type===setLanguageCode);
            if(foundItem&&foundItem.title!='') {
                title=foundItem.title;
            } else {
                var foundItem=val.title.find(item => item.type===defaultLanguageCode);
                if(foundItem&&foundItem.title!='') {
                    title=foundItem.title;
                } else {
                    var foundItem=val.title.find(item => item.type==='en');
                    title=foundItem.title;
                }
            }
        }
        html=html+'<td>'+imageHtml+'<a href="'+route1+'" name="service-edit">'+title+'</a></td>';
        
        var basicFare = val.prices[0].basicFare;
        var kmCharge= parseFloat(val.prices[0].basicFareCharge);
        var acCharge= parseFloat(val.prices[0].acCharge);
        var nonAcCharge= parseFloat(val.prices[0].nonAcCharge);
        
        if(symbolAtRight) {
            html+='<td>'+basicFare+'</td>';
            html+='<td>'+kmCharge.toFixed(decimal_degits)+currentCurrency+'</td>';
            if(acCharge){
                html+='<td>'+acCharge.toFixed(decimal_degits)+currentCurrency+'</td>';
            }else{
                html+='<td>-</td>';
            }
            if(nonAcCharge){
                html+='<td>'+nonAcCharge.toFixed(decimal_degits)+currentCurrency+'</td>';
            }else{
                html+='<td>-</td>';
            }
        } else {
            html+='<td>'+basicFare+'</td>';
            html+='<td>'+currentCurrency+kmCharge.toFixed(decimal_degits)+'</td>';
            if(acCharge){
                html+='<td>'+currentCurrency+acCharge.toFixed(decimal_degits)+'</td>';
            }else{
                html+='<td>-</td>';
            }
            if(nonAcCharge){
                html+='<td>'+currentCurrency+nonAcCharge.toFixed(decimal_degits)+'</td>';
            }else{
                html+='<td>-</td>';
            }
        }

        if(val.offerRate) {
            html=html+'<td><label class="switch"><input  type="checkbox" checked id="'+val.id+
                '" name="isofferRate"><span class="slider round"></span></label></td>';
        } else {
            html=html+'<td><label class="switch"><input  type="checkbox" id="'+val.id+
                '" name="isofferRate"><span class="slider round"></span></label></td>';
        }
        
        if(val.enable) {
            html=html+'<td><label class="switch"><input type="checkbox" checked id="'+val.id+
                '" name="isSwitch"><span class="slider round"></span></label></td>';
        } else {
            html=html+'<td><label class="switch"><input type="checkbox" id="'+val.id+
                '" name="isSwitch"><span class="slider round"></span></label></td>';
        }
        html=html+'<td class="action-btn"><a href="'+route1+'" name="service-edit"><i class="mdi mdi-lead-pencil"></i></a>';
        
            if(checkDeletePermission) {
            html=html+'<a id="'+val.id+
                '" class="delete-btn" name="service-delete" href="javascript:void(0)"><i class="mdi mdi-delete"></i></a>';
        }
        html=html+'</td>';
        html=html+'</tr>';
        return html;
    }

    $("#is_active").click(function() {
        $("#taxTable .is_open").prop('checked',$(this).prop('checked'));
    });
    
    $("#deleteAll").click(function() {
        if($('#taxTable .is_open:checked').length) {
            if(confirm(deleteSelectedRecordMsg)) {
                jQuery("#overlay").show();
                $('#taxTable .is_open:checked').each(async function() {
                    var dataId=$(this).attr('dataId');
                    await deleteDocumentWithImage('service',dataId,'image').then(function() {
                        window.location.reload();
                    });
                });
            } else {
                return false;
            }
        } else {
            alert("{{ trans('lang.select_delete_alert') }}");
        }
    });
    
    $(document).on("click","input[name='isSwitch']",function(e) {
        var ischeck=$(this).is(':checked');
        var id=this.id;
        if(ischeck) {
            database.collection('service').doc(id).update({
                'enable': true
            }).then(function(result) {
            });
        } else {
            database.collection('service').doc(id).update({
                'enable': false
            }).then(function(result) {
            });
        }
    });

    $(document).on("click","input[name='isofferRate']",function(e) {
        var ischeck=$(this).is(':checked');
        var id=this.id;
        if(ischeck) {
            database.collection('service').doc(id).update({
                'offerRate': true
            }).then(function(result) {
            });
        } else {
            database.collection('service').doc(id).update({
                'offerRate': false
            }).then(function(result) {
            });
        }
    });
    
    $(document).on("click","a[name='service-delete']",async function(e) {
        if(confirm(deleteMsg)) {
            var id=this.id;
            jQuery("#overlay").show();
            await deleteDocumentWithImage('service',id,'image').then(function(result) {
                window.location.href='{{ url()->current() }}';
            });
        } else {
            return false;
        }
    });

</script>

@endsection