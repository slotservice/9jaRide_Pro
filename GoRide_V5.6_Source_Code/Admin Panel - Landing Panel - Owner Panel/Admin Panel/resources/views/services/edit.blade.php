@extends('layouts.app')
@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.service_plural')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item"><a href="{!! route('services') !!}">{{trans('lang.service_plural')}}</a>
                </li>
                <li class="breadcrumb-item active">{{trans('lang.service_edit')}}</li>
            </ol>
        </div>
    </div>
    <div class="container-fluid">
        <div class="card pb-4">
             <div class="card-header">
                <ul class="nav nav-tabs" id="language-tabs" role="tablist">
                </ul>
            </div>
            <div class="card-body">
                <div class="error_top"></div>
                <div class="row restaurant_payout_create">
                    <div class="restaurant_payout_create-inner">
                        <fieldset>
                            <legend>{{trans('lang.service_details')}}</legend>
                            <div class="tab-content" id="language-contents">  </div>
                            <input type="hidden" id="distanceType" />
                            <div class="form-group row width-50">
                                <label class="col-3 control-label">{{trans('lang.service_image')}}</label>
                                <div class="col-7">
                                    <input type="file" onChange="handleFileSelect(event)" class="form-control image"
                                        id="service_image">
                                    <div class="placeholder_img_thumb service_image"></div>
                                    <div id="uploding_image"></div>
                                </div>
                            </div>
                            <div class="form-group row width-50">
                                <label class="col-3 control-label">{{trans('lang.marker_icon')}}</label>
                                <div class="col-7">
                                    <div class="map-markers">
                                        <ul>
                                            <li>
                                                <input type="radio" name="marker_icon" value="https://firebasestorage.googleapis.com/v0/b/goride-1a752.appspot.com/o/marker%2Fsedan.png?alt=media&token=fa051760-2f62-4c22-a3a5-b9862ab92937" id="sedan" title="Seden" checked>
                                                <img src="https://firebasestorage.googleapis.com/v0/b/goride-1a752.appspot.com/o/marker%2Fsedan.png?alt=media&token=fa051760-2f62-4c22-a3a5-b9862ab92937">
                                            </li>
                                            <li>
                                                <input type="radio" name="marker_icon" value="https://firebasestorage.googleapis.com/v0/b/goride-1a752.appspot.com/o/marker%2Fsuv.png?alt=media&token=5456457f-aad9-45fc-bb63-0831cc1938fe" id="suv" title="SUV">
                                                <img src="https://firebasestorage.googleapis.com/v0/b/goride-1a752.appspot.com/o/marker%2Fsuv.png?alt=media&token=5456457f-aad9-45fc-bb63-0831cc1938fe">
                                            </li>
                                            <li>
                                                <input type="radio" name="marker_icon" value="https://firebasestorage.googleapis.com/v0/b/goride-1a752.appspot.com/o/marker%2Fhatchback.png?alt=media&token=777b080b-f361-4c3e-aa81-12a07bc3604d" id="hatchback" title="Hatchback">
                                                <img src="https://firebasestorage.googleapis.com/v0/b/goride-1a752.appspot.com/o/marker%2Fhatchback.png?alt=media&token=777b080b-f361-4c3e-aa81-12a07bc3604d">
                                            </li>
                                            <li>
                                                <input type="radio" name="marker_icon" value="https://firebasestorage.googleapis.com/v0/b/goride-1a752.appspot.com/o/marker%2Fminivan.png?alt=media&token=d95a4a10-02b3-4576-b4e3-0560008abd76" id="minivan" title="Minivan">
                                                <img src="https://firebasestorage.googleapis.com/v0/b/goride-1a752.appspot.com/o/marker%2Fminivan.png?alt=media&token=d95a4a10-02b3-4576-b4e3-0560008abd76">
                                            </li>
                                            <li>
                                                <input type="radio" name="marker_icon" value="https://firebasestorage.googleapis.com/v0/b/goride-1a752.appspot.com/o/marker%2Fbike.png?alt=media&token=f1f8cc1e-fa7b-443c-a0a6-aa98e1b9a2ee" id="bike" title="Bike">
                                                <img src="https://firebasestorage.googleapis.com/v0/b/goride-1a752.appspot.com/o/marker%2Fbike.png?alt=media&token=f1f8cc1e-fa7b-443c-a0a6-aa98e1b9a2ee">
                                            </li>
                                            <li>
                                                <input type="radio" name="marker_icon" value="https://firebasestorage.googleapis.com/v0/b/goride-1a752.appspot.com/o/marker%2Fscooter.png?alt=media&token=eb2a6288-d40c-4822-a65f-7725d900283d" id="scooter" title="Scooter">
                                                <img src="https://firebasestorage.googleapis.com/v0/b/goride-1a752.appspot.com/o/marker%2Fscooter.png?alt=media&token=eb2a6288-d40c-4822-a65f-7725d900283d">
                                            </li>
                                            <li>
                                                <input type="radio" name="marker_icon" value="https://firebasestorage.googleapis.com/v0/b/goride-1a752.appspot.com/o/marker%2Fautorickshaw.png?alt=media&token=c2811b38-7475-499f-adf6-5f9fca48196d" id="autorickshaw" title="Auto Rickshaw">
                                                <img src="https://firebasestorage.googleapis.com/v0/b/goride-1a752.appspot.com/o/marker%2Fautorickshaw.png?alt=media&token=c2811b38-7475-499f-adf6-5f9fca48196d">
                                            </li>
                                            <li>
                                                <input type="radio" name="marker_icon" value="https://firebasestorage.googleapis.com/v0/b/goride-1a752.appspot.com/o/marker%2Fminibus.png?alt=media&token=e069c469-655d-40cf-bd71-b267ab679841" id="minibus" title="Minibus">
                                                <img src="https://firebasestorage.googleapis.com/v0/b/goride-1a752.appspot.com/o/marker%2Fminibus.png?alt=media&token=e069c469-655d-40cf-bd71-b267ab679841">
                                            </li>
                                            <li>
                                                <input type="radio" name="marker_icon" value="https://firebasestorage.googleapis.com/v0/b/goride-1a752.appspot.com/o/marker%2Fbus.png?alt=media&token=1bdd91b2-d54a-4ec9-85ac-cd4cdf81ee79" id="bus" title="Bus">
                                                <img src="https://firebasestorage.googleapis.com/v0/b/goride-1a752.appspot.com/o/marker%2Fbus.png?alt=media&token=1bdd91b2-d54a-4ec9-85ac-cd4cdf81ee79">
                                            </li>
                                            <li>
                                                <input type="radio" name="marker_icon" value="https://firebasestorage.googleapis.com/v0/b/goride-1a752.appspot.com/o/marker%2Ftruck.png?alt=media&token=5df5e16b-17ed-4014-894b-25b70ed03613" id="truck" title="Truck">
                                                <img src="https://firebasestorage.googleapis.com/v0/b/goride-1a752.appspot.com/o/marker%2Ftruck.png?alt=media&token=5df5e16b-17ed-4014-894b-25b70ed03613">
                                            </li>
                                        </ul>
                                        <div class="form-text text-muted">
                                            {{ trans('lang.marker_icon_help') }} 
                                        </div>   
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row width-50">
                                <div class="form-check">
                                    <input type="checkbox" class="service_active" id="active">
                                    <label class="col-3 control-label" for="active">{{trans('lang.enable')}}</label>
                                </div>
                            </div>
                            <div class="form-group row width-50">
                                <div class="form-check">
                                    <input type="checkbox" class="intercity_type" id="intercityType">
                                    <label class="col-3 control-label" for="intercityType">{{ trans('lang.service_intercity') }}</label>
                                    <div class="form-text text-muted">
                                    {{ trans('lang.intercity_help') }} 
                                    </div>   
                                </div>
                            </div>
                            <div class="form-group row width-50">
                                <div class="form-check">
                                    <input type="checkbox" class="offer_rate" id="offer_rate">
                                    <label class="col-3 control-label" for="offer_rate">{{ trans('lang.enable_offer_rate') }}</label>
                                    <div class="form-text text-muted">
                                          {{ trans('lang.offer_rate_help') }} 
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row width-50">
                                <div class="form-check">
                                    <input type="checkbox" class="IsglobalAdminComission" id="IsglobalAdminComission"
                                        onclick="ShowHideDiv()">
                                    <label class="col-3 control-label" for="IsglobalAdminComission">{{
                                         trans('lang.IsglobalAdminComossion') }}</label>
                                         <div class="form-text text-muted">
                                            {{ trans('lang.global_commission_help') }} <a href="{{ route('settings.businessModel') }}" target="_blank">{{trans('lang.here')}}</a>
                                        </div> 
                                </div>
                            </div>
                            <div class="form-group row width-50" id="comissionType">
                                <label class="col-4 control-label">{{ trans('lang.commission_type') }}</label>
                                <div class="col-7">
                                    <select class="form-control commission_type" id="commission_type">
                                        <option value="fix">{{ trans('lang.fixed') }}</option>
                                        <option value="percentage">{{ trans('lang.percentage') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row width-50" id="comission">
                                <label class="col-4 control-label">{{ trans('lang.admin_commission') }}<span
                                        class="required-field"></span></label>
                                <div class="col-7">
                                    <input type="number" class="form-control commission">
                                </div>
                            </div>
                        </fieldset>
                    </div>
                    <div class="serice-tab-wrap restaurant_payout_create-inner">  
                         <label class="col-4 control-label">{{ trans('lang.price_by_zone') }}</label>
                        <div class="card-header p-0">
                            <ul class="nav nav-tabs" id="zone-tabs" role="tablist">
                            </ul>
                            <button type="button" id="applyToAllBtn" class="btn btn-sm btn-primary">{{trans('lang.apply_to_all')}}</button>
                        </div>
                        <div class="tab-content" id="zone-contents"></div>
                    </div>
                </div>
                <div class="form-group col-12 text-center btm-btn">
                    <button type="button" class="btn btn-primary  edit-setting-btn"><i class="fa fa-save"></i> {{ trans('lang.save')}} </button>
                    <a href="{!! route('services') !!}" class="btn btn-default"><i class="fa fa-undo"></i>{{ trans('lang.cancel')}}</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')

<script>

    var id = "<?php echo $id; ?>";
    var database = firebase.firestore();
    var ref = database.collection('service').where("id", "==", id);
    var storageRef = firebase.storage().ref('images');

    var storage = firebase.storage();
    var photo = "";
    var fileName = "";
    var serviceImageFile = '';
    var append_list = '';
    var data = '';
    var langData = '';

    var placeholderImage = "{{ asset('/images/default_user.png') }}";
    function ShowHideDiv() {
        let enableCommision = $("#IsglobalAdminComission").is(":checked");
        if (enableCommision) {
            $("#comissionType").hide();
            $("#comission").hide();
        } else {
            $("#comissionType").show();
            $("#comission").show();
        }
    }
    function acNonAcDiv(zone_id) {
        let is_ac_non_ac = $(".is_ac_non_ac_"+zone_id).is(":checked");
        if (is_ac_non_ac) {
            $(".show_ac_non_ac_div_"+zone_id).removeClass('d-none');
            $(".km_charges_div_"+zone_id).addClass('d-none');
        } else {
            $(".show_ac_non_ac_div_"+zone_id).addClass('d-none');
            $(".km_charges_div_"+zone_id).removeClass('d-none');
        }
    }

    $(document).ready(function () {

        fetchLanguages().then(createLanguageTabs);
        
        $('.ride_sub_menu li').each(function () {
            var url = $(this).find('a').attr('href');
            if (url == document.referrer) {
                $(this).find('a').addClass('active');
                $('.rides_menu').addClass('active').attr('aria-expanded', true);
            }
            $('.ride_sub_menu').addClass('in').attr('aria-expanded', true);
        });
        
        jQuery("#overlay").show();
        
        ref.get().then(async function (snapshots) {

            data = snapshots.docs[0].data();
            
            if (data.hasOwnProperty('adminCommission')) {
                if (data.adminCommission.isEnabled) {
                    $("#IsglobalAdminComission").prop('checked', true);
                    $("#comissionType").hide();
                    $("#comission").hide();
                } else {
                    $("#commission_type").val(data.adminCommission.type);
                    $(".commission").val(data.adminCommission.amount);
                }
            }
            if(Array.isArray(data.title)) {
                data.title.forEach(function(titleObj) {
                    var inputField=$(`#service-title-${titleObj.type}`);
                    if(inputField.length) {
                        inputField.val(titleObj.title);
                    }
                });
            }
            if (data.offerRate) {
                $('.offer_rate').prop('checked', true);
            }
            if (data.enable) {
                $('.service_active').prop('checked', true);
            }
            $('.intercity_type').prop('checked', data.intercityType ? true : false);

            $('.intercity_type').prop('checked', data.intercityType ? true : false);

            if (data.markerIcon) {
                $("input[name='marker_icon'][value='" + data.markerIcon + "']").prop("checked", true);
            }else{
                $("#sedan").prop("checked", true);
            }
            
            photo = data.image;
            if (photo != '') {
                $(".service_image").append('<span class="image-item"><span class="remove-btn"><i class="fa fa-remove"></i></span><img class="rounded" style="width:50px" src="' + photo + '" alt="image">');
                serviceImageFile = data.image;
            } else {
                photo = "";
                $(".service_image").append('<span class="image-item"><span class="remove-btn"><i class="fa fa-remove"></i></span><img class="rounded" style="width:50px" src="' + placeholderImage + '" alt="image">');
            }
            
            const [langRef, zones, refCurrency] = await Promise.all([
                database.collection('languages').where('enable', '==', true).where('isDefault', '==', true).get(),
                fetchZones(),
                database.collection('currency').where('enable', '==', true).get()
            ]);
            langData = langRef.docs[0].data();
            createZoneTabs(zones);
            let currencyData = refCurrency.docs[0].data();
            $(".currentCurrency").text(currencyData.symbol);
            
            jQuery("#overlay").hide();
        });
    });

    async function storeImageData() {
        var newPhoto = '';
        try {
            if (serviceImageFile != "" && photo != serviceImageFile) {
                var serviceOldImageUrlRef = await storage.refFromURL(serviceImageFile);
                imageBucket = serviceOldImageUrlRef.bucket;
                var envBucket = "<?php echo env('FIREBASE_STORAGE_BUCKET'); ?>";
                if (imageBucket == envBucket) {
                    await serviceOldImageUrlRef.delete().then(() => {
                        console.log("Old file deleted!")
                    }).catch((error) => {
                        console.log("ERR File delete ===", error);
                    });
                } else {
                    console.log('Bucket not matched');
                }
            }
            if (photo != serviceImageFile) {
                photo = photo.replace(/^data:image\/[a-z]+;base64,/, "")
                var uploadTask = await storageRef.child(fileName).putString(photo, 'base64', { contentType: 'image/jpg' });
                var downloadURL = await uploadTask.ref.getDownloadURL();
                newPhoto = downloadURL;
                photo = downloadURL;
            } else {
                newPhoto = photo;
            }
        } catch (error) {
            console.log("ERR ===", error);
        }
        return newPhoto;
    }

    $(document).on("click", "#applyToAllBtn", async function () {
        let zones = await fetchZones();
        const activeTab = $("#zone-tabs .nav-link.active");
        const activeZoneId = activeTab.data("zone-id");
        const sourceValues = {
            isAcNonAc: $(`.is_ac_non_ac_${activeZoneId}`).is(":checked"),
            acCharge: $(`.ac_charges_${activeZoneId}`).val(),
            nonAcCharge: $(`.nonac_charges_${activeZoneId}`).val(),
            kmCharge: $(`.km_charges_${activeZoneId}`).val(),
            basicFare: $(`.basic_fare_km_${activeZoneId}`).val(),
            basicFareCharge: $(`.basic_fare_charges_${activeZoneId}`).val(),
            holdingMinute: $(`.holding_charge_minute_${activeZoneId}`).val(),
            holdingMinuteCharge: $(`.holding_charges_${activeZoneId}`).val(),
            perMinuteCharge: $(`.ride_time_fare_per_minute_${activeZoneId}`).val(),
            startNightTime: $(`.start_night_time_${activeZoneId}`).val(),
            endNightTime: $(`.end_night_time_${activeZoneId}`).val(),
            nightCharge: $(`.night_time_fare_${activeZoneId}`).val(),
        };
        zones.forEach(zone => {
            if (zone.id !== activeZoneId) {
                $(`.is_ac_non_ac_${zone.id}`).prop("checked", sourceValues.isAcNonAc);
                $(`.ac_charges_${zone.id}`).val(sourceValues.acCharge);
                $(`.nonac_charges_${zone.id}`).val(sourceValues.nonAcCharge);
                $(`.km_charges_${zone.id}`).val(sourceValues.kmCharge);
                $(`.basic_fare_km_${zone.id}`).val(sourceValues.basicFare);
                $(`.basic_fare_charges_${zone.id}`).val(sourceValues.basicFareCharge);
                $(`.holding_charge_minute_${zone.id}`).val(sourceValues.holdingMinute);
                $(`.holding_charges_${zone.id}`).val(sourceValues.holdingMinuteCharge);
                $(`.ride_time_fare_per_minute_${zone.id}`).val(sourceValues.perMinuteCharge);
                $(`.start_night_time_${zone.id}`).val(sourceValues.startNightTime);
                $(`.end_night_time_${zone.id}`).val(sourceValues.endNightTime);
                $(`.night_time_fare_${zone.id}`).val(sourceValues.nightCharge);
                acNonAcDiv(zone.id);
            }
        });
    });

    $(".edit-setting-btn").click(async function () {

        var type = $('#distanceType').val();
        var titles=[];
        $("[id^='service-title-']").each(function() {
            var languageCode=$(this).attr('id').replace('service-title-','');
            var nameValue=$(this).val();
            titles.push({
                title: nameValue,
                type: languageCode
            });
        });

        var isEnglishNameValid=titles.some(function(nameObj) {
            return nameObj.type === 'en' && nameObj.title.trim() !== '';
        });

        var enable = false;
        if ($(".service_active").is(':checked')) {
            enable = true;
        }
        var offerRate = false;
        if ($(".offer_rate").is(':checked')) {
            offerRate = true;
        }
        var isGlobalAdminCommission = $("#IsglobalAdminComission").is(":checked") ? true : false;
        if (isGlobalAdminCommission == false) {
            var comission_type = $("#commission_type :selected").val();
            var admin_comission = $(".commission").val();
        } else {
            var comission_type = '';
            var admin_comission = '';
        }
        var adminCommission = { 'isEnabled': isGlobalAdminCommission, 'type': comission_type, 'amount': admin_comission };
        var intercityType = $(".intercity_type").is(':checked') ? true : false;
        var markerIcon = $("input[name='marker_icon']:checked").val();
        
        if(!isEnglishNameValid) {
            showError("{{trans('lang.service_title_en_required')}}");
        } else if(admin_comission==''&&isGlobalAdminCommission==false) {
            showError("{{trans('lang.commission_help')}}");
        } else {

            let zones = await fetchZones();
            const prices = [];
            for (let zone of zones) {
                const is_ac_non_ac = $(`.is_ac_non_ac_${zone.id}`).is(":checked");
                const ac_charges = $(`.ac_charges_${zone.id}`).val();
                const nonac_charges = $(`.nonac_charges_${zone.id}`).val();
                const km_charges = $(`.km_charges_${zone.id}`).val();
                const basicFareKm = $(`.basic_fare_km_${zone.id}`).val();
                const basicFareCharges = $(`.basic_fare_charges_${zone.id}`).val();
                const holdingChargeMinute = $(`.holding_charge_minute_${zone.id}`).val();
                const holdingCharges = $(`.holding_charges_${zone.id}`).val();
                const rideTimeFarePerMinute = $(`.ride_time_fare_per_minute_${zone.id}`).val();
                const startNightTime = $(`.start_night_time_${zone.id}`).val();
                const endNightTime = $(`.end_night_time_${zone.id}`).val();
                const nightFareCharge = $(`.night_time_fare_${zone.id}`).val();
                const zoneData = zone.name.find(item => item.type === langData.code);

                if (basicFareKm == '' || basicFareKm < 0) {
                    showError("{{ trans('lang.please_enter_valid') }} {{ trans('lang.basic_fare') }} {{ trans('lang.for_ride_fare') }} (" + zoneData.name + ")");
                    return false;
                }
                if (basicFareCharges == '' || basicFareCharges < 0) {
                    showError("{{ trans('lang.please_enter_valid') }} {{ trans('lang.basic_fare') }} {{ trans('lang.charges') }} {{ trans('lang.for_ride_fare') }} (" + zoneData.name + ")");
                    return false;
                }
                if (is_ac_non_ac && (ac_charges == '' || ac_charges <= 0)) {
                    showError("{{ trans('lang.please_enter_valid') }} {{ trans('lang.ac_charges') }} {{ trans('lang.for_ride_fare') }} (" + zoneData.name + ")");
                    return false;
                }
                if (is_ac_non_ac && (nonac_charges == '' || nonac_charges <= 0)) {
                    showError("{{ trans('lang.please_enter_valid') }} {{ trans('lang.nonac_charges') }} {{ trans('lang.for_ride_fare') }} (" + zoneData.name + ")");
                    return false;
                }
                if (!is_ac_non_ac && (km_charges == '' || km_charges <= 0)) {
                    showError("{{ trans('lang.please_enter_valid') }} {{ trans('lang.charges') }} {{ trans('lang.for_ride_fare') }} (" + zoneData.name + ")");
                    return false;
                }
                if (holdingChargeMinute == '' || holdingChargeMinute < 0) {
                    showError("{{ trans('lang.please_enter_valid') }} {{ trans('lang.holding_charge_minute') }} (" + zoneData.name + ")");
                    return false;
                }
                if (holdingCharges == '' || holdingCharges < 0) {
                    showError("{{ trans('lang.please_enter_valid') }} {{ trans('lang.holding_charges') }} (" + zoneData.name + ")");
                    return false;
                }
                if (rideTimeFarePerMinute == '' || rideTimeFarePerMinute < 0) {
                    showError("{{ trans('lang.please_enter_valid') }} {{ trans('lang.ride_time_fare_per_minute') }} {{ trans('lang.for_ride_fare') }} (" + zoneData.name + ")");
                    return false;
                }
                if (startNightTime == '') {
                    showError("{{ trans('lang.start_night_time_help') }} (" + zoneData.name + ")");
                    return false;
                }
                if (endNightTime == '') {
                    showError("{{ trans('lang.end_night_time_help') }} (" + zoneData.name + ")");
                    return false;
                }
                if (nightFareCharge == '' || nightFareCharge < 0) {
                    showError("{{ trans('lang.please_enter_valid') }} {{ trans('lang.night_time_fare') }} {{ trans('lang.for_ride_fare') }} (" + zoneData.name + ")");
                    return false;
                }
                
                prices.push({
                    zoneId: zone.id,
                    isAcNonAc: is_ac_non_ac,
                    acCharge: ac_charges || null,
                    nonAcCharge: nonac_charges || null,
                    kmCharge: km_charges || null,
                    basicFare: basicFareKm || 0,
                    basicFareCharge: basicFareCharges || 0,
                    holdingMinute: holdingChargeMinute || 0,
                    holdingMinuteCharge: holdingCharges || 0,
                    perMinuteCharge: rideTimeFarePerMinute || 0,
                    startNightTime: startNightTime || null,
                    endNightTime: endNightTime || null,
                    nightCharge: nightFareCharge || 0,
                });
            }
            
            jQuery("#overlay").show();
            storeImageData().then(IMG => {
                database.collection('service').doc(id).update({
                    'title': titles,
                    'offerRate': offerRate,
                    'image': IMG,
                    'markerIcon': markerIcon,
                    'enable': enable,
                    'intercityType': intercityType,
                    'adminCommission': adminCommission,
                    'prices': prices
                }).then(function (result) {
                    jQuery("#overlay").hide();
                    window.location.href = '{{ route("services")}}';
                });
            }).catch(function (error) {
                showError(error);
            });
        }
    });

    function handleFileSelect(evt) {
        var f = evt.target.files[0];
        var reader = new FileReader();
        reader.onload = (function (theFile) {
            return function (e) {
                var filePayload = e.target.result;
                var val = f.name;
                var ext = val.split('.')[1];
                var docName = val.split('fakepath')[1];
                var filename = (f.name).replace(/C:\\fakepath\\/i, '')
                var timestamp = Number(new Date());
                var filename = filename.split('.')[0] + "_" + timestamp + '.' + ext;
                photo = filePayload;
                fileName = filename;
                $(".service_image").empty();
                $(".service_image").append('<span class="image-item" ><span class="remove-btn"><i class="fa fa-remove"></i></span><img class="rounded" style="width:50px" src="' + filePayload + '" alt="image"></span>');
            };
        })(f);
        reader.readAsDataURL(f);
    }

    $(document).on('click', '.remove-btn', function () {
        $(".image-item").remove();
        $('#service_image').val('');
    });
    
    async function fetchLanguages() {
        const languagesRef=database.collection('languages').where('isDeleted','==',false);
        const snapshot = await languagesRef.get();
        const languages=[];
        snapshot.forEach(doc => {
            languages.push(doc.data());
        });
        return languages;
    }

    async function fetchZones() {
        const zoneRef = database.collection('zone').where('publish','==',true);
        const snapshot = await zoneRef.get();
        const zones = [];
        snapshot.forEach(doc => {
            zones.push(doc.data());
        });
        return zones;
    }
    
    function createLanguageTabs(languages) {
        const tabsContainer=document.getElementById('language-tabs');
        const contentsContainer=document.getElementById('language-contents');
        tabsContainer.innerHTML='';
        contentsContainer.innerHTML='';
        const defaultLanguage=languages.find(language => language.isDefault);
        const otherLanguages=languages.filter(language => !language.isDefault);
        otherLanguages.sort((a,b) => a.name.localeCompare(b.name));
        const sortedLanguages=[defaultLanguage,...otherLanguages];
        sortedLanguages.forEach((language,index) => {
            var defaultClass='';
            if(language.isDefault){
                defaultClass='{{trans("lang.default")}}';
            }
            const tab=document.createElement('li');
            tab.classList.add('nav-item');
            tab.innerHTML=`
            <a class="nav-link ${index===0? 'active':''}" id="tab-${language.code}" data-bs-toggle="tab" href="#content-${language.code}" role="tab" aria-selected="${index===0}">
                ${language.name} (${language.code.toUpperCase()})
                <span class="badge badge-success ml-2">${defaultClass}</span>
            </a>
        `;
            tabsContainer.appendChild(tab);
            const content=document.createElement('div');
            content.classList.add('tab-pane','fade');
            if(index===0) {
                content.classList.add('show','active');
            }
            content.id=`content-${language.code}`; // Ensure this matches the tab link's href
            content.role="tabpanel";
            content.innerHTML=`
            <div class="form-group row width-100">
                <label class="col-3 control-label" for="service-title-${language.code}">{{trans('lang.service_title')}} (${language.code.toUpperCase()})<span class="required-field"></span></label>
                <div class="col-7">
                    <input type="text" class="form-control" id="service-title-${language.code}">
                    <div class="form-text text-muted">{{ trans("lang.service_title_help") }}</div>
                </div>                             
            </div>
        `;
            contentsContainer.appendChild(content);
        });
        const triggerTabList=document.querySelectorAll('#language-tabs a');
        triggerTabList.forEach(tab => {
            tab.addEventListener('click',function(event) {
                event.preventDefault();
                document.querySelectorAll('#language-contents .tab-pane').forEach(function(pane) {
                    pane.classList.remove('active','show');
                });
                document.querySelectorAll('#language-tabs .nav-link').forEach(function(navTab) {
                    navTab.classList.remove('active');
                });
                this.classList.add('active');
                const target=this.getAttribute('href');
                const targetPane=document.querySelector(target);
                if(targetPane) {
                    targetPane.classList.add('active','show');
                }
            });
        });
    }

    function createZoneTabs(zones) {
        
        const tabsContainer=document.getElementById('zone-tabs');
        const contentsContainer=document.getElementById('zone-contents');
        tabsContainer.innerHTML='';
        contentsContainer.innerHTML='';
        
        zones.forEach((zone,index) => {
            const zoneData = zone.name.find(item => item.type === langData.code);

            const tab=document.createElement('li');
            tab.classList.add('nav-item');
            tab.innerHTML=
            `<a class="nav-link ${index===0? 'active':''}" id="tab-${zone.id}" data-zone-id="${zone.id}" data-bs-toggle="tab" href="#content-${zone.id}" role="tab" aria-selected="${index===0}">
                ${zoneData.name} (${langData.code.toUpperCase()})
            </a>`;
            tabsContainer.appendChild(tab);

            const content=document.createElement('div');
            content.classList.add('tab-pane','fade');
            if(index===0) {
                content.classList.add('show','active');
            }
            content.id=`content-${zone.id}`;
            content.role="tabpanel";
            content.innerHTML=`
                <fieldset>
                    <legend>{{ trans('lang.basic_fare') }} {{ trans('lang.settings') }}</legend>
                    <div class="form-group row width-100">
                            <label class="col-4 control-label">{{ trans('lang.enter_basic_km') }} <span class="global_basic_label"></span><span
                                        class="required-field"></span></label>
                            <div class="col-7">
                                <input type="number" class="form-control basic_fare_km_${zone.id}">
                                <div class="form-text text-muted">{{ trans('lang.basic_fare_help') }}</div>
                            </div>
                    </div>
                    <div class="form-group row width-100">
                        <label class="col-4 control-label">{{ trans('lang.basic_fare_amount') }}<span
                                    class="required-field"></span></label>
                        <div class="col-7">
                            <div class="control-inner">
                                    <input type="number" class="form-control basic_fare_charges_${zone.id} currency_input">
                                    <span class="currentCurrency"></span>
                                    <div class="form-text text-muted">
                                    {{ trans('lang.basic_fare_amount') }}
                                    </div>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <fieldset>
                    <legend>{{ trans('lang.ac_nonac') }} {{ trans('lang.settings') }}</legend>
                    <div class="form-group row width-100">
                    <div class="form-check">
                            <input type="checkbox" class="is_ac_non_ac_${zone.id}" id="is_ac_non_ac_${zone.id}" onclick="acNonAcDiv('${zone.id}')">
                            <label class="col-3 control-label" for="is_ac_non_ac_${zone.id}">{{trans('lang.is_ac_non_ac')}}</label>
                        </div>
                    </div>
                    <div class="show_ac_non_ac_div_${zone.id} d-none">
                        <div class="form-group row width-100">
                            <label class="col-3 control-label ">{{trans('lang.max_ac_charges')}}<span class="global_basic_label"></span><span
                            class="required-field"></span></label>
                            <div class="col-7">
                                <div class="control-inner">
                                        <input type="number" class="form-control ac_charges_${zone.id} currency_input" min="0">
                                        <span class="currentCurrency"></span>
                                        <div class="form-text text-muted">
                                        {{ trans('lang.ac_charges_help') }}
                                        </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row width-100">
                            <label class="col-3 control-label">{{trans('lang.max_nonac_charges')}}<span class="global_basic_label"></span><span
                            class="required-field"></span></label>
                            <div class="col-7">
                                <div class="control-inner">
                                    <input type="number" class="form-control nonac_charges_${zone.id} currency_input" min="0">
                                    <span class="currentCurrency"></span>
                                    <div class="form-text text-muted">
                                    {{ trans('lang.nonac_charges_help') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row width-100 km_charges_div_${zone.id}">
                            <label class="col-3 control-label">{{ trans('lang.max_per_km') }} <span class="global_basic_label"></span><span
                            class="required-field"></span></label>
                            <div class="col-7">
                                <div class="control-inner">
                                    <input type="number" class="form-control km_charges_${zone.id} currency_input" min="0">
                                    <span class="currentCurrency"></span>
                                    <div class="form-text text-muted">
                                        {{ trans('lang.max_per_km_help') }}
                                    </div>
                                </div>
                            </div>
                    </div>
                </fieldset>
                <fieldset>
                    <legend>{{ trans('lang.holding_charge_details') }}</legend>
                    <div class="form-group row width-100">
                            <label class="col-4 control-label">{{ trans('lang.holding_charge_minute') }}<span
                                        class="required-field"></span></label>
                            <div class="col-7">
                                <input type="number" class="form-control holding_charge_minute_${zone.id}"
                                        id="holding_charge_minute_${zone.id}">
                                <div class="form-text text-muted">
                                    {{ trans('lang.holding_charge_minute_help') }}
                                </div>
                            </div>
                        </div>
                        <div class="form-group row width-100">
                            <label class="col-4 control-label">{{ trans('lang.holding_charges') }}<span
                                        class="required-field"></span></label>
                            <div class="col-7">
                                <div class="control-inner">
                                    <input type="number" class="form-control holding_charges_${zone.id} currency_input" id="holding_charges_${zone.id}">
                                    <span class="currentCurrency"></span>
                                    <div class="form-text text-muted">
                                        {{ trans('lang.holding_charges_help') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                </fieldset>
                <fieldset>
                    <legend>{{ trans('lang.ride_time_fare_details') }}</legend>
                    <div class="form-group row width-100">
                            <label class="col-4 control-label">{{ trans('lang.ride_time_fare_per_minute') }}<span class="required-field"></span></label>
                            <div class="col-7">
                                <div class="control-inner">
                                    <input type="number" class="form-control ride_time_fare_per_minute_${zone.id} currency_input"
                                        id="ride_time_fare_per_minute_${zone.id}">
                                        <span class="currentCurrency"></span>
                                        <div class="form-text text-muted">
                                            {{ trans('lang.ride_time_fare_per_minute_help') }}
                                        </div>
                                </div>
                            </div>
                        </div>
                </fieldset>
                <fieldset>
                    <legend>{{ trans('lang.night_fare_details') }}</legend>
                    <div class="form-group row width-50">
                            <label class="col-4 control-label">{{ trans('lang.start_night_time') }}<span class="required-field"></span></label>
                            <div class="col-7">
                                <input type="time" class="form-control start_night_time_${zone.id}"
                                        id="start_night_time_${zone.id}">
                                <div class="form-text text-muted">
                                    {{ trans('lang.start_night_time_help') }}
                                </div>
                            </div>
                        </div>
                        <div class="form-group row width-50">
                            <label class="col-4 control-label">{{ trans('lang.end_night_time') }}<span class="required-field"></span></label>
                            <div class="col-7">
                                <input type="time" class="form-control end_night_time_${zone.id}"
                                        id="end_night_time_${zone.id}">
                                <div class="form-text text-muted">
                                    {{ trans('lang.end_night_time_help') }}
                                </div>
                            </div>
                        </div>
                        <div class="form-group row width-100">
                            <label class="col-4 control-label">{{ trans('lang.night_time_fare') }}<span class="required-field"></span></label>
                            <div class="col-7">
                                <div class="control-inner">
                                    <select class="form-control night_time_fare_${zone.id}" id="night_time_fare_${zone.id}">
                                        <option value="0">Default(0)</option>
                                        <option value="1.5">1.5x</option>
                                        <option value="2">2x</option>
                                        <option value="2.5">2.5x</option>
                                        <option value="3">3x</option>
                                    </select>
                                    <div class="form-text text-muted">
                                        {{ trans('lang.night_time_fare_help') }} {{trans('lang.for_ride_fare')}}
                                        {{ trans('lang.night_fare_desc')}}
                                    </div>
                                </div>
                            </div>
                        </div>
                </fieldset>
            `;
            contentsContainer.appendChild(content);

            if (data.prices && Array.isArray(data.prices)) {
                const price = data.prices.find(p => p.zoneId === zone.id);
                if (price) {
                    $(`.basic_fare_km_${zone.id}`).val(price.basicFare || "");
                    $(`.basic_fare_charges_${zone.id}`).val(price.basicFareCharge || "");
                    $(`.km_charges_${zone.id}`).val(price.kmCharge || "");
                    $(`.ac_charges_${zone.id}`).val(price.acCharge || "");
                    $(`.nonac_charges_${zone.id}`).val(price.nonAcCharge || "");
                    $(`.holding_charge_minute_${zone.id}`).val(price.holdingMinute || "");
                    $(`.holding_charges_${zone.id}`).val(price.holdingMinuteCharge || "");
                    $(`.ride_time_fare_per_minute_${zone.id}`).val(price.perMinuteCharge || "");
                    $(`.start_night_time_${zone.id}`).val(price.startNightTime || "");
                    $(`.end_night_time_${zone.id}`).val(price.endNightTime || "");
                    $(`.night_time_fare_${zone.id}`).val(price.nightCharge || "0");
                    const $chk = $(`.is_ac_non_ac_${zone.id}`);
                    $chk.prop("checked", !!price.isAcNonAc).trigger("change");
                    acNonAcDiv(zone.id);
                }
            }
        });

        const triggerTabList=document.querySelectorAll('#zone-tabs a');
        triggerTabList.forEach(tab => {
            tab.addEventListener('click',function(event) {
                event.preventDefault();
                document.querySelectorAll('#zone-contents .tab-pane').forEach(function(pane) {
                    pane.classList.remove('active','show');
                });
                document.querySelectorAll('#zone-tabs .nav-link').forEach(function(navTab) {
                    navTab.classList.remove('active');
                });
                this.classList.add('active');
                const target=this.getAttribute('href');
                const targetPane=document.querySelector(target);
                if(targetPane) {
                    targetPane.classList.add('active','show');
                }
            });
        });
    }

</script>

@endsection