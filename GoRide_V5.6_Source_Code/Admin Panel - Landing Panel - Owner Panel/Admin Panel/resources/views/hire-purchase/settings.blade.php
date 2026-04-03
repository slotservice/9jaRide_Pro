@extends('layouts.app')
@section('content')
    <div class="page-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">HP Settings</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ trans('lang.dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ url('/hire-purchase') }}">Hire Purchase</a></li>
                    <li class="breadcrumb-item active">Settings</li>
                </ol>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header" style="background:#1B5E20;color:#fff;">
                            <h4 class="mb-0"><i class="fa fa-cog"></i> Hire Purchase Configuration</h4>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-success d-none" id="hp_settings_success">Settings saved successfully!</div>
                            <div class="alert alert-danger d-none" id="hp_settings_error"></div>
                            <form id="hpSettingsForm">
                                <div class="form-group">
                                    <label><strong>Default Daily Deduction Amount (<span class="currentCurrency"></span>)</strong></label>
                                    <input type="number" class="form-control" id="hp_daily_deduction" placeholder="Enter daily deduction amount" step="0.01" min="0">
                                    <small class="text-muted">This amount will be deducted daily from each HP driver's wallet</small>
                                </div>
                                <div class="form-group">
                                    <label><strong>Yellow Warning Threshold (hours)</strong></label>
                                    <input type="number" class="form-control" id="hp_yellow_threshold" value="24" min="1">
                                    <small class="text-muted">Hours overdue before status changes to Yellow (warning)</small>
                                </div>
                                <div class="form-group">
                                    <label><strong>Red Lockout Threshold (hours)</strong></label>
                                    <input type="number" class="form-control" id="hp_red_threshold" value="48" min="1">
                                    <small class="text-muted">Hours overdue before status changes to Red (app lockout)</small>
                                </div>
                                <div class="form-group">
                                    <label><strong>Auto Kill Switch on Red Status</strong></label>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="hp_auto_kill" checked>
                                        <label class="custom-control-label" for="hp_auto_kill">Automatically lock driver app when status turns Red</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label><strong>Royalty Percentage (%)</strong></label>
                                    <input type="number" class="form-control" id="hp_royalty_percentage" value="2.5" step="0.1" min="0" max="100">
                                    <small class="text-muted">Percentage deducted from each ride fare (in addition to HP deduction)</small>
                                </div>
                                <button type="submit" class="btn btn-primary btn-lg"><i class="fa fa-save"></i> Save Settings</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header" style="background:#D4AF37;color:#fff;">
                            <h4 class="mb-0"><i class="fa fa-plus-circle"></i> Assign HP to Driver</h4>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-success d-none" id="hp_assign_success">HP assigned to driver successfully!</div>
                            <div class="alert alert-danger d-none" id="hp_assign_error"></div>
                            <form id="hpAssignForm">
                                <div class="form-group">
                                    <label><strong>Select Driver</strong></label>
                                    <select class="form-control" id="hp_driver_select" style="width:100%;">
                                        <option value="">Loading drivers...</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label><strong>Vehicle Total Cost (<span class="currentCurrency"></span>)</strong></label>
                                    <input type="number" class="form-control" id="hp_vehicle_cost" placeholder="Total vehicle cost" step="0.01" min="0">
                                </div>
                                <div class="form-group">
                                    <label><strong>Initial Payment (<span class="currentCurrency"></span>)</strong></label>
                                    <input type="number" class="form-control" id="hp_initial_payment" placeholder="Amount already paid" step="0.01" min="0" value="0">
                                </div>
                                <div class="form-group">
                                    <label><strong>Daily Deduction (<span class="currentCurrency"></span>)</strong></label>
                                    <input type="number" class="form-control" id="hp_assign_daily" placeholder="Daily deduction for this driver" step="0.01" min="0">
                                    <small class="text-muted">Leave empty to use the default from settings</small>
                                </div>
                                <button type="submit" class="btn btn-success btn-lg"><i class="fa fa-check"></i> Assign HP Plan</button>
                            </form>
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
    var currencySymbol = '';

    database.collection('currency').where('enable', '==', true).get().then(function(snap) {
        if (snap.docs.length > 0) {
            currencySymbol = snap.docs[0].data().symbol;
            $(".currentCurrency").text(currencySymbol);
        }
    });

    // Load current settings
    database.collection('settings').doc('hirePurchase').get().then(function(doc) {
        if (doc.exists) {
            var data = doc.data();
            $('#hp_daily_deduction').val(data.defaultDailyDeduction || 0);
            $('#hp_yellow_threshold').val(data.yellowThresholdHours || 24);
            $('#hp_red_threshold').val(data.redThresholdHours || 48);
            $('#hp_auto_kill').prop('checked', data.autoKillSwitch !== false);
            $('#hp_royalty_percentage').val(data.royaltyPercentage || 2.5);
        }
    });

    // Save settings
    $('#hpSettingsForm').on('submit', function(e) {
        e.preventDefault();
        database.collection('settings').doc('hirePurchase').set({
            defaultDailyDeduction: parseFloat($('#hp_daily_deduction').val()) || 0,
            yellowThresholdHours: parseInt($('#hp_yellow_threshold').val()) || 24,
            redThresholdHours: parseInt($('#hp_red_threshold').val()) || 48,
            autoKillSwitch: $('#hp_auto_kill').is(':checked'),
            royaltyPercentage: parseFloat($('#hp_royalty_percentage').val()) || 2.5,
            updatedAt: firebase.firestore.FieldValue.serverTimestamp()
        }).then(function() {
            $('#hp_settings_success').removeClass('d-none').delay(3000).queue(function(n) { $(this).addClass('d-none'); n(); });
        }).catch(function(err) {
            $('#hp_settings_error').removeClass('d-none').text('Error: ' + err.message);
        });
    });

    // Load drivers for assignment dropdown
    database.collection('driver_users').get().then(function(snap) {
        var options = '<option value="">Select a driver</option>';
        snap.docs.forEach(function(doc) {
            var d = doc.data();
            if (!d.hpEnabled) {
                options += '<option value="' + doc.id + '">' + (d.fullName || 'Unknown') + ' (' + (d.phoneNumber || '') + ')</option>';
            }
        });
        $('#hp_driver_select').html(options);
        $('#hp_driver_select').select2({ placeholder: 'Select a driver', allowClear: true });
    });

    // Assign HP to driver
    $('#hpAssignForm').on('submit', function(e) {
        e.preventDefault();
        var driverId = $('#hp_driver_select').val();
        if (!driverId) { alert('Please select a driver'); return; }

        var totalCost = parseFloat($('#hp_vehicle_cost').val()) || 0;
        var initialPayment = parseFloat($('#hp_initial_payment').val()) || 0;
        var dailyDeduction = parseFloat($('#hp_assign_daily').val()) || parseFloat($('#hp_daily_deduction').val()) || 0;

        if (totalCost <= 0) { alert('Please enter vehicle total cost'); return; }
        if (dailyDeduction <= 0) { alert('Please enter daily deduction amount'); return; }

        database.collection('driver_users').doc(driverId).update({
            hpEnabled: true,
            hpTotalCost: totalCost,
            hpAmountPaid: initialPayment,
            hpBalance: totalCost - initialPayment,
            hpDailyDeduction: dailyDeduction,
            hpStatus: 'green',
            hpStartDate: firebase.firestore.FieldValue.serverTimestamp(),
            hpLastPaymentDate: firebase.firestore.FieldValue.serverTimestamp()
        }).then(function() {
            $('#hp_assign_success').removeClass('d-none');
            $('#hpAssignForm')[0].reset();
            $('#hp_driver_select').val('').trigger('change');
            setTimeout(function() { $('#hp_assign_success').addClass('d-none'); }, 3000);
        }).catch(function(err) {
            $('#hp_assign_error').removeClass('d-none').text('Error: ' + err.message);
        });
    });
</script>
@endsection
