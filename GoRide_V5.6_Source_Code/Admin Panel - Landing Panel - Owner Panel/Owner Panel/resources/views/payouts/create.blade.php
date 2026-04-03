@extends('layouts.app')
@section('content')
<?php if ($id == 'create') {
    $id = '';
} ?>
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.payout_plural')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item"><a
                        href="{!! route('payout') !!}">{{trans('lang.payout_table')}}</a>
                </li>
                <li class="breadcrumb-item">{{trans('lang.owner_payout_create')}}</li>
            </ol>
        </div>
    </div>
    <div class="card-body">
        <div class="error_top"></div>
        <div class="row restaurant_payout_create">
            <div class="restaurant_payout_create-inner">
                <fieldset>
                    <legend>{{trans('lang.owner_payout_create')}}</legend>
                    
                    <div class="form-group row width-100">
                        <label class="col-3 control-label">{{trans('lang.amount')}}</label>
                        <div class="col-7">
                            <input type="number" class="form-control payout_amount">
                            <div class="form-text text-muted" min="0">
                                {{ trans("lang.insert_amount") }}
                            </div>
                        </div>
                    </div>
                    <div class="form-group row width-100">
                        <label class="col-3 control-label">{{ trans('lang.note')}}</label>
                        <div class="col-7">
                            <textarea type="text" rows="8" class="form-control payout_note"></textarea>
                        </div>
                    </div>
                  
                </fieldset>
            </div>
        </div>
    </div>
    <div class="form-group col-12 text-center btm-btn">
        <button type="button" class="btn btn-primary save_vendor_payout_btn"><i class="fa fa-save"></i>
            {{trans('lang.save')}}
        </button>
        <a href="{!! route('payout') !!}" class="btn btn-default"><i
                class="fa fa-undo"></i>{{trans('lang.cancel')}}</a>
    </div>
</div>
</div>
</div>
@endsection
@section('scripts')
<script>
    var database = firebase.firestore();
    var vendorUserId = "<?php echo $id; ?>";
   
    var withdrawMethodRef = database.collection('withdrawal_history').where('userId', '==', vendorUserId);
   
    var currentCurrency = '';
    var currencyAtRight = false;
    var decimal_degits = 0;
    var refCurrency = database.collection('currency').where('enable', '==', true);   
    refCurrency.get().then(async function (snapshots) {
        var currencyData = snapshots.docs[0].data();
        currentCurrency = currencyData.symbol;
        currencyAtRight = currencyData.symbolAtRight;
        currencyAtRight = currencyData.symbolAtRight;
        if (currencyData.decimalDigits) {
            decimal_degits = currencyData.decimalDigits;
        }
    });
    var bankDetail = database.collection('bank_details').where('userId', '==', vendorUserId);

    async function remainingPrice(vendorID) {
        var remaining = 0;
        await database.collection('owner_users').where("id", "==", vendorID).get().then(async function (snapshotss) {
            if (snapshotss.docs.length) {
                userdata = snapshotss.docs[0].data();
                if (isNaN(userdata.walletAmount) || userdata.walletAmount == undefined) {
                    remaining = 0;
                } else {
                    remaining = userdata.walletAmount;
                }
            }
        });
        return remaining;
    }
    $(document).ready(function () {
        $("#data-table_processing").show();
       
        database.collection('owner_users').get().then(async function (snapshots) {
            snapshots.docs.forEach((listval) => {
                var data = listval.data();
                $('#select_vendor').append($("<option></option>")
                    .attr("value", data.id)
                    .text(data.title));
            })
        });
        $("#data-table_processing").hide();
    });
    var userName = '';
    var userContact = '';
    var payoutId = database.collection('tmp').doc().id;
    $(".save_vendor_payout_btn").click(async function () {
        jQuery(".error_top").hide();
        const bankSnap = await bankDetail.get();
        if (bankSnap.empty) {
            $(".error_top").show();
            $(window).scrollTop(0);
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.add_bank_detail_first')}}</p>");
            return; 
        }
        var vendorId = '';
        var vendorEmail = await getVendorEmail(vendorUserId);
        getVendorId(vendorUserId).then(data => {
            vendorId = data;
            var remaining = 0;
            remainingPrice(vendorId).then(data => {
                var remaining = data;
                var amount = parseFloat($(".payout_amount").val());
                var note = $(".payout_note").val();
                var date = new Date(Date.now());
                if (isNaN(amount) || amount == " " || amount == null) {
                    $(".error_top").show();
                    $(window).scrollTop(0);
                    $(".error_top").html("");
                    $(".error_top").append("<p>{{trans('lang.vendor_insufficient_payment_error')}}</p>");
                }else if (amount > remaining) {
                    $(".error_top").show();
                    $(window).scrollTop(0);
                    $(".error_top").html("");
                    $(".error_top").append("<p>{{trans('lang.insufficient_payment_error')}}</p>");
                } else {
                    database.collection('withdrawal_history').doc(payoutId).set({
                        'userId': vendorId,
                        'amount': amount.toString(),
                        'note': note,
                        'adminNote': null,
                        'id': payoutId,
                        'paymentStatus': 'pending',
                        'paymentDate': null,
                        'createdDate':date,
                    }).then(async function () {
                        if (vendorId != '' && (amount != '' || amount != NaN)) {
                            if (remaining > 0) {
                                price = remaining - amount;
                            }else {
                                price = amount;
                            }
                            database.collection('owner_users').doc(vendorUserId).update({ 'walletAmount': price.toString() });
                        }
                        if (currencyAtRight) {
                            amount = parseInt(amount).toFixed(decimal_degits) + "" + currentCurrency;
                        } else {
                            amount = currentCurrency + "" + parseInt(amount).toFixed(decimal_degits);
                        }
                       
                        window.location.href = "{{route('payout')}}";
                        
                    })
                }
            });
        });
    });
    async function getVendorId(vendorUser) {
        var vendorId = '';
        await database.collection('owner_users').where('id', "==", vendorUser).get().then(async function (vendorSnapshots) {
            var vendorData = vendorSnapshots.docs[0].data();
            vendorId = vendorData.id;
        });
        return vendorId;
    }
    async function getVendorEmail(vendorUser) {
        var userEmail = '';
        await database.collection('owner_users').where('id', "==", vendorUser).get().then(async function (vendorSnapshots) {
            if (vendorSnapshots.docs[0]) {
                var vendorData = vendorSnapshots.docs[0].data();
                userEmail = vendorData.email;
                userName = vendorData.firstName + " " + vendorData.lastName;
                userContact = vendorData.phoneNumber;
            }
        });
        return userEmail;
    }
   
</script>
@endsection