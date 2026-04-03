@extends('layouts.app')

@section('content')
<?php
$countries = file_get_contents(public_path('countriesdata.json'));
$countries = json_decode($countries);
$countries = (array) $countries;
$newcountries = [];
$newcountriesjs = [];
foreach ($countries as $keycountry => $valuecountry) {
    $newcountries[$valuecountry->phoneCode] = $valuecountry;
    $newcountriesjs[$valuecountry->phoneCode] = $valuecountry->code;
}
?>
    <div class="page-wrapper">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">{{trans('lang.user_profile')}}</h3>
            </div>

            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                  
                    <li class="breadcrumb-item active">{{trans('lang.user_edit')}}</li>
                </ol>
            </div>

        </div>

        <div class="profile-form">


            <div class="card-body">

                <div id="data-table_processing" class="dataTables_processing panel panel-default"
                     style="display: none;">{{trans('lang.processing')}}
                </div>

                @if (Session::has('message'))
                    <div class="error_top"><p>{{Session::get('message')}}</p></div>
                @endif

                <div class="column">
                    <form method="post" action="{{ route('users.profile.update',$user->id) }}">
                        @csrf

                        <div class="row restaurant_payout_create">
                            <div class="restaurant_payout_create-inner">
                                <fieldset>
                                    <legend>Profile Details</legend>
                                    <div class="form-group row">
                                        <label class="col-5 control-label">{{trans('lang.user_name')}}</label>
                                        <div class="col-7">
                                            <input type="text" class=" col-6 form-control" name="name"
                                                   value="<?php echo $user->name; ?>" id="name">
                                            <div class="form-text text-muted">
                                                {{ trans("lang.user_name_help") }}
                                            </div>
                                        </div>
                                    </div>
                                  
                                    <div class="form-group row width-50">
                                        <label class="col-5 control-label">{{trans('lang.user_email')}}</label>
                                        <div class="col-7">
                                            <input type="text" class=" col-6 form-control"
                                                   value="<?php echo $user->email; ?>" name="email" disabled>
                                            <div class="form-text text-muted">
                                                {{ trans("lang.user_email_help") }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row width-50"> 
                                        <label class="col-3 control-label">{{trans('lang.user_phone')}}</label>
                                        <div class="col-7">
                                        <div class="phone-box position-relative " id="phone-box">
                                                <select name="country" id="country_selector">
                                            @foreach($countries_data as $country)
                                                    <option value="{{$country->phoneCode}}">{{$country->countryName}}
                                                        (+{{$country->phoneCode}})
                                                    </option>
                                                @endforeach
                                                </select>
                                                <input type="text" class="form-control user_phone"  value="<?php echo $vendorUser->phone; ?>" disabled>
                                                <div id="error2" class="err"></div>
                                        </div>
                                        <div class="form-text text-muted">
                                            {{trans('lang.user_phone_help')}}
                                        </div>                  
                                    </div>
                                </fieldset>
                                <fieldset>
                                    <legend>{{trans('lang.bank_details')}}</legend>
                                    <div class="form-group row width-50">
                                        <label class="col-3 control-label">{{trans('lang.bank_name')}}<span></span></label>
                                        <div class="col-7">
                                            <input type="text" id='bank_name'  name="bank_name" class="form-control bank_name" required>
                                            <div class="form-text text-muted">
                                                {{ trans("lang.bank_name_help") }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row width-50">
                                        <label class="col-3 control-label">{{trans('lang.branch_name')}}<span></span></label>
                                        <div class="col-7">
                                            <input type="text" class="form-control branch_name" name="branch_name">
                                            <div class="form-text text-muted">{{trans('lang.branch_name_help')}}</div>
                                        </div>
                                    </div>
                                    <div class="form-group row width-50">
                                        <label class="col-3 control-label">{{trans('lang.holder_name')}}<span></span></label>
                                        <div class="col-7">
                                            <input type="text" class="form-control holder_name" name="holder_name">
                                            <div class="form-text text-muted">{{trans('lang.holder_name_help')}}</div>
                                        </div>
                                    </div>
                                    <div class="form-group row width-50">
                                        <label class="col-3 control-label">{{trans('lang.account_number')}}<span></span></label>
                                        <div class="col-7">
                                            <input type="text" class="form-control account_number"  name="account_number">
                                            <div class="form-text text-muted">{{trans('lang.account_number_help')}}</div>
                                        </div>
                                    </div>
                                    <div class="form-group row width-50">
                                        <label class="col-3 control-label">{{trans('lang.other_information')}}<span></span></label>
                                        <div class="col-7">
                                            <input type="text" class="form-control other_info" name="other_info">
                                            <div class="form-text text-muted">{{trans('lang.other_info_help')}}</div>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                </div>

                <div class="form-group col-12 text-center btm-btn">
                    <button type="submit" class="btn btn-primary  edit-setting-btn" id="edit-setting-btn"><i
                                class="fa fa-save"></i> {{ trans('lang.save')}}</button>
                    <a href="{!! route('dashboard') !!}" class="btn btn-default"><i
                                class="fa fa-undo"></i>{{ trans('lang.cancel')}}</a>
                </div>
                </form>

            </div>

        </div>

        @endsection

        @section('scripts')
            
        <script>
            var database=firebase.firestore();
            var ownerId = "{{$id}}";
            var newcountriesjs = '<?php echo json_encode($newcountriesjs); ?>';
            var newcountriesjs = JSON.parse(newcountriesjs);
            var ref=database.collection('owner_users').where("id","==",ownerId);
            var ownerRef = database.collection('owner_users')
            var bankRef=database.collection('bank_details').where("userId","==",ownerId);

            $(document).ready(async function() {
                $("#country_selector").select2({
                    templateResult: formatState,
                    templateSelection: formatState2,
                    placeholder: "Select Country",
                    allowClear: true
                });
                ref.get().then(async function(snapshots) {
                    var ownerUser=snapshots.docs[0].data();
                    var countryCode=ownerUser.countryCode.includes('+')? ownerUser.countryCode.slice(1):ownerUser.countryCode;
                    $("#country_selector").val(countryCode).trigger('change').prop("disabled", true);
                })
                bankRef.get().then(async function(snapshots) {
                    if(snapshots.docs.length>0) {
                        var bankData=snapshots.docs[0].data();
                        $('.bank_name').val(bankData.bankName);
                        $('.branch_name').val(bankData.branchName);
                        $('.holder_name').val(bankData.holderName);
                        $('.account_number').val(bankData.accountNumber);
                        $('.other_info').val(bankData.otherInformation);
                    }
                })
                $("#edit-setting-btn").click(async function (e) {
                    e.preventDefault(); // stop default submit for now

                    var bankName = $('.bank_name').val();
                    var branchName = $('.branch_name').val();
                    var holderName = $('.holder_name').val();
                    var accountNumber = $('.account_number').val();
                    var otherInfo = $('.other_info').val();
                    var fullName = $('#name').val();

                    await ownerRef.doc(ownerId).update({
                        fullName: fullName
                    });

                    const bankDocRef = database.collection('bank_details').doc(ownerId);

                    try {
                        const docSnap = await bankDocRef.get();

                        if (docSnap.exists) {
                            await bankDocRef.update({
                                bankName: bankName,
                                branchName: branchName,
                                holderName: holderName,
                                accountNumber: accountNumber,
                                otherInformation: otherInfo
                            });
                        } else {
                            await bankDocRef.set({
                                bankName: bankName,
                                branchName: branchName,
                                holderName: holderName,
                                accountNumber: accountNumber,
                                otherInformation: otherInfo,
                                userId: ownerId
                            });
                        }                        
                        $('<input>').attr({
                            type: 'hidden',
                            name: 'form_submitted',
                            value: '1'
                        }).appendTo('form');

                        $("form").off("submit").submit();


                    } catch (error) {
                        console.error("Error updating bank details:", error);
                        alert("Something went wrong while updating bank details.");
                    }
                });
            });
            function formatState(state) {
                if (!state.id) {
                    return state.text;
                }
                var baseUrl = "<?php echo URL::to('/'); ?>/flags/120/";
                var $state = $(
                    '<span><img src="' + baseUrl + '/' + newcountriesjs[state.element.value].toLowerCase() + '.png" class="img-flag" /> ' + state.text + '</span>'
                );
                return $state;
            }

            function formatState2(state) {
                if (!state.id) {
                    return state.text;
                }
                var baseUrl = "<?php echo URL::to('/'); ?>/flags/120/"
                var $state = $(
                    '<span><img class="img-flag" /> <span></span></span>'
                );
                $state.find("span").text(state.text);
                $state.find("img").attr("src", baseUrl + "/" + newcountriesjs[state.element.value].toLowerCase() + ".png");
                return $state;
            }
           

        </script>
        @endsection
