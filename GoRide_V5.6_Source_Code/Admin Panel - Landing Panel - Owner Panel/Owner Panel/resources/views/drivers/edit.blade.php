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
                                        <input type="text" class="form-control user_phone"  onkeypress="return chkAlphabets2(event,'error2')">
                                        <div id="error2" class="err"></div>
                                   </div>
                                  <div class="form-text text-muted">
                                    {{trans('lang.user_phone_help')}}
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
                            <div class="show_km_charges_div d-none">
                                <div class="form-group row width-100">
                                    <label class="col-3 control-label ">{{trans('lang.per')}} <span class="global_basic_label"></span> {{trans('lang.rate')}}<span
                                    class="required-field"></span></label>
                                    <div class="col-7">
                                        <input type="number" class="form-control km_charges" min="0">
                                        <div class="form-text text-muted">
                                        {{ trans('lang.driver_km_help') }}
                                        </div>
                                    </div>
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
                    <button type="button" class="btn btn-primary edit-form-btn"><i class="fa fa-save"></i> {{
    trans('lang.save')}}
                    </button>
                    <a href="{!! route('drivers') !!}" class="btn btn-default"><i class="fa fa-undo"></i>{{
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
    var vehicle_type=[];
    var id="{{$driverId}}";
    var placeholderImage="{{ asset('/images/default_user.png') }}";
    var ref=database.collection('driver_users').where("id","==",id);
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
         $("#country_selector").select2({
            templateResult: formatState,
            templateSelection: formatState2,
            placeholder: "Select Country",
            allowClear: true
        });
        jQuery("#zone").select2({
            placeholder: "Select Country",
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
       
        ref.get().then(async function(snapshots) {
            var driverUser=snapshots.docs[0].data();
            $(".user_name").val(driverUser.fullName);
            $(".user_email").val(shortEmail(driverUser.email)).prop("disabled", true);
            var countryCode=driverUser.countryCode.includes('+')? driverUser.countryCode.slice(1):driverUser.countryCode;
            $("#country_selector").val(countryCode).trigger('change').prop("disabled", true);
            $(".user_phone").val(EditPhoneNumber(driverUser.phoneNumber)).prop("disabled", true);
            $("#user_service option[value='"+driverUser.serviceId+"']").attr("selected","selected").trigger('change');
            $("#user_service").prop("disabled", true);
            $(".user_latitude").val(driverUser.location&&driverUser.location.latitude? driverUser.location.latitude:'');
            $(".user_longitude").val(driverUser.location&&driverUser.location.longitude? driverUser.location.longitude:'');
            if(driverUser.hasOwnProperty('zoneIds')&&driverUser.zoneIds!=null&&driverUser.zoneIds!=''&&driverUser.zoneIds.length>0) {
                $("#zone").val(driverUser.zoneIds);
            }
            if(driverUser.vehicleInformation) {
                $(".vehicle_seats").val(driverUser.vehicleInformation.seats? driverUser.vehicleInformation.seats:'');
                if(driverUser.vehicleInformation.vehicleTypeId) {
                    $("#vehicle_type option[value='"+driverUser.vehicleInformation.vehicleTypeId+"']").attr("selected","selected");
                }
                if(driverUser.vehicleInformation.vehicleColor) {
                    $(".vehicle_color option[value='"+driverUser.vehicleInformation.vehicleColor+"']").attr("selected","selected");
                }
                $(".vehicle_number").val(driverUser.vehicleInformation.vehicleNumber? driverUser.vehicleInformation.vehicleNumber:'');               
                if (driverUser.vehicleInformation.registrationDate) {
                    var regDate = driverUser.vehicleInformation.registrationDate.toDate();
                    var formattedDate = regDate.toISOString().split('T')[0];
                    $(".registration_date").val(formattedDate);
                }
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
            if(driverUser.hasOwnProperty('zoneIds') && driverUser.zoneIds.length > 0) {
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

            let selectedRules = [];
            if (driverUser.hasOwnProperty('vehicleInformation') && driverUser.vehicleInformation.driverRules) {
                if (Array.isArray(driverUser.vehicleInformation.driverRules)) {
                    selectedRules = driverUser.vehicleInformation.driverRules.map(rule => rule.id);
                } else {
                    selectedRules = Object.keys(driverUser.vehicleInformation.driverRules);
                }
            }


            await database.collection('driver_rules')
            .where('enable', '==', true)
            .get()
            .then(async function(snapshots) {
                let rulesHtml = '';
                snapshots.docs.forEach(doc => {
                    let data = doc.data();
                    let id = doc.id;

                    var title = '';
                    if (Array.isArray(data.name)) {
                        var foundItem = data.name.find(item => item.type === setLanguageCode);
                        if (foundItem && foundItem.name != '') {
                            title = foundItem.name;
                        } else {
                            foundItem = data.name.find(item => item.type === defaultLanguageCode);
                            if (foundItem && foundItem.name != '') {
                                title = foundItem.name;
                            } else {
                                foundItem = data.name.find(item => item.type === 'en');
                                title = foundItem.name;
                            }
                        }
                    }

                    let isChecked = selectedRules.includes(id) ? 'checked' : '';

                    rulesHtml += `
                        <div class="col-12 mb-2">
                            <div class="form-check">
                                <input type="checkbox" 
                                    class="form-check-input driver_rule_checkbox" 
                                    id="rule_${id}" 
                                    name="driver_rules[]" 
                                    value="${id}" 
                                    ${isChecked}>
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

            jQuery("#overlay").hide();
        })
        $(".edit-form-btn").click(async function() {
            $(".error_top").hide().html("");
            var  type=$('#distanceType').val();
            var userName=$(".user_name").val();
            var email=$(".user_email").val();
            var countryCode=$("#country_selector :selected").val();
            var userPhone=$(".user_phone").val();
            var userService=$("#user_service :selected").val();
            var userServiceName=$("#service_name").val();
            var userLatitude=parseFloat($(".user_latitude").val());
            var userLongitude=parseFloat($(".user_longitude").val());
          
            var zoneIds=$('#zone option:selected').toArray().map(item => item.value);
            var ac_charges = null;
            var nonac_charges = null;
            var km_charges = null;          
            var vehicleSeats=$(".vehicle_seats").val();
            var vehicleType=$("#vehicle_type :selected").val();
            var vehicleColor=$(".vehicle_color :selected").val();
            var vehicleNumber=$(".vehicle_number").val();
            var subscriptionPlanId=$('#subscriptionPlanId').val();           
            var registrationDateInput = $(".registration_date").val();
            var registrationDate = null;
            if (registrationDateInput) {
                registrationDate = firebase.firestore.Timestamp.fromDate(new Date(registrationDateInput));
            }
            data_is_acnonac = $('#user_service option:selected').attr('data-is-acnonac');
            service_ac_charges = $("#user_service option:selected").attr("data-ac-charge");
            service_nonac_charges = $("#user_service option:selected").attr("data-non-ac-charge");
            service_km_charges = $("#user_service option:selected").attr("data-km-charge");
          
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
            } else if(vehicleType=='') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.vehicle_type_help')}}</p>");
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
            } else if (!registrationDateInput) {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{ trans('lang.registration_date_help') }}</p>");
                window.scrollTo(0, 0);
                jQuery("#overlay").hide();
                return false;
            }else {
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
                storeImageData().then(IMG => {                  
                    updateData={                       
                        'location.longitude': userLongitude,
                        'location.latitude': userLatitude,
                        'profilePic': IMG,
                        'email': email,
                        'serviceId': userService,
                        'serviceName': window.selectedService ? window.selectedService.title : null,
                        'countryCode': '+'+countryCode,
                        'fullName': userName,                       
                        'vehicleInformation.vehicleColor': vehicleColor,
                        'vehicleInformation.vehicleNumber': vehicleNumber,                      
                        'vehicleInformation.seats': vehicleSeats,                           
                        'vehicleInformation.rates': rates,
                        'vehicleInformation.driverRules': driverRules,
                        'phoneNumber': userPhone,
                        'zoneIds': zoneIds,
                        registrationDate: registrationDate,
                    }
                    
                    database.collection('driver_users').doc(id).update(updateData).then(function(result) {
                        jQuery("#overlay").hide();
                        window.location.href='{{ route("drivers") }}';
                      
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
        $("#zone-charges-wrapper").empty();
        let selectedZones = $('#zone').val() || [];

        selectedZones.forEach(zoneId => {
            let zoneName = $("#zone option[value='"+zoneId+"']").text();

            let html = `<div class="zone-charge-box border p-3 mb-2">
                            <h6><strong>${zoneName}</strong></h6>`;

            if(data_is_acnonac && data_is_acnonac == "true"){
                html += `
                    <div class="form-group">
                        <label>{{trans('lang.ac_per')}} ${distanceType} {{trans('lang.rate')}}</label>
                        <input type="number" class="form-control ac_rate" data-zone="${zoneId}" min="0">
                    </div>
                    <div class="form-group">
                        <label>{{trans('lang.nonac_per')}} ${distanceType} {{trans('lang.rate')}}</label>
                        <input type="number" class="form-control nonac_rate" data-zone="${zoneId}" min="0">
                    </div>`;
            } else {
                html += `
                    <div class="form-group">
                        <label>{{trans('lang.per')}} ${distanceType} {{trans('lang.rate')}}</label>
                        <input type="number" class="form-control km_rate" data-zone="${zoneId}" min="0">
                    </div>`;
            }

            html += `</div>`;
            $("#zone-charges-wrapper").append(html);
        });
    }
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