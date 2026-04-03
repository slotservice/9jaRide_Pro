@extends('layouts.app')
@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.owner_plural')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item"><a href="{!! route('owners') !!}">{{trans('lang.owner_plural')}}</a>
                </li>
                <li class="breadcrumb-item active">{{trans('lang.owner_edit')}}</li>
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
                            <legend>{{trans('lang.owner_details')}}</legend>
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
                            <div class="form-check width-100">
                                <input type="checkbox" class="col-7 form-check-inline is_active" id="is_active">
                                <label class="col-3 control-label"
                                    for="is_active">{{trans('lang.document_verification')}}</label>
                            </div>
                        </fieldset>
                        <fieldset>
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
                    <a href="{!! route('owners') !!}" class="btn btn-default"><i class="fa fa-undo"></i>{{
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
    var ref=database.collection('owner_users').where("id","==",id);
    var bankRef=database.collection('bank_details').where("userId","==",id);
    var storageRef=firebase.storage().ref('images');
    var storage=firebase.storage();
    var setLanguageCode=getCookie('setLanguage');
    var defaultLanguageCode=getCookie('defaultLanguage');
    
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
    
    $(document).ready(async function() {
        $('.owner_menu').addClass('active');
        jQuery("#country_selector").select2({
            placeholder: "Select Country",
            allowClear: true
        });
       
        jQuery("#overlay").show();        
        
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
            var ownerUser=snapshots.docs[0].data();
            $(".user_name").val(ownerUser.fullName);
            $(".user_email").val(shortEmail(ownerUser.email));
            if (ownerUser.countryISOCode) {
                    const isoCode = ownerUser.countryISOCode.toUpperCase();
                $("#country_selector").val(isoCode).trigger('change');
            } else if (ownerUser.countryCode) {
                let phoneCode = ownerUser.countryCode;
                if (phoneCode.startsWith('+')) phoneCode = phoneCode.substring(1);
                let $option = $("#country_selector option").filter(function(){
                    return $(this).data('phonecode') == phoneCode;
                });
                if ($option.length) {
                    $("#country_selector").val($option.val()).trigger('change');
                }
            }
            $(".user_phone").val(EditPhoneNumber(ownerUser.phoneNumber));
            
            $(".user_latitude").val(ownerUser.location&&ownerUser.location.latitude? ownerUser.location.latitude:'');
            $(".user_longitude").val(ownerUser.location&&ownerUser.location.longitude? ownerUser.location.longitude:'');
         
            if(ownerUser.documentVerification) {
                $("#is_active").prop("checked",true);
            }
             if(ownerUser.hasOwnProperty('subscriptionExpiryDate') && ownerUser.subscriptionExpiryDate!=null) {
                const expiresAt=new Date(ownerUser.subscriptionExpiryDate.toDate());
                const [year,month,day]=expiresAt.toISOString().slice(0,10).split("-");
                const formattedDate=`${year}-${month}-${day}`;
                $('#change_expiry_date').val(formattedDate);
            }
            if(ownerUser.hasOwnProperty('subscriptionPlanId') && ownerUser.subscriptionPlanId!=null) {
                $('#subscriptionPlanId').val(ownerUser.subscriptionPlanId);
            }
            if(ownerUser.profilePic!=''&&ownerUser.profilePic!=null) {
                $(".user_image").append('<span class="image-item"><span class="remove-btn"><i class="fa fa-remove"></i></span><img class="rounded" style="width:50px" src="'+ownerUser.profilePic+'" alt="image"></span>');
                photo=ownerUser.profilePic;
                userImageFile=ownerUser.profilePic;
            } else {
                photo="";
                $(".user_image").append('<span class="image-item"><span class="remove-btn"><i class="fa fa-remove"></i></span><img class="rounded" style="width:50px" src="'+placeholderImage+'" alt="image"></span>');
            }
            jQuery("#overlay").hide();
        })
        $(".edit-form-btn").click(async function() {
            var  type=$('#distanceType').val();
            var userName=$(".user_name").val();
            var email=$(".user_email").val();
            var selectedOption = $("#country_selector option:selected");
            var countryCode = selectedOption.data('phonecode');  
            var countryIsoCode = selectedOption.val();  
            var countryCode = countryCode ?  + countryCode : '';
            var userPhone=$(".user_phone").val();
            var userLatitude=parseFloat($(".user_latitude").val());
            var userLongitude=parseFloat($(".user_longitude").val());
            var active=$(".is_active").is(":checked");
            var bankName=$('.bank_name').val();
            var branchName=$('.branch_name').val();
            var holderName=$('.holder_name').val();
            var accountNumber=$('.account_number').val();
            var otherInfo=$('.other_info').val();           
            is_active_deactivate=false;
            if($("#is_active").is(':checked')) {
                is_active_deactivate=true;
            }
            
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
                            'countryCode': '+'+countryCode,
                            'countryISOCode': countryIsoCode,
                            'fullName': userName,
                            'phoneNumber': userPhone,                           
                            'subscriptionExpiryDate': subscriptionPlanExpiryDate
                        }
                    } else {
                        updateData={
                            'documentVerification': is_active_deactivate,
                            'location.longitude': userLongitude,
                            'location.latitude': userLatitude,
                            'profilePic': IMG,
                            'email': email,
                            'countryCode': '+'+countryCode,
                            'countryISOCode': countryIsoCode,
                            'fullName': userName,
                            'phoneNumber': userPhone,
                        }
                    }
                    database.collection('owner_users').doc(id).update(updateData).then(function(result) {
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
                                    window.location.href='{{ route("owners") }}';
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
                                    window.location.href='{{ route("owners") }}';
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

</script>
@endsection