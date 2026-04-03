@extends('layouts.app')
@section('content')
    <div class="page-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Hire Purchase Management</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ trans('lang.dashboard') }}</a></li>
                    <li class="breadcrumb-item active">Hire Purchase</li>
                </ol>
            </div>
        </div>
        <div class="container-fluid">
            <!-- HP Summary Cards -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6">
                    <div class="card border-left-success">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="mr-3"><i class="mdi mdi-check-circle" style="font-size:40px;color:#1B5E20;"></i></div>
                                <div>
                                    <h6 class="text-muted mb-1">Green Status</h6>
                                    <h3 class="mb-0 hp-green-count">0</h3>
                                    <small class="text-muted">Payment up to date</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card border-left-warning">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="mr-3"><i class="mdi mdi-alert" style="font-size:40px;color:#FFC107;"></i></div>
                                <div>
                                    <h6 class="text-muted mb-1">Yellow Status</h6>
                                    <h3 class="mb-0 hp-yellow-count">0</h3>
                                    <small class="text-muted">Overdue 24hrs</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card border-left-danger">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="mr-3"><i class="mdi mdi-close-circle" style="font-size:40px;color:#e6294b;"></i></div>
                                <div>
                                    <h6 class="text-muted mb-1">Red Status</h6>
                                    <h3 class="mb-0 hp-red-count">0</h3>
                                    <small class="text-muted">Locked out (48hrs+)</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card" style="border-left: 4px solid #D4AF37;">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="mr-3"><i class="mdi mdi-cash-multiple" style="font-size:40px;color:#D4AF37;"></i></div>
                                <div>
                                    <h6 class="text-muted mb-1">Total HP Balance</h6>
                                    <h3 class="mb-0"><span class="currentCurrency"></span><span class="hp-total-balance">0</span></h3>
                                    <small class="text-muted">Outstanding</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- HP Drivers Table -->
            <div class="row">
                <div class="col-12">
                    <div class="card border">
                        <div class="card-header d-flex justify-content-between align-items-center border-0">
                            <div>
                                <h3 class="text-dark-2 mb-2 h4">Hire Purchase Drivers</h3>
                                <p class="mb-0 text-dark-2">Manage all drivers under hire-purchase agreements</p>
                            </div>
                            <a href="{{ url('hire-purchase/settings') }}" class="btn btn-primary btn-sm"><i class="fa fa-cog"></i> HP Settings</a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="hpTable" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Driver</th>
                                            <th>Phone</th>
                                            <th>Vehicle Cost</th>
                                            <th>Amount Paid</th>
                                            <th>Balance</th>
                                            <th>Daily Deduction</th>
                                            <th>Last Payment</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="hp_table_body">
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
    var currencySymbol = '';

    var refCurrency = database.collection('currency').where('enable', '==', true);
    refCurrency.get().then(async function(snapshots) {
        if (snapshots.docs.length > 0) {
            currencySymbol = snapshots.docs[0].data().symbol;
            $(".currentCurrency").text(currencySymbol);
        }
    });

    var greenCount = 0, yellowCount = 0, redCount = 0, totalBalance = 0;

    database.collection('driver_users').where('hpEnabled', '==', true).get().then(function(snapshots) {
        var tableBody = '';
        snapshots.docs.forEach(function(doc) {
            var data = doc.data();
            var id = doc.id;
            var name = data.fullName || 'N/A';
            var phone = data.phoneNumber || 'N/A';
            var totalCost = parseFloat(data.hpTotalCost || 0);
            var amountPaid = parseFloat(data.hpAmountPaid || 0);
            var balance = parseFloat(data.hpBalance || 0);
            var dailyDeduction = parseFloat(data.hpDailyDeduction || 0);
            var status = data.hpStatus || 'green';
            var lastPayment = data.hpLastPaymentDate ? new Date(data.hpLastPaymentDate.seconds * 1000).toLocaleDateString() : 'N/A';
            var profilePic = data.profilePic || "{{ asset('/images/default_user.png') }}";

            totalBalance += balance;
            if (status === 'green') greenCount++;
            else if (status === 'yellow') yellowCount++;
            else if (status === 'red') redCount++;

            var statusBadge = '';
            if (status === 'green') statusBadge = '<span class="badge" style="background:#1B5E20;color:#fff;padding:5px 12px;border-radius:20px;"><i class="fa fa-check-circle"></i> Green</span>';
            else if (status === 'yellow') statusBadge = '<span class="badge" style="background:#FFC107;color:#000;padding:5px 12px;border-radius:20px;"><i class="fa fa-exclamation-triangle"></i> Yellow</span>';
            else if (status === 'red') statusBadge = '<span class="badge" style="background:#e6294b;color:#fff;padding:5px 12px;border-radius:20px;"><i class="fa fa-times-circle"></i> Red</span>';

            tableBody += '<tr>';
            tableBody += '<td><div class="d-flex align-items-center"><img src="' + profilePic + '" style="width:35px;height:35px;border-radius:50%;object-fit:cover;margin-right:10px;"> ' + name + '</div></td>';
            tableBody += '<td>' + phone + '</td>';
            tableBody += '<td>' + currencySymbol + totalCost.toLocaleString() + '</td>';
            tableBody += '<td>' + currencySymbol + amountPaid.toLocaleString() + '</td>';
            tableBody += '<td><strong>' + currencySymbol + balance.toLocaleString() + '</strong></td>';
            tableBody += '<td>' + currencySymbol + dailyDeduction.toLocaleString() + '/day</td>';
            tableBody += '<td>' + lastPayment + '</td>';
            tableBody += '<td>' + statusBadge + '</td>';
            tableBody += '<td>';
            tableBody += '<a href="{{ url("hire-purchase/driver") }}/' + id + '" class="btn btn-info btn-sm" title="View Details"><i class="fa fa-eye"></i></a> ';
            if (status === 'red') {
                tableBody += '<button class="btn btn-danger btn-sm kill-switch-btn" data-id="' + id + '" data-name="' + name + '" title="Kill Switch Active"><i class="fa fa-power-off"></i></button>';
            } else {
                tableBody += '<button class="btn btn-sm btn-outline-secondary" disabled title="No action needed"><i class="fa fa-power-off"></i></button>';
            }
            tableBody += '</td>';
            tableBody += '</tr>';
        });

        $('#hp_table_body').html(tableBody);
        $('.hp-green-count').text(greenCount);
        $('.hp-yellow-count').text(yellowCount);
        $('.hp-red-count').text(redCount);
        $('.hp-total-balance').text(totalBalance.toLocaleString());

        $('#hpTable').DataTable({
            order: [[7, 'desc']],
            responsive: true,
            language: datatableLang
        });
    });

    $(document).on('click', '.kill-switch-btn', function() {
        var driverId = $(this).data('id');
        var driverName = $(this).data('name');
        Swal.fire({
            title: 'Activate Kill Switch?',
            html: 'This will <strong>lock out</strong> driver <strong>' + driverName + '</strong> from accepting new rides until their HP payment is cleared.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e6294b',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Lock Driver'
        }).then((result) => {
            if (result.isConfirmed) {
                database.collection('driver_users').doc(driverId).update({
                    isActive: false,
                    appLocked: true,
                    lockReason: 'HP payment overdue - Red status',
                    lockedAt: firebase.firestore.FieldValue.serverTimestamp()
                }).then(function() {
                    Swal.fire('Locked!', 'Driver has been locked out.', 'success').then(() => location.reload());
                }).catch(function(err) {
                    Swal.fire('Error', 'Failed to activate kill switch: ' + err.message, 'error');
                });
            }
        });
    });
</script>
@endsection
