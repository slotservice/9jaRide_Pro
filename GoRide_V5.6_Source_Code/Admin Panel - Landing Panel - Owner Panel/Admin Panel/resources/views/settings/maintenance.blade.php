@extends('layouts.app')
@section('content')
    <div class="page-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">{{ trans('lang.maintenance_mode_settings') }}</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ trans('lang.dashboard') }}</a></li>
                    <li class="breadcrumb-item active">{{ trans('lang.maintenance_mode_settings') }}</li>
                </ol>
            </div>
        </div>
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <div id="data-table_processing" class="dataTables_processing panel panel-default"
                        style="display: none;">{{ trans('lang.processing') }}</div>
                    <div class="error_top" style="display:none"></div>

                    <div class="row restaurant_payout_create">

                        <div class="restaurant_payout_create-inner">
                            <fieldset>
                                <legend>{{ trans('lang.maintenance_mode_settings') }}</legend>
                                <div class="form-check width-100">
                                    <input type="checkbox" class="form-check-inline" id="isMaintenanceModeForcustomerApp">
                                    <label class="col-5 control-label"
                                        for="isMaintenanceModeForcustomerApp">{{ trans('lang.enable_maintenance_customer_app') }}</label>
                                </div>
                                <div class="form-check width-100">
                                    <input type="checkbox" class="form-check-inline" id="isMaintenanceModeFordriverApp">
                                    <label class="col-5 control-label"
                                        for="isMaintenanceModeFordriverApp">{{ trans('lang.enable_maintenance_driver_app') }}</label>
                                </div>
                               
                                <div class="form-check width-100">
                                    <input type="checkbox" class="form-check-inline" id="isMaintenanceModeForownerApp">
                                    <label class="col-5 control-label"
                                        for="isMaintenanceModeForownerApp">{{ trans('lang.enable_maintenance_mode_for_owner') }}</label>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                      <div class="form-group col-12 text-center btm-btn">
                    <button type="button" class="btn btn-primary edit-setting-btn"><i class="fa fa-save"></i>
                        {{ trans('lang.save') }}</button>
                    <a href="{{ url('/dashboard') }}" class="btn btn-default"><i
                            class="fa fa-undo"></i>{{ trans('lang.cancel') }}
                    </a>
                </div>
                </div>
              
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        var database = firebase.firestore();
        var ref = database.collection('settings').doc("maintenance_settings");
        $(document).ready(function() {
            jQuery("#overlay").show();
            ref.get().then(async function(snapshots) {
                var documentVerification = snapshots.data();
                if (documentVerification == undefined) {
                    database.collection('settings').doc('maintenance_settings').set({});
                }
                try {
                    if (documentVerification.customerApp) {
                        $("#isMaintenanceModeForcustomerApp").prop('checked', true);
                    }
                    if (documentVerification.driverApp) {
                        $("#isMaintenanceModeFordriverApp").prop('checked', true);
                    }
                   
                    if (documentVerification.ownerApp) {
                        $("#isMaintenanceModeForownerApp").prop('checked', true);
                    }
                    

                } catch (error) {}
                jQuery("#overlay").hide();
            })
            $(".edit-setting-btn").click(function() {
                jQuery("#overlay").show();
                var enablecustomer = $("#isMaintenanceModeForcustomerApp").is(":checked");
                var enabledriver = $("#isMaintenanceModeFordriverApp").is(":checked");
              
                var enableownerApp = $("#isMaintenanceModeForownerApp").is(":checked");
                
                database.collection('settings').doc("maintenance_settings").update({
                    'customerApp': enablecustomer,
                    'driverApp': enabledriver,
                    
                    'ownerApp': enableownerApp,
                    
                }).then(function(result) {
                    window.location.href = '{{ url('settings/maintenance') }}';
                });
            })
        })
    </script>
@endsection
