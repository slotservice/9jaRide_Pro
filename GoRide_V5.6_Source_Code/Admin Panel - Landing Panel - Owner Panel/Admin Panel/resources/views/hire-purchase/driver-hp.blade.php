@extends('layouts.app')
@section('content')
    <div class="page-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Driver HP Details</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ trans('lang.dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ url('/hire-purchase') }}">Hire Purchase</a></li>
                    <li class="breadcrumb-item active">Driver Details</li>
                </ol>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row">
                <!-- Driver Info Card -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <img src="{{ asset('/images/default_user.png') }}" class="hp-driver-photo" style="width:100px;height:100px;border-radius:50%;object-fit:cover;margin-bottom:15px;">
                            <h4 class="hp-driver-name">Loading...</h4>
                            <p class="text-muted hp-driver-phone"></p>
                            <div class="hp-status-badge my-3"></div>
                            <hr>
                            <div class="text-left">
                                <p><strong>Email:</strong> <span class="hp-driver-email">-</span></p>
                                <p><strong>Vehicle:</strong> <span class="hp-driver-vehicle">-</span></p>
                                <p><strong>HP Start Date:</strong> <span class="hp-start-date">-</span></p>
                            </div>
                        </div>
                    </div>

                    <!-- Kill Switch Card -->
                    <div class="card">
                        <div class="card-header" style="background:#e6294b;color:#fff;">
                            <h5 class="mb-0"><i class="fa fa-power-off"></i> Kill Switch Control</h5>
                        </div>
                        <div class="card-body text-center">
                            <p class="kill-switch-status mb-3">Checking status...</p>
                            <button class="btn btn-danger btn-lg btn-block kill-lock-btn d-none" id="lockBtn"><i class="fa fa-lock"></i> Lock Driver App</button>
                            <button class="btn btn-success btn-lg btn-block kill-unlock-btn d-none" id="unlockBtn"><i class="fa fa-unlock"></i> Unlock Driver App</button>
                        </div>
                    </div>
                </div>

                <!-- HP Financial Details -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header" style="background:#1B5E20;color:#fff;">
                            <h4 class="mb-0"><i class="fa fa-car"></i> Hire Purchase Financial Summary</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <div class="p-3 border rounded">
                                        <h6 class="text-muted">Total Vehicle Cost</h6>
                                        <h2><span class="currentCurrency"></span><span class="hp-total-cost">0</span></h2>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="p-3 border rounded" style="background:#f0fff0;">
                                        <h6 class="text-muted">Amount Paid</h6>
                                        <h2 style="color:#1B5E20;"><span class="currentCurrency"></span><span class="hp-amount-paid">0</span></h2>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="p-3 border rounded" style="background:#fff8e1;">
                                        <h6 class="text-muted">Balance Remaining</h6>
                                        <h2 style="color:#D4AF37;"><span class="currentCurrency"></span><span class="hp-balance">0</span></h2>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="p-3 border rounded">
                                        <h6 class="text-muted">Daily Deduction</h6>
                                        <h2><span class="currentCurrency"></span><span class="hp-daily">0</span>/day</h2>
                                    </div>
                                </div>
                            </div>

                            <!-- Progress Bar -->
                            <div class="mt-3">
                                <h6>Payment Progress</h6>
                                <div class="progress" style="height:25px;border-radius:12px;">
                                    <div class="progress-bar hp-progress-bar" role="progressbar" style="width:0%;background:#1B5E20;border-radius:12px;font-size:14px;font-weight:600;">0%</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Update HP Details -->
                    <div class="card">
                        <div class="card-header" style="background:#D4AF37;color:#fff;">
                            <h4 class="mb-0"><i class="fa fa-edit"></i> Update HP Details</h4>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-success d-none" id="update_success">HP details updated successfully!</div>
                            <form id="updateHPForm" class="row">
                                <div class="col-md-4 form-group">
                                    <label><strong>Total Cost (<span class="currentCurrency"></span>)</strong></label>
                                    <input type="number" class="form-control" id="edit_total_cost" step="0.01" min="0">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label><strong>Amount Paid (<span class="currentCurrency"></span>)</strong></label>
                                    <input type="number" class="form-control" id="edit_amount_paid" step="0.01" min="0">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label><strong>Daily Deduction (<span class="currentCurrency"></span>)</strong></label>
                                    <input type="number" class="form-control" id="edit_daily_deduction" step="0.01" min="0">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label><strong>HP Status</strong></label>
                                    <select class="form-control" id="edit_hp_status">
                                        <option value="green">Green - Up to date</option>
                                        <option value="yellow">Yellow - Warning</option>
                                        <option value="red">Red - Locked out</option>
                                    </select>
                                </div>
                                <div class="col-md-4 form-group d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary btn-lg btn-block"><i class="fa fa-save"></i> Update</button>
                                </div>
                                <div class="col-md-4 form-group d-flex align-items-end">
                                    <button type="button" class="btn btn-danger btn-lg btn-block" id="removeHPBtn"><i class="fa fa-times"></i> Remove HP</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Payment History -->
                    <div class="card">
                        <div class="card-header border-0">
                            <h4 class="mb-0">Payment History</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped" id="paymentHistoryTable">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Type</th>
                                            <th>Amount</th>
                                            <th>Balance After</th>
                                        </tr>
                                    </thead>
                                    <tbody id="payment_history_body">
                                        <tr><td colspan="4" class="text-center text-muted">No payment history yet</td></tr>
                                    </tbody>
                                </table>
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
    var driverId = "{{ $id }}";
    var currencySymbol = '';

    database.collection('currency').where('enable', '==', true).get().then(function(snap) {
        if (snap.docs.length > 0) {
            currencySymbol = snap.docs[0].data().symbol;
            $(".currentCurrency").text(currencySymbol);
        }
    });

    // Load driver HP data
    database.collection('driver_users').doc(driverId).get().then(function(doc) {
        if (!doc.exists) { alert('Driver not found'); return; }
        var d = doc.data();

        $('.hp-driver-name').text(d.fullName || 'N/A');
        $('.hp-driver-phone').text(d.phoneNumber || '');
        $('.hp-driver-email').text(d.email || '-');
        if (d.profilePic) $('.hp-driver-photo').attr('src', d.profilePic);

        var totalCost = parseFloat(d.hpTotalCost || 0);
        var amountPaid = parseFloat(d.hpAmountPaid || 0);
        var balance = parseFloat(d.hpBalance || 0);
        var daily = parseFloat(d.hpDailyDeduction || 0);
        var status = d.hpStatus || 'green';

        $('.hp-total-cost').text(totalCost.toLocaleString());
        $('.hp-amount-paid').text(amountPaid.toLocaleString());
        $('.hp-balance').text(balance.toLocaleString());
        $('.hp-daily').text(daily.toLocaleString());

        if (d.hpStartDate) {
            $('.hp-start-date').text(new Date(d.hpStartDate.seconds * 1000).toLocaleDateString());
        }

        // Progress bar
        var progress = totalCost > 0 ? Math.round((amountPaid / totalCost) * 100) : 0;
        $('.hp-progress-bar').css('width', progress + '%').text(progress + '%');

        // Status badge
        var badgeHtml = '';
        if (status === 'green') badgeHtml = '<span class="badge" style="background:#1B5E20;color:#fff;padding:8px 20px;border-radius:20px;font-size:14px;"><i class="fa fa-check-circle"></i> Green - Payment Up to Date</span>';
        else if (status === 'yellow') badgeHtml = '<span class="badge" style="background:#FFC107;color:#000;padding:8px 20px;border-radius:20px;font-size:14px;"><i class="fa fa-exclamation-triangle"></i> Yellow - Payment Overdue</span>';
        else if (status === 'red') badgeHtml = '<span class="badge" style="background:#e6294b;color:#fff;padding:8px 20px;border-radius:20px;font-size:14px;"><i class="fa fa-times-circle"></i> Red - App Locked</span>';
        $('.hp-status-badge').html(badgeHtml);

        // Fill edit form
        $('#edit_total_cost').val(totalCost);
        $('#edit_amount_paid').val(amountPaid);
        $('#edit_daily_deduction').val(daily);
        $('#edit_hp_status').val(status);

        // Kill switch status
        if (d.appLocked) {
            $('.kill-switch-status').html('<span class="text-danger"><i class="fa fa-lock"></i> Driver is LOCKED</span>');
            $('#unlockBtn').removeClass('d-none');
        } else {
            $('.kill-switch-status').html('<span class="text-success"><i class="fa fa-unlock"></i> Driver is ACTIVE</span>');
            $('#lockBtn').removeClass('d-none');
        }
    });

    // Load payment history
    database.collection('driver_users').doc(driverId).collection('hp_payments').orderBy('date', 'desc').limit(50).get().then(function(snap) {
        if (snap.docs.length > 0) {
            var rows = '';
            snap.docs.forEach(function(doc) {
                var p = doc.data();
                rows += '<tr>';
                rows += '<td>' + (p.date ? new Date(p.date.seconds * 1000).toLocaleDateString() : '-') + '</td>';
                rows += '<td>' + (p.type || 'Deduction') + '</td>';
                rows += '<td>' + currencySymbol + parseFloat(p.amount || 0).toLocaleString() + '</td>';
                rows += '<td>' + currencySymbol + parseFloat(p.balanceAfter || 0).toLocaleString() + '</td>';
                rows += '</tr>';
            });
            $('#payment_history_body').html(rows);
        }
    });

    // Update HP
    $('#updateHPForm').on('submit', function(e) {
        e.preventDefault();
        var totalCost = parseFloat($('#edit_total_cost').val()) || 0;
        var amountPaid = parseFloat($('#edit_amount_paid').val()) || 0;
        database.collection('driver_users').doc(driverId).update({
            hpTotalCost: totalCost,
            hpAmountPaid: amountPaid,
            hpBalance: totalCost - amountPaid,
            hpDailyDeduction: parseFloat($('#edit_daily_deduction').val()) || 0,
            hpStatus: $('#edit_hp_status').val()
        }).then(function() {
            $('#update_success').removeClass('d-none').delay(3000).queue(function(n) { $(this).addClass('d-none'); n(); });
            setTimeout(function() { location.reload(); }, 1500);
        });
    });

    // Remove HP
    $('#removeHPBtn').on('click', function() {
        Swal.fire({
            title: 'Remove HP Plan?',
            text: 'This will remove the hire-purchase plan from this driver.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e6294b',
            confirmButtonText: 'Yes, Remove'
        }).then(function(result) {
            if (result.isConfirmed) {
                database.collection('driver_users').doc(driverId).update({
                    hpEnabled: false, hpTotalCost: 0, hpAmountPaid: 0,
                    hpBalance: 0, hpDailyDeduction: 0, hpStatus: '',
                    appLocked: false
                }).then(function() {
                    window.location.href = "{{ url('hire-purchase') }}";
                });
            }
        });
    });

    // Kill Switch buttons
    $('#lockBtn').on('click', function() {
        $.ajax({
            url: '/api/kill-switch/' + driverId + '/lock',
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: { reason: 'Manual lock from admin panel' },
            success: function() { Swal.fire('Locked!', 'Driver locked out.', 'success').then(function() { location.reload(); }); },
            error: function() { Swal.fire('Error', 'Failed to lock driver.', 'error'); }
        });
    });

    $('#unlockBtn').on('click', function() {
        $.ajax({
            url: '/api/kill-switch/' + driverId + '/unlock',
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function() { Swal.fire('Unlocked!', 'Driver unlocked.', 'success').then(function() { location.reload(); }); },
            error: function() { Swal.fire('Error', 'Failed to unlock driver.', 'error'); }
        });
    });
</script>
@endsection
