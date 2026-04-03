@extends('layouts.app')
@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.driver_plural')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item"><a href="{!! route('drivers') !!}">{{trans('lang.driver_plural')}}</a>
                </li>
                <li class="breadcrumb-item active">{{trans('lang.driver_edit')}}</li>
            </ol>
        </div>
    </div>
    <div class="container-fluid">
        <div class="card  pb-4">
            <div class="card-body">
                <div class="error_top"></div>
                <div class="row restaurant_payout_create">
                    <div class="restaurant_payout_create-inner">
                    <input type="hidden" id="distanceType" />
                        <fieldset>
                            <legend>{{trans('lang.driver_details')}}</legend>
                            <div class="form-group row width-50">
                                <label class="col-3 control-label">{{trans('lang.user_name')}}<span
                                        class="required-field"></span></label>
                                <div class="col-7">
                                    <input type="text" class="form-control user_name">
                                    <div class="form-text text-muted">
                                        {{ trans("lang.user_name_help") }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row width-50">
                                <label class="col-3 control-label">{{trans('lang.email')}}<span
                                        class="required-field"></span></label>
                                <div class="col-7">
                                    <input type="text" class="form-control user_email">
                                    <div class="form-text text-muted">
                                        {{ trans("lang.user_email_help") }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row width-50" id="phone-box">
                                <label class="col-3 control-label">{{trans('lang.country_code')}}<span
                                        class="required-field"></span></label>
                                <div class="col-7">
                                    <select name="country" id="country_selector" class="form-control country_code">
                                        <option value="">{{ trans("lang.user_country_code_help") }}</option>
                                        @foreach($countries_data as $country)
                                            <option value="{{$country->code}}" data-phonecode="{{ $country->phoneCode }}" data-iso="{{$country->code}}">{{$country->countryName}}
                                                (+{{$country->phoneCode}})
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="form-text text-muted">
                                        {{ trans("lang.user_country_code_help") }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row width-50">
                                <label class="col-3 control-label">{{trans('lang.user_phone')}}<span
                                        class="required-field"></span></label>
                                <div class="col-7">
                                    <input type="text" class="form-control user_phone">
                                    <div class="form-text text-muted w-50">
                                        {{ trans("lang.user_phone_help") }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row width-50">
                                <label class="col-3 control-label">{{trans('lang.user_service')}}<span
                                        class="required-field"></span></label>
                                <div class="col-7">
                                    <select id='user_service' class="form-control" required>
                                        <option value="">{{trans('lang.user_service_help')}}</option>
                                    </select>
                                    <div class="form-text text-muted">
                                        {{ trans("lang.user_service_help") }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row width-50">
                                <label class="col-3 control-label">{{trans('lang.zone')}}<span
                                        class="required-field"></span></label>
                                <div class="col-7 multi-select-box">
                                    <select id='zone' class="form-control" multiple required></select>
                                </div>
                            </div>
                            <div class="form-group row width-100">
                                <div class="col-12">
                                    <h6>{{ trans("lang.know_your_cordinates") }} <a target="_blank"
                                            href="https://www.latlong.net/">{{
    trans("lang.latitude_and_longitude_finder") }}</a></h6>
                                </div>
                            </div>
                            <div class="form-group row width-50">
                                <label class="col-3 control-label">{{trans('lang.user_latitude')}}<span
                                        class="required-field"></span></label>
                                <div class="col-7">
                                    <input type="number" class="form-control user_latitude">
                                    <div class="form-text text-muted">{{trans('lang.user_latitude_help')}}</div>
                                </div>
                            </div>
                            <div class="form-group row width-50">
                                <label class="col-3 control-label">{{trans('lang.user_longitude')}}<span
                                        class="required-field"></span></label>
                                <div class="col-7">
                                    <input type="number" class="form-control user_longitude">
                                    <div class="form-text text-muted">{{trans('lang.user_longitude_help')}}</div>
                                </div>
                            </div>
                            <div class="form-group row width-50">
                                <label class="col-3 control-label">{{trans('lang.profile_image')}}</label>
                                <div class="col-7">
                                    <input type="file" onChange="handleFileSelect(event)" class="" id="userImage">
                                    <div class="form-text text-muted">{{trans('lang.profile_image_help')}}</div>
                                </div>
                                <div class="placeholder_img_thumb user_image"></div>
                                <div id="uploding_image"></div>
                            </div>
                            <div class="form-check width-100 doc-verification-div d-none">
                                <input type="checkbox" class="col-7 form-check-inline is_active" id="is_active">
                                <label class="col-3 control-label"
                                    for="is_active">{{trans('lang.document_verification')}}</label>
                            </div>
                        </fieldset>
                        <fieldset class="subscription-model-div d-none">
                            <legend>{{ trans('lang.subscription_model') }}</legend>
                            <div class="form-group row width-100">
                                <label class="col-4 control-label">{{ trans('lang.change_expiry_date') }}</label>
                                <div class="col-7">
                                    <input type="date" name="change_expiry_date" class="form-control"
                                        id="change_expiry_date" value="">
                                </div>
                            </div>
                            <input type="hidden" id="subscriptionPlanId">
                        </fieldset>
                        <fieldset>
                            <legend>{{trans('lang.vehicle_information')}}</legend>
                            <div class="form-group row width-50">
                                <label class="col-3 control-label">{{trans('lang.seats')}}<span
                                        class="required-field"></span></label>
                                <div class="col-7">
                                    <input type="text" class="form-control vehicle_seats">
                                    <div class="form-text text-muted">{{trans('lang.vehicle_seats_help')}}</div>
                                </div>
                            </div>
                            @php
                                $colorArray = [
                                    'Red',
                                    'Black',
                                    'White',
                                    'Blue',
                                    'Green',
                                    'Orange',
                                    'Silver',
                                    'Gray',
                                    'Yellow',
                                    'Brown',
                                    'Gold',
                                    'Beige',
                                    'Purple'
                                ];
                            @endphp
                            <div class="form-group row width-50">
                                <label class="col-3 control-label">{{trans('lang.vehicle_color')}}<span
                                        class="required-field"></span></label>
                                <div class="col-7">
                                    <select name="vehicle_color" id="colorPicker" class="form-control vehicle_color">
                                        @foreach($colorArray as $color)
                                            <option value="{{$color}}">{{$color}}</option>
                                        @endforeach
                                    </select>
                                    <div class="form-text text-muted">{{trans('lang.vehicle_color_help')}}</div>
                                </div>
                            </div>
                            <div class="form-group row width-50">
                                <label class="col-3 control-label">{{trans('lang.vehicle_number')}}<span
                                        class="required-field"></span></label>
                                <div class="col-7">
                                    <input type="text" class="form-control vehicle_number" name="vehicle_number">
                                    <div class="form-text text-muted">{{trans('lang.vehicle_number_help')}}</div>
                                </div>
                            </div>
                            <div id="zone-charges-wrapper"></div>
                        </fieldset>
                        <fieldset class="bank-detail-div d-none">
                            <legend>{{trans('lang.bank_details')}}</legend>
                            <div class="form-group row width-50">
                                <label class="col-3 control-label">{{trans('lang.bank_name')}}<span></span></label>
                                <div class="col-7">
                                    <input type="text" id='bank_name' class="form-control bank_name" required>
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
                                    <input type="text" class="form-control account_number">
                                    <div class="form-text text-muted">{{trans('lang.account_number_help')}}</div>
                                </div>
                            </div>
                            <div class="form-group row width-50">
                                <label class="col-3 control-label">{{trans('lang.other_info')}}<span></span></label>
                                <div class="col-7">
                                    <input type="text" class="form-control other_info" name="other_info">
                                    <div class="form-text text-muted">{{trans('lang.other_info_help')}}</div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
                <div class="form-group col-12 text-center btm-btn">
                    <button type="button" class="btn btn-primary edit-form-btn"><i class="fa fa-save"></i> {{
    trans('lang.save')}}
                    </button>
                    <a href="javascript:void(0)" class="btn btn-default cancel-btn"><i class="fa fa-undo"></i>{{
    trans('lang.cancel')}}</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
    var database=firebase.firestore();
    var photo="";
    var fileName="";
    var userImageFile="";
    var service_list=[];
    var is_active_deactivate=false;
    var id="{{$id}}";
    var placeholderImage="{{ asset('/images/default_user.png') }}";
    var ref=database.collection('driver_users').where("id","==",id);
    var bankRef=database.collection('bank_details').where("userId","==",id);
    var storageRef=firebase.storage().ref('images');
    var storage=firebase.storage();
    var setLanguageCode=getCookie('setLanguage');
    var defaultLanguageCode=getCookie('defaultLanguage');
    var service_ac_charges = null;
    var service_nonac_charges = null;
    var service_km_charges = null;
    var data_is_acnonac = false;
    var listUrl = "{{route('fleet.driver.list')}}";

    var stringToColour=function(str) {
        var hash=0;
        for(var i=0;i<str.length;i++) {
            hash=str.charCodeAt(i)+((hash<<5)-hash);
        }
        var colour='#';
        for(var i=0;i<3;i++) {
            var value=(hash>>(i*8))&0xFF;
            colour+=('00'+value.toString(16)).substr(-2);
        }
        return colour;
    };
    async function storeImageData() {
        var newPhoto='';
        try {
            if(userImageFile!=""&&photo!=userImageFile) {
                var userOldImageUrlRef=await storage.refFromURL(userImageFile);
                imageBucket=userOldImageUrlRef.bucket;
                var envBucket="<?php echo env('FIREBASE_STORAGE_BUCKET'); ?>";
                if(imageBucket==envBucket) {
                    await userOldImageUrlRef.delete().then(() => {
                        console.log("Old file deleted!")
                    }).catch((error) => {
                        console.log("ERR File delete ===",error);
                    });
                } else {
                    console.log('Bucket not matched');
                }
            }
            if(photo!=userImageFile) {
                photo=photo.replace(/^data:image\/[a-z]+;base64,/,"")
                var uploadTask=await storageRef.child(fileName).putString(photo,'base64',{contentType: 'image/jpg'});
                var downloadURL=await uploadTask.ref.getDownloadURL();
                newPhoto=downloadURL;
                photo=downloadURL;
            } else {
                newPhoto=photo;
            }
        } catch(error) {
            console.log("ERR ===",error);
        }
        return newPhoto;
    }
    $('#user_service').on('change',function(){
        data_is_acnonac = $('#user_service option:selected').attr('data-is-acnonac');
        service_ac_charges = $("#user_service option:selected").attr("data-ac-charge");
        service_nonac_charges = $("#user_service option:selected").attr("data-non-ac-charge");
        service_km_charges = $("#user_service option:selected").attr("data-km-charge");
        let serviceId = $('#user_service option:selected').val();
        let fullService = service_list.find(s => s.id === serviceId);
        window.selectedService = fullService;
        $("#zone").trigger('change');
    });
    $(document).ready(async function() {
        $('.driver_menu').addClass('active');
        jQuery("#country_selector").select2({
            placeholder: "Select Country",
            allowClear: true
        });
        jQuery("#zone").select2({
            placeholder: "Select Zone",
            allowClear: true
        });
        jQuery("#overlay").show();
        await database.collection('service').where('enable','==',true).get().then(async function(snapshots) {
            snapshots.docs.forEach((listval) => {
                var data=listval.data();
                service_list.push(data);
                var title='';
                if(Array.isArray(data.title)) {
                    var foundItem=data.title.find(item => item.type===setLanguageCode);
                    if(foundItem&&foundItem.title!='') {
                        title=foundItem.title;
                    } else {
                        var foundItem=data.title.find(item => item.type===defaultLanguageCode);
                        if(foundItem&&foundItem.title!='') {
                            title=foundItem.title;
                        } else {
                            var foundItem=data.title.find(item => item.type==='en');
                            title=foundItem.title;
                        }
                    }
                }
                $('#user_service').append($("<option></option>")
                    .attr("value",data.id)
                    .attr("data-title",title)
                    .attr("data-is-acnonac",data.prices[0].isAcNonAc)
                    .attr("data-ac-charge",(data.prices[0].acCharge ? data.prices[0].acCharge : ''))
                    .attr("data-non-ac-charge",(data.prices[0].nonAcCharge ? data.prices[0].nonAcCharge : ''))
                    .attr('data-km-charge',data.prices[0].kmCharge)
                    .text(title));
            });
        });
     
        database.collection('zone').where('publish','==',true).get().then(async function(snapshots) {
            snapshots.docs.forEach((listval) => {
                var data=listval.data();
                var name='';
                if(Array.isArray(data.name)) {
                    var foundItem=data.name.find(item => item.type===setLanguageCode);
                    if(foundItem&&foundItem.name!='') {
                        name=foundItem.name;
                    } else {
                        var foundItem=data.name.find(item => item.type===defaultLanguageCode);
                        if(foundItem&&foundItem.name!='') {
                            name=foundItem.name;
                        } else {
                            var foundItem=data.name.find(item => item.type==='en');
                            name=foundItem.name;
                        }
                    }
                }
                $('#zone').append($("<option></option>")
                    .attr("value",data.id)
                    .attr("data-name",data.name)
                    .text(name));
            })
        });
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
        ref.get().then(async function(snapshots) {
            var driverUser=snapshots.docs[0].data();
            $(".user_name").val(driverUser.fullName);
            $(".user_email").val(shortEmail(driverUser.email));
            if (driverUser.countryISOCode) {
                const isoCode = driverUser.countryISOCode.toUpperCase();
                $("#country_selector").val(isoCode).trigger('change');
            } else if (driverUser.countryCode) {
                let phoneCode = driverUser.countryCode;
                if (phoneCode.startsWith('+')) phoneCode = phoneCode.substring(1);
                let $option = $("#country_selector option").filter(function(){
                    return $(this).data('phonecode') == phoneCode;
                });
                if ($option.length) {
                    $("#country_selector").val($option.val()).trigger('change');
                }
            }

            $(".user_phone").val(EditPhoneNumber(driverUser.phoneNumber));
            $("#user_service option[value='"+driverUser.serviceId+"']").attr("selected","selected").trigger('change');
            $(".user_latitude").val(driverUser.location&&driverUser.location.latitude? driverUser.location.latitude:'');
            $(".user_longitude").val(driverUser.location&&driverUser.location.longitude? driverUser.location.longitude:'');
            if(driverUser.hasOwnProperty('ownerId')&&driverUser.ownerId==null){
                $('.doc-verification-div').removeClass('d-none');
                $('.subscription-model-div').removeClass('d-none');
                $('.bank-detail-div').removeClass('d-none');
                listUrl = "{{route('drivers')}}";
            }
            
            if(driverUser.hasOwnProperty('zoneIds')&&driverUser.zoneIds!=null&&driverUser.zoneIds!=''&&driverUser.zoneIds.length>0) {
                $("#zone").val(driverUser.zoneIds).trigger('change');
                 setTimeout(() => {
                    if(driverUser.vehicleInformation && driverUser.vehicleInformation.rates){
                        driverUser.vehicleInformation.rates.forEach(rate => {
                            let $zoneBox = $("#zone-charges-wrapper .zone-charge-box")
                                            .find("input[data-zone='"+rate.zoneId+"']")
                                            .closest(".zone-charge-box");
                            if($zoneBox.length){
                                if(data_is_acnonac && data_is_acnonac == "true"){
                                    $zoneBox.find(".ac_rate").val(rate.acPerKmRate || "");
                                    $zoneBox.find(".nonac_rate").val(rate.nonAcPerKmRate || "");
                                }else{
                                    $zoneBox.find(".km_rate").val(rate.perKmRate || "");
                                }
                            }
                        });
                    }
                }, 300); 
            }
            
            if(driverUser.documentVerification) {
                $("#is_active").prop("checked",true);
            }
            if(driverUser.vehicleInformation) {
                $(".vehicle_seats").val(driverUser.vehicleInformation.seats? driverUser.vehicleInformation.seats:'');
                if(driverUser.vehicleInformation.vehicleColor) {
                    $(".vehicle_color option[value='"+driverUser.vehicleInformation.vehicleColor+"']").attr("selected","selected");
                }
                $(".vehicle_number").val(driverUser.vehicleInformation.vehicleNumber? driverUser.vehicleInformation.vehicleNumber:'');
                if(data_is_acnonac && data_is_acnonac =="true"){
                    if(driverUser.vehicleInformation.acPerKmRate){
                        $(".ac_charges").val(driverUser.vehicleInformation.acPerKmRate);
                    }
                    if(driverUser.vehicleInformation.nonAcPerKmRate){
                        $(".nonac_charges").val(driverUser.vehicleInformation.nonAcPerKmRate);
                    }
                }else{
                    $(".km_charges").val(driverUser.vehicleInformation.perKmRate);
                }
            }
             if(driverUser.hasOwnProperty('subscriptionExpiryDate') && driverUser.subscriptionExpiryDate!=null) {
                const expiresAt=new Date(driverUser.subscriptionExpiryDate.toDate());
                const [year,month,day]=expiresAt.toISOString().slice(0,10).split("-");
                const formattedDate=`${year}-${month}-${day}`;
                $('#change_expiry_date').val(formattedDate);
            }
            if(driverUser.hasOwnProperty('subscriptionPlanId') && driverUser.subscriptionPlanId!=null) {
                $('#subscriptionPlanId').val(driverUser.subscriptionPlanId);
            }
            if(driverUser.profilePic!=''&&driverUser.profilePic!=null) {
                $(".user_image").append('<span class="image-item"><span class="remove-btn"><i class="fa fa-remove"></i></span><img class="rounded" style="width:50px" src="'+driverUser.profilePic+'" alt="image"></span>');
                photo=driverUser.profilePic;
                userImageFile=driverUser.profilePic;
            } else {
                photo="";
                $(".user_image").append('<span class="image-item"><span class="remove-btn"><i class="fa fa-remove"></i></span><img class="rounded" style="width:50px" src="'+placeholderImage+'" alt="image"></span>');
            }
            jQuery("#overlay").hide();
        })
        $(".edit-form-btn").click(async function() {
            var type=$('#distanceType').val();
            var userName=$(".user_name").val();
            var email=$(".user_email").val();
            var selectedOption = $("#country_selector option:selected");
            var countryCode = selectedOption.data('phonecode');  
            var countryIsoCode = selectedOption.val();  
            var countryCode = countryCode ?  + countryCode : '';
            var userPhone=$(".user_phone").val();
            var userService=$("#user_service :selected").val();
            var userServiceName=$("#user_service :selected").data('title');
            var userLatitude=parseFloat($(".user_latitude").val());
            var userLongitude=parseFloat($(".user_longitude").val());
            var active=$(".is_active").is(":checked");
            var bankName=$('.bank_name').val();
            var branchName=$('.branch_name').val();
            var holderName=$('.holder_name').val();
            var accountNumber=$('.account_number').val();
            var otherInfo=$('.other_info').val();
            
            var zoneIds=$('#zone option:selected').toArray().map(item => item.value);
            let rates = [];
            $("#zone-charges-wrapper .zone-charge-box").each(function(){
                let zoneId = $(this).find("input").first().data("zone");
                let acPerKmRate = $(this).find(".ac_rate").val() || "";
                let nonAcPerKmRate = $(this).find(".nonac_rate").val() || "";
                let perKmRate = $(this).find(".km_rate").val() || "";
                rates.push({
                    zoneId: zoneId,
                    acPerKmRate: acPerKmRate,
                    nonAcPerKmRate: nonAcPerKmRate,
                    perKmRate: perKmRate
                });
            });
            let isValid = true;

            $("#zone-charges-wrapper .zone-charge-box").each(function(){
                let zoneId = $(this).find("input").first().data("zone");
                let acPerKmRate   = $(this).find(".ac_rate").val() || "";
                let nonAcPerKmRate = $(this).find(".nonac_rate").val() || "";
                let perKmRate     = $(this).find(".km_rate").val() || "";

                if(data_is_acnonac && data_is_acnonac == "true"){
                   
                    if(acPerKmRate === '' || parseFloat(acPerKmRate) <= 0){
                        $(".error_top").show().html("<p>{{ trans('lang.please_enter_valid') }} {{ trans('lang.ac_charges') }}</p>");
                        isValid = false;
                        return false; 
                    }
                    if(service_ac_charges && parseFloat(acPerKmRate) > parseFloat(service_ac_charges)){
                        $(".error_top").show().html("<p>{{ trans('lang.please_enter_valid_ac_charges') }} (max " + service_ac_charges + ")</p>");
                        isValid = false;
                        return false;
                    }
                 
                    if(nonAcPerKmRate === '' || parseFloat(nonAcPerKmRate) <= 0){
                        $(".error_top").show().html("<p>{{ trans('lang.please_enter_valid') }} {{ trans('lang.nonac_charges') }}</p>");
                        isValid = false;
                        return false;
                    }
                    if(service_nonac_charges && parseFloat(nonAcPerKmRate) > parseFloat(service_nonac_charges)){
                        $(".error_top").show().html("<p>{{ trans('lang.please_enter_valid_nonac_charges') }} (max " + service_nonac_charges + ")</p>");
                        isValid = false;
                        return false;
                    }

                }else{
                  
                    if(perKmRate === '' || parseFloat(perKmRate) <= 0){
                        $(".error_top").show().html("<p>{{ trans('lang.please_enter_valid') }} {{ trans('lang.per') }} "+type+" {{ trans('lang.rate') }}</p>");
                        isValid = false;
                        return false;
                    }
                    if(service_km_charges && parseFloat(perKmRate) > parseFloat(service_km_charges)){
                        $(".error_top").show().html("<p>{{ trans('lang.please_enter_valid_km_charges') }} (max " + service_km_charges + ")</p>");
                        isValid = false;
                        return false;
                    }
                }
            });

            if(!isValid){
                window.scrollTo(0,0);
                jQuery("#overlay").hide();
                return;
            }

            is_active_deactivate=false;
            if($("#is_active").is(':checked')) {
                is_active_deactivate=true;
            }
            var vehicleSeats=$(".vehicle_seats").val();
            var vehicleColor=$(".vehicle_color :selected").val();
            var vehicleNumber=$(".vehicle_number").val();
            var subscriptionPlanId=$('#subscriptionPlanId').val();
            var change_expiry_date=$('#change_expiry_date').val();
             if(change_expiry_date!=''&&change_expiry_date!=null) {
                var subscriptionPlanExpiryDate=firebase.firestore.Timestamp.fromDate(new Date(
                    change_expiry_date));
            } else {
                var subscriptionPlanExpiryDate=null;           
            }
            if(userName=='') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.user_name_help')}}</p>");
                window.scrollTo(0,0);
            } else if(email=='') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.user_email_help')}}</p>");
                window.scrollTo(0,0);
            } else if(countryCode=='') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.user_country_code_help')}}</p>");
                window.scrollTo(0,0);
            } else if(userPhone=='') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.user_phone_help')}}</p>");
                window.scrollTo(0,0);
            } else if(userService=='') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.user_service_help')}}</p>");
                window.scrollTo(0,0);
            } else if(zoneIds.length==0) {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.select_zone_help')}}</p>");
                window.scrollTo(0,0);
            } else if(isNaN(userLatitude)||userLatitude=='') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.user_latitude_help')}}</p>");
                window.scrollTo(0,0);
            } else if(isNaN(userLongitude)||userLongitude=='') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.user_longitude_help')}}</p>");
                window.scrollTo(0,0);
            } else if(vehicleSeats=='') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.vehicle_seats_help')}}</p>");
                window.scrollTo(0,0);
            } else if(vehicleColor=='') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.vehicle_color_help')}}</p>");
                window.scrollTo(0,0);
            } else if(vehicleNumber=='') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.vehicle_number_help')}}</p>");
                window.scrollTo(0,0);
            }else {
                jQuery("#overlay").show();
                storeImageData().then(IMG => {
                    if(subscriptionPlanId!='' && subscriptionPlanId!=null){
                        updateData={
                            'documentVerification': is_active_deactivate,
                            'location.longitude': userLongitude,
                            'location.latitude': userLatitude,
                            'profilePic': IMG,
                            'email': email,
                            'serviceId': userService,
                            'serviceName': window.selectedService ? window.selectedService.title : null,
                            'countryCode': '+'+countryCode,
                            'countryISOCode': countryIsoCode,
                            'fullName': userName,
                            'vehicleInformation.vehicleColor': vehicleColor,
                            'vehicleInformation.vehicleNumber': vehicleNumber,
                            'vehicleInformation.seats': vehicleSeats,
                            'vehicleInformation.rates': rates,
                            'phoneNumber': userPhone,
                            'zoneIds': zoneIds,
                            'subscriptionExpiryDate': subscriptionPlanExpiryDate
                        }
                    } else {
                        updateData={
                            'documentVerification': is_active_deactivate,
                            'location.longitude': userLongitude,
                            'location.latitude': userLatitude,
                            'profilePic': IMG,
                            'email': email,
                            'serviceId': userService,
                            'serviceName': window.selectedService ? window.selectedService.title : null,
                            'countryCode': '+'+countryCode,
                            'countryISOCode': countryIsoCode,
                            'fullName': userName,
                            'vehicleInformation.vehicleColor': vehicleColor,
                            'vehicleInformation.vehicleNumber': vehicleNumber,
                            'vehicleInformation.seats': vehicleSeats,
                            'vehicleInformation.rates': rates,
                            'phoneNumber': userPhone,
                            'zoneIds': zoneIds
                        }
                    }
                    database.collection('driver_users').doc(id).update(updateData).then(function(result) {
                        bankRef.get().then(async function(snapshots) {
                            if(snapshots.docs.length>0) {
                                database.collection('bank_details').doc(id).update({
                                    'bankName': bankName,
                                    'branchName': branchName,
                                    'holderName': holderName,
                                    'accountNumber': accountNumber,
                                    'holderName': holderName,
                                    'otherInformation': otherInfo
                                }).then(function(result) {
                                    jQuery("#overlay").hide();
                                    window.location.href='{{ route("drivers") }}';
                                })
                            } else {
                                database.collection('bank_details').doc(id).set({
                                    'bankName': bankName,
                                    'branchName': branchName,
                                    'holderName': holderName,
                                    'accountNumber': accountNumber,
                                    'holderName': holderName,
                                    'otherInformation': otherInfo,
                                    'userId': id
                                }).then(function(result) {
                                    jQuery("#overlay").hide();
                                    window.location.href='{{ route("drivers") }}';
                                })
                            }
                        })
                    }).catch(err => {
                        jQuery("#overlay").hide();
                        $(".error_top").show();
                        $(".error_top").html("");
                        $(".error_top").append("<p>"+err+"</p>");
                        window.scrollTo(0,0);
                    });
                }).catch(err => {
                    jQuery("#overlay").hide();
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>"+err+"</p>");
                    window.scrollTo(0,0);
                });
            }
        })
    });

    function handleFileSelect(evt) {
        var f=evt.target.files[0];
        var reader=new FileReader();
        reader.onload=(function(theFile) {
            return function(e) {
                var filePayload=e.target.result;
                var val=f.name;
                var ext=val.split('.')[1];
                var docName=val.split('fakepath')[1];
                var filename=(f.name).replace(/C:\\fakepath\\/i,'')
                var timestamp=Number(new Date());
                var filename=filename.split('.')[0]+"_"+timestamp+'.'+ext;
                photo=filePayload;
                fileName=filename;
                $(".user_image").empty();
                $(".user_image").append('<span class="image-item"><span class="remove-btn"><i class="fa fa-remove"></i></span><img class="rounded" style="width:50px" src="'+filePayload+'" alt="image"></span>');
            };
        })(f);
        reader.readAsDataURL(f);
    }

    $(document).on('click','.remove-btn',function() {
        $(".user_image").empty();
        photo='';
    });

    $('#zone').on('change', function(){
        renderZoneCharges();
    });
    
    function renderZoneCharges() {
        var distanceType = $('#distanceType').val();
        $("#zone-charges-wrapper").empty();
        let selectedZones = $('#zone').val() || [];

        selectedZones.forEach(zoneId => {
            let zoneName = $("#zone option[value='"+zoneId+"']").text();

            let html = `<div class="zone-charge-box border m-3 p-3 mb-2">
                            <h6><strong>${zoneName}</strong></h6>`;

            if(data_is_acnonac && data_is_acnonac == "true"){
                html += `
                    <div class="form-group">
                        <label>{{ trans('lang.ac_charges') }} {{ trans('lang.per') }} ${distanceType} {{ trans('lang.rate') }}</label>
                        <input type="number" class="form-control ac_rate" data-zone="${zoneId}" min="0">
                    </div>
                    <div class="form-group">
                        <label>{{ trans('lang.nonac_charges') }} {{ trans('lang.per') }} ${distanceType} {{ trans('lang.rate') }}</label>
                        <input type="number" class="form-control nonac_rate" data-zone="${zoneId}" min="0">
                    </div>`;
            } else {
                html += `
                    <div class="form-group">
                        <label>{{ trans('lang.per') }} ${distanceType} {{ trans('lang.rate') }} </label>
                        <input type="number" class="form-control km_rate" data-zone="${zoneId}" min="0">
                    </div>`;
            }

            html += `</div>`;
            $("#zone-charges-wrapper").append(html);
        });
    }
    $('.cancel-btn').on('click', function() {           
        window.location.href = listUrl;
    });

</script>

@endsection