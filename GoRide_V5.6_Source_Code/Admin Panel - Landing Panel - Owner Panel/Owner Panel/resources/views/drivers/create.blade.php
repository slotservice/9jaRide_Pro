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
                                <label class="col-3 control-label">{{trans('lang.user_phone')}}<span
                                        class="required-field"></span></label>
                                <div class="col-7">
                                <div class="phone-box position-relative " id="phone-box">
                                        <select name="country" id="country_selector">
                                       @foreach($countries_data as $country)
                                            <option phoneCode="{{ $country->phoneCode }}" value="{{ $country->code }}">{{$country->countryName}}
                                                (+{{$country->phoneCode}})
                                            </option>
                                        @endforeach
                                        </select>
                                        <input type="text" class="form-control user_phone"  onkeypress="return chkAlphabets2(event,'error2')">
                                        <div id="error2" class="err"></div>
                                   </div>
                                  <div class="form-text text-muted">
                                    {{trans('lang.user_phone_help')}}
                                  </div>                  
                                  <button type="button" onclick="sendOTP()" id="send-code" class="btn btn-dark btn-lg btn-block text-uppercase waves-effect waves-light btn btn-primary">{{ trans('lang.otp_send') }}</button>
                               </div>                                
                            </div>
                            <div id="recaptcha-container" style="display:none;"></div>
                            <div class="form-group row width-50"> 
                                <label class="col-3 control-label" id="otp-label" style="display:none;">{{trans('lang.otp_verify')}}<span
                                        class="required-field"></span></label>
                                <div class="col-7">
                                    <div class="form-group " id="otp-box" style="display:none;">                                    
                                        <input class="form-control" placeholder="{{trans('lang.otp')}}" id="verificationcode" type="text" class="form-control" name="otp" value="{{ old('otp') }}" required autocomplete="otp" autofocus>
                                        <div class="otp_error">
                                        </div>
                                    </div>
                                    <div class="form-group text-center m-t-20">
                                        <div class="col-xs-12">
                                            <button type="button" style="display:none;" onclick="applicationVerifier()" id="verify_btn" class="btn btn-dark btn-lg btn-block text-uppercase waves-effect waves-light btn btn-primary">{{ trans('lang.otp_verify') }}</button>                                  
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" id="hidden_uuid" />

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
                                <input type="hidden" id="service_name" name="service_name">
                            </div>
                            <div class="form-group row width-100">
                                <label class="col-3 control-label">{{trans('lang.zone')}}<span
                                        class="required-field"></span></label>
                                <div class="col-7 multi-select-box">
                                    <select id='zone' class="form-control" multiple required></select>
                                </div>
                            </div>
                            <div class="form-group row width-100">
                                <div class="col-12">
                                    <h6>{{ trans("lang.know_your_cordinates") }} <a target="_blank"
                                            href="https://www.latlong.net/">{{trans("lang.latitude_and_longitude_finder") }}</a></h6>
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
                            <div class="form-group row width-50">
                                <label class="col-3 control-label">{{trans('lang.registration_date')}}<span class="required-field"></span></label>
                                <div class="col-7">
                                    <input type="date" class="form-control registration_date" name="registration_date">
                                    <div class="form-text text-muted">{{trans('lang.registration_date_help')}}</div>
                                </div>
                            </div>
                            <div id="zone-charges-wrapper"></div>


                        </fieldset>
                       
                        <fieldset>
                            <legend>{{trans('lang.select_your_rules')}}</legend>
                           
                            <div class="form-group row width-100" id="driver_rules_list"></div>
                        </fieldset>
                    </div>
                </div>
                <div class="form-group col-12 text-center btm-btn">
                    <button type="button" class="btn btn-primary edit-form-btn" style="display:none;"><i class="fa fa-save"></i> {{trans('lang.save')}}
                    </button>
                    <a href="{!! route('drivers') !!}" class="btn btn-default"><i class="fa fa-undo"></i>{{trans('lang.cancel')}}</a>
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
    var vehicle_type=[];
    var id="{{$id}}";                  
    var placeholderImage="{{ asset('/images/default_user.png') }}";
    var storageRef=firebase.storage().ref('images');
    var storage=firebase.storage();
    var setLanguageCode=getCookie('setLanguage');
    var defaultLanguageCode=getCookie('defaultLanguage');
    var service_ac_charges = null;
    var service_nonac_charges = null;
    var service_km_charges = null;
    var data_is_acnonac = false;
    var newcountriesjs = '<?php echo json_encode($newcountriesjs); ?>';
    var newcountriesjs = JSON.parse(newcountriesjs);
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
    var distanceType = '';
    database.collection('settings').doc('globalValue').get()
    .then((doc) => {
        if (doc.exists) {
            distanceType = doc.data().distanceType;  
            console.log(distanceType)  ;       
        } else {
            console.log("No such document!");
        }
    })
    .catch((error) => {
        console.error("Error getting document:", error);
    });

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
        let serviceName = $('#user_service option:selected').attr('data-title');
        $('#service_name').val(serviceName);       
        let serviceId = $('#user_service option:selected').val();
        let fullService = service_list.find(s => s.id === serviceId);
        window.selectedService = fullService;
        renderZoneCharges();
    });
    $(document).ready(async function() {
        $('.driver_menu').addClass('active');
        // jQuery("#country_selector").select2({
        //     placeholder: "{{trans('lang.select_country')}}",
        //     allowClear: true
        // });
        $("#country_selector").select2({
            templateResult: formatState,
            templateSelection: formatState2,
            placeholder: "Select Country",
            allowClear: true
        });
        jQuery("#zone").select2({
            placeholder: "{{trans('lang.select_zone')}}",
            allowClear: true
        });
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

        await database.collection('driver_rules')
        .where('enable', '==', true)
        .get()
        .then(async function(snapshots) {
            let rulesHtml = '';
            snapshots.docs.forEach(doc => {
                let data = doc.data();
                let id = doc.id;
                // let ruleName = data.name || '';

                var title = '';
                if (Array.isArray(data.name)) {

                    var foundItem = data.name.find(item => item.type === setLanguageCode);
                    if (foundItem && foundItem.name != '') {
                        title = foundItem.name;
                    } else {
                        var foundItem = data.name.find(item => item.type === defaultLanguageCode);
                        if (foundItem && foundItem.name != '') {
                            title = foundItem.name;
                        } else {
                            var foundItem = data.name.find(item => item.type === 'en');
                            title = foundItem.name;
                        }
                    }

                }

                rulesHtml += `
                    <div class="col-12 mb-2">
                        <div class="form-check">
                            <input type="checkbox" 
                                class="form-check-input driver_rule_checkbox" 
                                id="rule_${id}" 
                                name="driver_rules[]" 
                                value="${id}">
                            <label class="form-check-label" for="rule_${id}">
                                ${title}
                            </label>
                        </div>
                    </div>
                `;
            });

            document.getElementById("driver_rules_list").innerHTML = rulesHtml;
        })
        .catch(function(error) {
            console.error("Error loading driver rules:", error);
        });
       
      
        $(".edit-form-btn").click(async function() {           
            jQuery("#overlay").show();
            var  type=$('#distanceType').val();
            var userName=$(".user_name").val();
            var email=$(".user_email").val();
            var countryCode=$("#country_selector :selected").attr("phoneCode");
            var userPhone=$(".user_phone").val();
            var userService=$("#user_service :selected").val();
            var userServiceName=$("#service_name").val();
            var userLatitude=parseFloat($(".user_latitude").val());
            var userLongitude=parseFloat($(".user_longitude").val());
           
            var zoneIds=$('#zone option:selected').toArray().map(item => item.value);           
            var km_charges = null;
            var registrationDateInput = $(".registration_date").val();
            var registrationDate = null;
            if (registrationDateInput) {
                registrationDate = firebase.firestore.Timestamp.fromDate(new Date(registrationDateInput));
            }
            var vehicleSeats=$(".vehicle_seats").val();
          
            var vehicleColor=$(".vehicle_color :selected").val();
            var vehicleNumber=$(".vehicle_number").val();
            var subscriptionPlanId=$('#subscriptionPlanId').val();
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
                        $(".error_top").show().html("<p>{{ trans('lang.please_enter_valid') }} {{ trans('lang.per_km_rate') }}</p>");
                        isValid = false;
                        return false;
                    }
                    if(service_km_charges && parseFloat(perKmRate) > parseFloat(service_km_charges)){
                        $(".error_top").show().html("<p>{{ trans('lang.please_enter_valid_per_km_rate') }} (max " + service_km_charges + ")</p>");
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

            if(userName=='') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.user_name_help')}}</p>");
                window.scrollTo(0,0);
                jQuery("#overlay").hide();
            } else if(email=='') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.user_email_help')}}</p>");
                window.scrollTo(0,0);
                jQuery("#overlay").hide();

            } else if(countryCode=='') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.user_country_code_help')}}</p>");
                window.scrollTo(0,0);
                jQuery("#overlay").hide();

            } else if(userPhone=='') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.user_phone_help')}}</p>");
                window.scrollTo(0,0);
                jQuery("#overlay").hide();

            } else if(userService=='') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.user_service_help')}}</p>");
                window.scrollTo(0,0);
                jQuery("#overlay").hide();

            } else if(zoneIds.length==0) {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.select_zone_help')}}</p>");
                window.scrollTo(0,0);
                jQuery("#overlay").hide();

            } else if(isNaN(userLatitude)||userLatitude=='') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.user_latitude_help')}}</p>");
                window.scrollTo(0,0);
                jQuery("#overlay").hide();
            } else if(isNaN(userLongitude)||userLongitude=='') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.user_longitude_help')}}</p>");
                window.scrollTo(0,0);
                jQuery("#overlay").hide();
            } else if(vehicleSeats=='') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.vehicle_seats_help')}}</p>");
                window.scrollTo(0,0);
                jQuery("#overlay").hide();
            } else if(vehicleColor=='') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.vehicle_color_help')}}</p>");
                window.scrollTo(0,0);
                jQuery("#overlay").hide();
            } else if(vehicleNumber=='') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.vehicle_number_help')}}</p>");
                window.scrollTo(0,0);
                jQuery("#overlay").hide();

            } else if(!data_is_acnonac && (km_charges == "" || km_charges <= 0)){
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{ trans('lang.please_enter_valid') }}  {{ trans('lang.per') }} "+type+"  {{ trans('lang.rate') }}</p>");
                window.scrollTo(0,0);
                jQuery("#overlay").hide();

            }else if(!data_is_acnonac && service_km_charges != null && service_km_charges != "" && parseFloat(km_charges) > parseFloat(service_km_charges)){
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>"+type +" {{ trans('lang.please_enter_valid_km_charges') }} "+service_km_charges+"</p>");
                window.scrollTo(0,0);
                jQuery("#overlay").hide();

            }else if (!registrationDateInput) {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{ trans('lang.registration_date_help') }}</p>");
                window.scrollTo(0, 0);
                jQuery("#overlay").hide();
                return false;
            } else {
                jQuery("#overlay").show();
               
                let selectedRuleIds = [];
                $(".driver_rule_checkbox:checked").each(function () {
                    selectedRuleIds.push($(this).val());
                });

                let driverRules = [];

                await Promise.all(selectedRuleIds.map(async (ruleId) => {
                    let ruleDoc = await database.collection('driver_rules').doc(ruleId).get();
                    if (ruleDoc.exists) {
                        let ruleData = ruleDoc.data();
                        driverRules.push({
                            id: ruleDoc.id,
                            ...ruleData  
                        });
                    }
                }));

            
                storeImageData().then(async (IMG) => {
                    var newDriverId = $('#hidden_uuid').val();

                    var newData = {
                        'id': newDriverId,
                        'documentVerification': true,
                        'location': {
                            longitude: userLongitude,
                            latitude: userLatitude
                        },
                        'profilePic': IMG,
                        'email': email,
                        'serviceId': userService,
                        'serviceName':  window.selectedService ? window.selectedService.title : null,
                        'countryCode': '+'+countryCode,
                        'fullName': userName,
                        'isEnabled': true,
                        'isOnline': false,
                        'vehicleInformation': {
                            vehicleColor: vehicleColor,
                            vehicleNumber: vehicleNumber,                            
                            seats: vehicleSeats,                           
                            registrationDate: registrationDate,
                            driverRules: driverRules,
                            rates: rates
                        },
                        'phoneNumber': userPhone,
                        'zoneIds': zoneIds,
                        'createdAt': firebase.firestore.FieldValue.serverTimestamp(),
                        'ownerId':id,
                        subscriptionExpiryDate: null,
                        subscriptionPlanId: null,
                        subscriptionTotalOrders: null,
                        subscription_plan:null,
                        'reviewsCount': "0.0",
                        'reviewsSum': "0.0",                        
                    };

                    await database.collection('driver_users').doc(newDriverId).set(newData).then(async function() {
                     
                        jQuery("#overlay").hide();
                        window.location.href='{{ route("drivers") }}';
                    }).catch(err => {
                        jQuery("#overlay").hide();
                        $(".error_top").show().html("<p>"+err+"</p>");
                        window.scrollTo(0,0);
                    });
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
    function chkAlphabets2(event,msg)
    {
        if(!(event.which>=48  && event.which<=57)
        )
        {
        document.getElementById(msg).innerHTML="{{trans('lang.accept_only_number')}}";
        return false;
        }
        else
        {
        document.getElementById(msg).innerHTML="";
        return true;
        }
    }
    $('#zone').on('change', function(){
        renderZoneCharges();
    });
    function renderZoneCharges() {
        $("#zone-charges-wrapper").empty();
        let selectedZones = $('#zone').val() || [];

        selectedZones.forEach(zoneId => {
            let zoneName = $("#zone option[value='"+zoneId+"']").text();

            let html = `<div class="zone-charge-box border p-3 mb-2">
                            <h6><strong>${zoneName}</strong></h6>`;

            if(data_is_acnonac && data_is_acnonac == "true"){
                html += `
                    <div class="form-group">
                        <label>{{trans('lang.ac_per')}} ${distanceType} {{trans('lang.rate')}}<span
                                        class="required-field"></span></label>
                        <input type="number" class="form-control ac_rate" data-zone="${zoneId}" min="0">
                    </div>
                    <div class="form-group">
                        <label>{{trans('lang.nonac_per')}} ${distanceType} {{trans('lang.rate')}}<span
                                        class="required-field"></span></label>
                        <input type="number" class="form-control nonac_rate" data-zone="${zoneId}" min="0">
                    </div>`;
            } else {
                html += `
                    <div class="form-group">
                        <label>{{trans('lang.per')}} ${distanceType} {{trans('lang.rate')}}<span
                                        class="required-field"></span></label>
                        <input type="number" class="form-control km_rate" data-zone="${zoneId}" min="0">
                    </div>`;
            }

            html += `</div>`;
            $("#zone-charges-wrapper").append(html);
        });
    }  
    if (!window.recaptchaVerifier) {
        window.recaptchaVerifier = new firebase.auth.RecaptchaVerifier('recaptcha-container', {
            'size': 'invisible',
            'callback': (response) => {}
        });
    } 
    function sendOTP() {
        $(".otp").val("");          
        var userPhone=$(".user_phone").val();
        if (userPhone == "") {
            $(".error_top").show();
            $(".error_top").html("");
            $(".error_top").append("<p>{{trans('lang.user_phone_help')}}</p>");
            window.scrollTo(0,0);
            jQuery("#overlay").hide();
        } else {
            var phone = jQuery(".user_phone").val();
            var phoneNumber = '+' + jQuery("#country_selector :selected").attr("phoneCode") + jQuery(".user_phone").val();
            database.collection("driver_users").where('phoneNumber', '==', phone).get().then(async function(snapshots) {
                if (snapshots.docs.length > 0) {
                    alert("{{trans('lang.you_already_have_account_with_this_phone_number')}}")
                    return false;
                } else {
                    
                    firebase.auth().signInWithPhoneNumber(phoneNumber, window.recaptchaVerifier)
                        .then(function(confirmationResult) {
                            window.confirmationResult = confirmationResult;
                            if (confirmationResult.verificationId) {                                                            
                                $(".error_top").hide();                             
                                jQuery("#verify_btn").show();
                                jQuery("#otp-box").show();
                                jQuery("#otp-label").show();
                            }
                        });
                }
            })
        }
    }
    function applicationVerifier() {
        var code = $('#verificationcode').val();
        if (!code) {
            $('.otp_error').html("{{trans('lang.please_enter_otp')}}");
            return;
        }

        window.confirmationResult.confirm(code)
        .then(async function(result) {
            var uuid = result.user.uid;
            $('#hidden_uuid').val(uuid);
            $('#verify_btn').hide();
            $('.edit-form-btn').show();
            $('#verificationcode').hide();
            $('#otp-label').hide();
            $('#send-code').hide();   
            $(".otp_error").html("");     
            $("#country_selector").prop("disabled", true);
            $(".user_phone").prop("disabled", true);   
        })
        .catch((error) => {
            console.error("OTP verification failed ", error);
            $(".otp_error").html("{{trans('lang.invalid_otp')}}");
        });
    }

     var newcountriesjs = '<?php echo json_encode($newcountriesjs); ?>';
            var newcountriesjs = JSON.parse(newcountriesjs);
            function formatState(state) {
                if (!state.id) {
                    return state.text;
                }
                var countryCode = state.element.value.toLowerCase(); // "GB" → "gb"
                var baseUrl = "<?php echo URL::to('/'); ?>/scss/icons/flag-icon-css/flags";
                var $state = $(
                    '<span><img src="' + baseUrl + '/' + countryCode + '.svg' + '" class="img-flag" /> ' + state.text + '</span>'
                );
                return $state;
            }
            function formatState2(state) {
                if (!state.id) {
                    return state.text;
                }
                var countryCode = state.element.value.toLowerCase();
                var baseUrl = "<?php echo URL::to('/'); ?>/scss/icons/flag-icon-css/flags";
                var $state = $(
                    '<span><img class="img-flag" src="' + baseUrl + '/' + countryCode + '.svg' + '" /> <span>' + state.text + '</span></span>'
                );
                return $state;
            }	
            
            var globalSettingsRef = database.collection('settings').doc("globalValue");

            globalSettingsRef.get().then(function(snapshot) {

                var globalSettings = snapshot.data();

                if (!globalSettings || !globalSettings.defaultCountryCode) {
                    return;
                }

                let defaultCountryCode = globalSettings.defaultCountryCode.toString().trim();
                let $option = null;

                $option = $("#country_selector option[value='" + defaultCountryCode.toUpperCase() + "']");

                if ($option.length === 0) {
                    let phoneCode = defaultCountryCode.replace('+', '');
                    $option = $("#country_selector option[phoneCode='" + phoneCode + "']");
                }

                if ($option.length > 0) {
                    $("#country_selector").val($option.val()).trigger('change');
                } else {
                    console.warn("Default country not found:", defaultCountryCode);
                }

            }).catch(function(error) {
                console.error("Error fetching global settings:", error);
	        });
</script>
@endsection