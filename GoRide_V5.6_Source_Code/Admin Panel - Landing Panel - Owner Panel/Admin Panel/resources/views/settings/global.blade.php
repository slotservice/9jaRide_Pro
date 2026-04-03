@extends('layouts.app')
@section('content')
<?php
$countries = file_get_contents(public_path('/json/countriesdata.json'));
$countries = json_decode($countries);
$countries = (array) $countries;
$newcountries = [];
$newcountriesjs = [];
foreach ($countries as $keycountry => $valuecountry) {
    $newcountries[$valuecountry->code] = $valuecountry;
    $newcountriesjs[$valuecountry->countryName] = $valuecountry->code;
}
?>
<style>
span .img-flag {
    max-width: 25px;
}
</style>
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.app_setting_global')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.app_setting_global')}}</li>
            </ol>
        </div>
    </div>
   <div class="container-fluid">
        <div class="card">
    <div class="card-body">
        <div id="data-table_processing" class="dataTables_processing panel panel-default" style="display: none;">{{trans('lang.processing')}}</div>
        <div class="error_top" style="display:none"></div>
        <div class="row restaurant_payout_create">
            <div class="restaurant_payout_create-inner">
                <fieldset>
                    <legend>{{trans('lang.general_settings')}}</legend>
                    <div class="form-group row width-100">
                        <label class="col-3 control-label">{{trans('lang.app_version')}}<span class="required-field"></span></label>
                        <div class="col-7">
                            <input type="text" class="form-control" name="app_version" id="app_version">
                        </div>
                    </div>
                    <div class="form-group row width-100">
                        <label class="col-3 control-label">{{trans('lang.owner_panel_url')}}<span class="required-field"></span></label>
                        <div class="col-7">
                            <input type="text" class="form-control" name="owner_panel_url" id="owner_panel_url">
                        </div>
                    </div>
                    <div class="form-group row width-50">
                        <label class="col-3 control-label">{{trans('lang.app_logo')}}<span class="required-field"></span></label>
                        <div class="col-7">
                            <input type="file" onChange="handleLogoFileSelect(event)" class="form-control image" id="appLogo">
                            <div class="placeholder_img_thumb app_logo_image"></div>
                        </div>
                    </div>
                    <div class="form-group row width-50">
                        <label class="col-3 control-label">{{trans('lang.app_favicon_logo')}}<span class="required-field"></span></label>
                        <div class="col-7">
                            <input type="file" onChange="handleFavIconFileSelect(event)" class="form-control image" id="faviconLogo">
                            <div class="placeholder_img_thumb app_favicon_logo_image"></div>
                        </div>
                    </div>
                    <div class="form-group row width-50">
                        <label class="col-3 control-label">{{trans('lang.owner_panel_logo')}}<span class="required-field"></span></label>
                        <div class="col-7">
                            <input type="file" onChange="handleOwnerFileSelect(event)" class="form-control image" id="ownerLogo">
                            <div class="placeholder_img_thumb app_owner_logo_image"></div>
                        </div>
                    </div>
                    <div class="form-group row width-50">
                        <label class="col-5 control-label">{{ trans('lang.owner_panel_color') }}</label>
                        <input type="color" class="ml-3" name="owner_panel_color" id="owner_panel_color">
                    </div>
                    <div class="form-group row width-50">
                        <label class="col-5 control-label">{{ trans('lang.app_customer_dark_color_settings') }}</label>
                        <input type="color" class="ml-3" name="customer_app_color" id="customer_app_color">
                    </div>
                    <div class="form-group row width-50">
                        <label class="col-5 control-label">{{ trans('lang.app_customer_light_color_settings') }}</label>
                        <input type="color" class="ml-3" name="customer_app_light_color" id="customer_app_light_color">
                    </div>
                    <div class="form-group row width-50">
                        <label class="col-5 control-label">{{ trans('lang.app_driver_dark_color_settings') }}</label>
                        <input type="color" class="ml-3" name="driver_app_color" id="driver_app_color">
                    </div>
                    <div class="form-group row width-50">
                        <label class="col-5 control-label">{{ trans('lang.app_driver_light_color_settings') }}</label>
                        <input type="color" class="ml-3" name="driver_app_light_color" id="driver_app_light_color">
                    </div>
                    <div class="form-group row width-50">
                        <label class="col-5 control-label">{{ trans('lang.app_owner_dark_color_settings') }}</label>
                        <input type="color" class="ml-3" name="owner_app_color" id="owner_app_color">
                    </div>
                    <div class="form-group row width-50">
                        <label class="col-5 control-label">{{ trans('lang.app_owner_light_color_settings') }}</label>
                        <input type="color" class="ml-3" name="owner_app_light_color" id="owner_app_light_color">
                    </div>
                   <div class="form-group row width-50">
                        <label class="col-3 control-label">{{ trans('lang.default_country') }}<span
                                class="required-field"></span></label>
                        <div class="col-7">
                            <select name="country" id="country" class="form-control defaultCountryCode">
                                @foreach ($countries_data as $country)
                                    <option value="{{ $country->countryName }}"
                                        data-code="{{ $country->code }}"
                                        data-phonecode="+{{ $country->phoneCode }}">
                                        {{ $country->countryName }}+({{ $country->phoneCode }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text text-muted">
                                {{ trans('lang.default_country_help') }}
                            </div>
                        </div>
                    </div>
                </fieldset>
                <fieldset>
                    <legend>{{trans('lang.google_map_api_key_title')}}</legend>
                    <div class="form-group row width-100">
                        <label class="col-3 control-label">{{trans('lang.google_map_api_key')}}</label>
                        <div class="col-7">
                            <input type="password" class="form-control" name="map_key" id="map_key" autocomplete="new-password">
                        </div>
                    </div>
                </fieldset>
                <fieldset>
                    <legend>{{trans('lang.document_verification_setting')}}</legend>
                    <div class="form-group row width-100">
                        <div class="form-check width-100">
                        <div class="col-7">
                            <input type="checkbox" class="form-check-inline"  id="driver_document_verification">
                            <label class="col-3 control-label" for="driver_document_verification">{{trans('lang.enable_driver_document_verification')}}</label>
                        </div>
                        </div>
                    </div>
                    <div class="form-group row width-100">
                        <div class="form-check width-100">
                        <div class="col-7">
                            <input type="checkbox" class="form-check-inline"  id="owner_document_verification">
                            <label class="col-3 control-label" for="owner_document_verification">{{trans('lang.enable_owner_document_verification')}}</label>
                        </div>
                        </div>
                    </div>
                </fieldset>
                <fieldset>
                    <legend>{{trans('lang.wallet_settings')}}</legend>
                    <div class="form-group row width-100">
                        <label class="col-4 control-label">{{ trans('lang.minimum_deposit_amount')}}</label>
                        <div class="col-7">
                            <div class="control-inner">
                                <input type="number" class="form-control minimum_deposit_amount">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row width-100">
                        <label class="col-4 control-label">{{ trans('lang.minimum_withdrawal_amount')}}</label>
                        <div class="col-7">
                            <div class="control-inner">
                                <input type="number" class="form-control minimum_withdrawal_amount">
                            </div>
                        </div>
                    </div>
                </fieldset>
                <fieldset>
                    <legend>{{trans('lang.referral_settings')}}</legend>
                    <div class="form-group row width-100">
                        <label class="col-4 control-label">{{ trans('lang.referral_amount_for_customer')}}</label>
                        <div class="col-7">
                            <div class="control-inner">
                                <input type="number" class="form-control referral_amount_customer referral_amount">
                                <span class="currentCurrency"></span>
                                <div class="form-text text-muted">
                                    {{ trans("lang.referral_amount_help") }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row width-100">
                        <label class="col-4 control-label">{{ trans('lang.referral_amount_for_driver')}}</label>
                        <div class="col-7">
                            <div class="control-inner">
                                <input type="number" class="form-control referral_amount_driver referral_amount">
                                <span class="currentCurrency"></span>
                                <div class="form-text text-muted">
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <fieldset>
                    <legend>{{trans('lang.delivery_distance')}}</legend>
                    <div class="form-group row width-100">
                    <label class="col-4 control-label">{{trans('lang.distance')}}</label>
                    <div class="col-7">
                        <select name="delivery_distance" id="delivery_distance" class="form-control">
                            <option value="Km">{{trans('lang.km')}}</option>
                            <option value="Miles">{{trans('lang.miles')}}</option>
                        </select>
                    </div>
                    </div>
                    <div class="form-group row width-100">
                        <label class="col-4 control-label">{{trans('lang.radius')}}</label>
                        <div class="col-7">
                            <input name="radius" id="radius" class="form-control">
                        </div>
                    </div>
                </fieldset>
                <fieldset>
                    <legend>{{trans('lang.surge_zone_settings')}}</legend>
                    <div class="form-group row width-100">
                        <label class="col-4 control-label">{{trans('lang.surge_zone_radius')}}</label>
                        <div class="col-7">
                            <input name="surge_zone_radius" id="surge_zone_radius" class="form-control" type="number">
                             <div class="form-text text-muted">
                                {{ trans("lang.surge_zone_radius_help") }}
                            </div>
                        </div>
                    </div>
                    <div class="form-group row width-100">
                        <label class="col-4 control-label">{{trans('lang.surge_zone_ride_count')}}</label>
                        <div class="col-7">
                            <input name="surge_zone_ride_count" id="surge_zone_ride_count" class="form-control" type="number">
                             <div class="form-text text-muted">
                                {{ trans("lang.surge_zone_ride_count_help") }}
                            </div>
                        </div>
                    </div>
                    <div class="form-group row width-100">
                        <label class="col-4 control-label">{{trans('lang.surge_zone_selection_minutes')}}</label>
                        <div class="col-7">
                            <input name="surge_zone_selection_minutes" id="surge_zone_selection_minutes" class="form-control" type="number">
                             <div class="form-text text-muted">
                                {{ trans("lang.surge_zone_selection_minutes_help") }}
                            </div>
                        </div>
                    </div>
                    <div class="form-group row width-100">
                        <label class="col-4 control-label">{{trans('lang.surge_zone_duration_minutes')}}</label>
                        <div class="col-7">
                            <input name="surge_zone_duration_minutes" id="surge_zone_duration_minutes" class="form-control" type="number">
                             <div class="form-text text-muted">
                                {{ trans("lang.surge_zone_duration_minutes_help") }}
                            </div>
                        </div>
                    </div>
                    <div class="form-group row width-100">
                        <label class="col-4 control-label">{{trans('lang.surge_zone_surge_percentage')}}</label>
                        <div class="col-7">
                            <input name="surge_zone_surge_percentage" id="surge_zone_surge_percentage" class="form-control" type="number">
                             <div class="form-text text-muted">
                                {{ trans("lang.surge_zone_surge_percentage_help") }}
                            </div>
                        </div>
                    </div>
                </fieldset>
                <fieldset>
                    <legend>{{trans('lang.map_redirection')}}</legend>
                    <div class="form-group row width-100">
                        <label class="col-4 control-label">{{trans('lang.select_map_type_for_application')}}</label>
                            <div class="col-7">
                                <select name="selectedMapType" id="selectedMapType"
                                    class="form-control selectedMapType">
                                    <option value="google">{{trans("lang.google_maps")}}</option>
                                    <option value="osm">{{trans("lang.open_street_map")}}</option>
                                </select>
                            </div>
                            <div class="form-text pl-3 text-muted">
                                <span><strong>{{trans("lang.note")}} :</strong>
                                    {{trans("lang.google_map_note")}}<br>
                                    {{trans("lang.open_street_map_note")}}<br>
                                    <strong>{{trans("lang.recommended_note")}}</strong></span>
                            </div>
                    </div>
                    <div class="form-group row width-100">
                        <label class="col-4 control-label">{{trans('lang.select_map_type')}}</label>
                        <div class="col-7">
                            <select name="map_type" id="map_type" class="form-control map_type">
                                <option value="">{{trans("lang.select_type")}}</option>
                                <option value="google">{{trans("lang.google_map")}}</option>
                                <option value="googleGo">{{trans("lang.google_go_map")}}</option>
                                <option value="waze">{{trans("lang.waze_map")}}</option>
                                <option value="mapswithme">{{trans("lang.mapswithme_map")}}</option>
                                <option value="yandexNavi">{{trans("lang.vandexnavi_map")}}</option>
                                <option value="yandexMaps">{{trans("lang.vandex_map")}}</option>
                                <option value="inappmap">{{trans("lang.inapp_map")}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row width-100">
                        <label class="col-4 control-label">{{trans('lang.driver_location_update')}}</label>
                        <div class="col-7">
                            <input name="radius" id="driver_location_update" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row width-100">
                        <label class="col-3 control-label">{{trans('lang.place_picker')}}</label>
                        <div class="col-7">
                            <select name="region" id="region" class="form-control region">
                                <option value="all" region_country="all">{{ trans("lang.all") }}</option>
                                @foreach($countries_data as $country)
                                    <option value="{{$country->code}}" region_country="{{$country->countryName}}">{{$country->countryName}}
                                        ({{$country->code}})
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text text-muted">
                                {{ trans("lang.place_picker_help") }}
                            </div>
                        </div>
                    </div>
                </fieldset>
                <fieldset>
                    <legend>{{trans('lang.contact_us')}}</legend>
                    <div class="form-group row width-100">
                        <label class="col-3 control-label">{{trans('lang.email_subject')}}<span class="required-field"></span></label>
                        <div class="col-7">
                            <input type="text" class="form-control" name="contact_us_subject" id="contact_us_subject">
                        </div>
                    </div>
                    <div class="form-group row width-50">
                        <label class="col-4 control-label">{{trans('lang.email')}}<span class="required-field"></span></label>
                        <div class="col-7">
                            <input type="text" name="contact_us_email" id="contact_us_email" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row width-50">
                        <label class="col-4 control-label">{{trans('lang.contact_us_phone')}}<span class="required-field"></span></label>
                        <div class="col-7">
                            <input type="text" name="contact_us_phone_number" id="contact_us_phone_number" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row width-100">
                        <label class="col-4 control-label">{{trans('lang.address')}}<span class="required-field"></span></label>
                        <div class="col-7">
                            <textarea name="contact_us_address" id="contact_us_address" class="form-control"></textarea>
                        </div>
                    </div>
                     <div class="form-group row width-100">
                        <label class="col-4 control-label">{{trans('lang.support_url')}}<span class="required-field"></span></label>
                        <div class="col-7">
                            <input type="text" name="support_url" id="support_url" class="form-control">
                        </div>
                    </div>
                </fieldset>
                <fieldset>
                    <legend><i class="mr-3 mdi mdi-comment-alert"></i>{{trans('lang.notification_setting')}}</legend>
                    <div class="form-group row width-100">
                        <label class="col-5 control-label">{{trans('lang.sender_id')}}</label>
                        <div class="col-7">
                            <input type="text" class="form-control" id="sender_id">
                        </div>
                        <div class="form-text pl-3 text-muted">
                            {{ trans("lang.notification_sender_id_help") }}
                        </div>
                    </div>
                    <div class="form-group row width-100">
                        <label class="col-3 control-label">{{trans('lang.upload_json_file')}}</label>
                        <input type="file" class="col-7 pb-2" onChange="handleUploadJsonFile(event)">
                        <div id="uploding_json_file"></div>
                        <div id="uploded_json_file" class="pl-3"></div>
                        <div class="form-text pl-3 text-muted">
                            {{ trans("lang.notification_json_file_help") }}
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
    <div class="form-group col-12 text-center btm-btn">
        <button type="button" class="btn btn-primary edit-setting-btn"><i class="fa fa-save"></i> {{trans('lang.save')}}</button>
        <a href="{{url('/dashboard')}}" class="btn btn-default"><i class="fa fa-undo"></i>{{trans('lang.cancel')}}
        </a>
    </div>
</div>
</div>
</div>
@endsection
@section('scripts')
<script>
    var app_logo_image = '';
    var app_owner_logo_image = '';
    var app_favicon_logo_image = '';
    var appLogoImagePath = '';
    var ownerLogoImagePath = '';
    var appFavIconImagePath = '';
    var logoFileName = '';
    var favIconFileName = '';
    var ownerFileName = '';
    var serviceJsonFile = '';
    var storageRef = firebase.storage().ref('images');
    var storage = firebase.storage();
    var database = firebase.firestore();
    var globalKey = database.collection('settings').doc("globalKey");
    var referralAmountRef = database.collection('settings').doc("referral");
    var globalValue = database.collection('settings').doc("globalValue");
    var contactUsRef = database.collection('settings').doc("contact_us");
    var logoRef = database.collection('settings').doc("logo");
    var refNotificationSetting = database.collection('settings').doc("notification_setting");
    // var numberSetting = database.collection('settings').doc("numberSetting");
    var global = database.collection('settings').doc("global");
    var refCurrency = database.collection('currency').where('enable', '==', true);
    refCurrency.get().then(async function(snapshots) {
        var currencyData = snapshots.docs[0].data();
        $(".currentCurrency").text(currencyData.symbol);
    });
     var newcountriesjs = '<?php echo json_encode($newcountriesjs); ?>';
    var newcountriesjs = JSON.parse(newcountriesjs);

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
 jQuery("#country").select2({
            templateResult: formatState,

            templateSelection: formatState2,


            placeholder: "{{ trans('lang.select_country') }}",

            allowClear: true

        });

    $(document).ready(function() {
        jQuery("#overlay").show();
        jQuery("#region").select2({
        placeholder: "{{trans('lang.select_country')}}",
        allowClear: true
        });
        globalKey.get().then(async function(snapshots) {
            var globalKeyData = snapshots.data();
            try {
                if (globalKeyData.googleMapKey) {
                    $("#map_key").val(globalKeyData.googleMapKey);
                }
            } catch (error) {
            }
        })
        global.get().then(async function(snapshots) {
            var globalSetting = snapshots.data();
            if (globalSetting.appVersion) {
                $("#app_version").val(globalSetting.appVersion);
            }
            if (globalSetting.ownerPanelUrl) {
                $("#owner_panel_url").val(globalSetting.ownerPanelUrl);
            }
            jQuery("#overlay").hide();
        })
        referralAmountRef.get().then(async function(snapshots) {
            var referralAmountData = snapshots.data();
            if (referralAmountData == undefined) {
                database.collection('settings').doc('referral').set({});
            }
            try {
                $(".referral_amount_customer").val(referralAmountData.referralAmount);
                $(".referral_amount_driver").val(referralAmountData.referralAmountDriver);
            } catch (error) {
            }
            jQuery("#overlay").hide();
        })
        globalValue.get().then( async function(snapshots){
            var globalValueSettings = snapshots.data();
            if(globalValueSettings == undefined){
                database.collection('settings').doc('globalValue').set({});
            }else{
                if(globalValueSettings.app_customer_color){
                    $("#customer_app_color").val(globalValueSettings.app_customer_color);
                }
                if(globalValueSettings.app_driver_color){
                    $("#driver_app_color").val(globalValueSettings.app_driver_color);
                }
                if(globalValueSettings.app_customer_light_color){
                    $("#customer_app_light_color").val(globalValueSettings.app_customer_light_color);
                }
                if(globalValueSettings.app_driver_light_color){
                    $("#driver_app_light_color").val(globalValueSettings.app_driver_light_color);
                }
                if(globalValueSettings.app_owner_color){
                    $("#owner_app_color").val(globalValueSettings.app_owner_color);
                }
                if(globalValueSettings.app_owner_light_color){
                    $("#owner_app_light_color").val(globalValueSettings.app_owner_light_color);
                }
                if(globalValueSettings.ownerPanelColor){
                    $("#owner_panel_color").val(globalValueSettings.ownerPanelColor);
                }
              if(globalValueSettings.distanceType){
                $("#delivery_distance").val(globalValueSettings.distanceType);
              }
              if(globalValueSettings.radius){
                $('#radius').val(globalValueSettings.radius);
              }
              if(globalValueSettings.surgeZoneRadius){
                $('#surge_zone_radius').val(globalValueSettings.surgeZoneRadius);
              }
              if(globalValueSettings.surgeZoneRideCount){
                $('#surge_zone_ride_count').val(globalValueSettings.surgeZoneRideCount);
              }
              if(globalValueSettings.surgeZoneSelectionMinutes){
                $('#surge_zone_selection_minutes').val(globalValueSettings.surgeZoneSelectionMinutes);
              }
              if(globalValueSettings.surgeZoneDurationMinutes){
                $('#surge_zone_duration_minutes').val(globalValueSettings.surgeZoneDurationMinutes);
              }
              if(globalValueSettings.surgeZoneSurgePercentage){
                $('#surge_zone_surge_percentage').val(globalValueSettings.surgeZoneSurgePercentage);
              }
              if (globalValueSettings.minimumDepositToRideAccept) {
                    $(".minimum_deposit_amount").val(globalValueSettings.minimumDepositToRideAccept);    
                }
                if (globalValueSettings.minimumAmountToWithdrawal) {
                    $(".minimum_withdrawal_amount").val(globalValueSettings.minimumAmountToWithdrawal);
                }
                if (globalValueSettings.mapType) {
                    $('#map_type').val(globalValueSettings.mapType).trigger('change');
                }
                if (globalValueSettings.selectedMapType) {
                    $('#selectedMapType').val(globalValueSettings.selectedMapType).trigger('change');
                }
                if (globalValueSettings.driverLocationUpdate) {
                    $('#driver_location_update').val(globalValueSettings.driverLocationUpdate);
                }
                if(globalValueSettings.isVerifyDocument) {
                    $('#driver_document_verification').prop('checked',true);
                }
                if(globalValueSettings.isVerifyOwnerDocument) {
                    $('#owner_document_verification').prop('checked',true);
                }
                if(globalValueSettings.regionCode){
                    $('#region').val(globalValueSettings.regionCode).trigger('change');
                }
                 if (globalValueSettings.defaultCountryCode) {
                    $('.defaultCountryCode').val(globalValueSettings.defaultCountryCode).trigger('change');

                    if ($('.defaultCountryCode').val() == null) {
                        var selectedCountry = $('.defaultCountryCode option').filter(function() {
                            return $(this).data('code') ===   globalValueSettings.defaultCountryCode;
                        }).first().val();
                        if (selectedCountry) {
                            $('.defaultCountryCode').val(selectedCountry).trigger('change');
                        }
                    }
                }
            }
            jQuery("#overlay").hide();
        })
        contactUsRef.get().then(async function(contactusSnap) {
            var contactData = contactusSnap.data();
            $("#contact_us_subject").val(contactData.subject);
            $("#contact_us_email").val(shortEmail(contactData.email));
            if(contactData.phone.includes('+')){
                contactData.phone = contactData.phone.slice(1);
            }
            else
            {
                contactData.phone = contactData.phone;
            }
            $("#contact_us_phone_number").val('+'+EditPhoneNumber(contactData.phone));
            $("#contact_us_address").text(contactData.address);
            $("#support_url").val(contactData.supportURL);
        });
        logoRef.get().then(async function(snapshots) {
            var logoRefData = snapshots.data();
            if (logoRefData == undefined) {
                database.collection('settings').doc('logo').set({});
            }
            try {
                if(logoRefData.appLogo){
                    app_logo_image = logoRefData.appLogo;
                    appLogoImagePath = logoRefData.appLogo;
                    $(".app_logo_image").append('<span class="image-item"><span class="remove-btn" data-val="app_logo"><i class="fa fa-remove"></i></span><img class="rounded" style="width:50px" src="' + logoRefData.appLogo + '" alt="image"></span>');
                }
                if(logoRefData.appFavIconLogo) {
                    app_favicon_logo_image = logoRefData.appFavIconLogo;
                    appFavIconImagePath = logoRefData.appFavIconLogo;
                    $(".app_favicon_logo_image").append('<span class="image-item"><span class="remove-btn" data-val="app_favicon_logo"><i class="fa fa-remove"></i></span><img class="rounded" style="width:50px" src="' + logoRefData.appFavIconLogo + '" alt="image"></span>');
                }
                if(logoRefData.ownerPanelLogo){
                    app_owner_logo_image = logoRefData.ownerPanelLogo;
                    ownerLogoImagePath = logoRefData.ownerPanelLogo;
                    $(".app_owner_logo_image").append('<span class="image-item"><span class="remove-btn" data-val="app_logo"><i class="fa fa-remove"></i></span><img class="rounded" style="width:50px" src="' + logoRefData.ownerPanelLogo + '" alt="image"></span>');
                }
            } catch (error) {
            }
            jQuery("#overlay").hide();
        })
        async function storeImageData() {
            var newPhoto = [];
            try {
                if(appLogoImagePath != "" && app_logo_image != appLogoImagePath){
                    var appLogoImagePathRef = await storage.refFromURL(appLogoImagePath);
                    imageBucket = appLogoImagePathRef.bucket;
                    var envBucket = "<?php echo env('FIREBASE_STORAGE_BUCKET'); ?>";
                    if (imageBucket == envBucket) {
                        await appLogoImagePathRef.delete().then(() => {
                            console.log("Old file deleted!")
                        }).catch((error) => {
                            console.log("ERR File delete ===", error);
                        });
                    } else {
                        console.log('Bucket not matched');
                    }
                }
                if(app_logo_image != appLogoImagePath){
                    app_logo_image = app_logo_image.replace(/^data:image\/[a-z]+;base64,/, "")
                    var uploadTask = await storageRef.child(logoFileName).putString(app_logo_image, 'base64', {contentType:'image/jpg'});
                    var downloadURL = await uploadTask.ref.getDownloadURL();
                    newPhoto['app_logo_image'] = downloadURL;
                    app_logo_image = downloadURL;
                }else{
                    newPhoto['app_logo_image'] = app_logo_image;
                }
                if(appFavIconImagePath != "" && app_favicon_logo_image != appFavIconImagePath){
                    var appFavIconImagePathRef = await storage.refFromURL(appFavIconImagePath);
                    imageBucket = appFavIconImagePathRef.bucket;
                    var envBucket = "<?php echo env('FIREBASE_STORAGE_BUCKET'); ?>";
                    if (imageBucket == envBucket) {
                    await appFavIconImagePathRef.delete().then(() => {
                        console.log("Old file deleted!")
                    }).catch((error) => {
                        console.log("ERR File delete ===", error);
                    });
                }else{
                    console.log("Bucket not matched!")
                }
                }
                if(app_favicon_logo_image != appFavIconImagePath){
                    app_favicon_logo_image = app_favicon_logo_image.replace(/^data:image\/[a-z]+;base64,/, "")
                    var uploadTask = await storageRef.child(favIconFileName).putString(app_favicon_logo_image, 'base64', {contentType:'image/jpg'});
                    var downloadURL = await uploadTask.ref.getDownloadURL();
                    newPhoto['app_favicon_logo_image'] = downloadURL;
                    app_favicon_logo_image = downloadURL;
                }else{
                    newPhoto['app_favicon_logo_image'] = app_favicon_logo_image;
                }

                if(ownerLogoImagePath != "" && app_owner_logo_image != ownerLogoImagePath){
                    var ownerLogoImagePathRef = await storage.refFromURL(ownerLogoImagePath);
                    imageBucket = ownerLogoImagePathRef.bucket;
                    var envBucket = "<?php echo env('FIREBASE_STORAGE_BUCKET'); ?>";
                    if (imageBucket == envBucket) {
                        await ownerLogoImagePathRef.delete().then(() => {
                            console.log("Old file deleted!")
                        }).catch((error) => {
                            console.log("ERR File delete ===", error);
                        });
                    } else {
                        console.log('Bucket not matched');
                    }
                }
                if(app_owner_logo_image != ownerLogoImagePath){
                    app_owner_logo_image = app_owner_logo_image.replace(/^data:image\/[a-z]+;base64,/, "")
                    var uploadTask = await storageRef.child(ownerFileName).putString(app_owner_logo_image, 'base64', {contentType:'image/jpg'});
                    var downloadURL = await uploadTask.ref.getDownloadURL();
                    newPhoto['app_owner_logo_image'] = downloadURL;
                    app_owner_logo_image = downloadURL;
                }else{
                    newPhoto['app_owner_logo_image'] = app_owner_logo_image;
                }
            } catch (error) {
                console.log("ERR ===", error);
            }
            return newPhoto;
        }
        refNotificationSetting.get().then(async function (snapshots) {
            var notificationData = snapshots.data();
            if (notificationData == undefined) {
                database.collection('settings').doc('notification_setting').set({});
            }else{
                if(notificationData.senderId != '' && notificationData.senderId != null){
                    $('#sender_id').val(notificationData.senderId);
                }
                if(notificationData.serviceJson != '' && notificationData.serviceJson != null){
                    $('#uploded_json_file').html("File Uploaded");
                    serviceJsonFile = notificationData.serviceJson;
                }else{
                    $('#uploded_json_file').html("File Not Uploaded");
                }
            }
        });
        $(".edit-setting-btn").click(function() {
            var mapKey = $("#map_key").val();
            var app_version = $("#app_version").val();
            var owner_panel_url = $("#owner_panel_url").val();
            var referralAmountCustomer = $(".referral_amount_customer").val();
            var referralAmountDriver = $(".referral_amount_driver").val();
            var distance = $("#delivery_distance :selected").val();
            var radius=$('#radius').val();
            var subject  = $("#contact_us_subject").val();
            var email = $("#contact_us_email").val();
            var phone = $("#contact_us_phone_number").val();
            var address =$("#contact_us_address").text();
            var supportURL=$('#support_url').val();
            var minimumDepositToRideAccept = $(".minimum_deposit_amount").val();
            var minimumAmountToWithdrawal = $(".minimum_withdrawal_amount").val();
            var selectedMapType = $("#selectedMapType").val();
            var map_type = $('#map_type').val();
            var driver_location_update = $('#driver_location_update').val();
            var senderId = $("#sender_id").val();
            var regionCode = $('#region').val();
            var custAppLightColor = $('#customer_app_light_color').val();
            var custAppColor = $('#customer_app_color').val();
            var driverAppLightColor = $('#driver_app_light_color').val();
            var driverAppColor = $('#driver_app_color').val();
            var ownerAppColor = $('#owner_app_color').val();
            var ownerAppLightColor = $('#owner_app_light_color').val();
            var ownerPanelColor = $('#owner_panel_color').val();
            var regionCountry = $('#region option:selected').attr('region_country');
            var isVerifyDocument = $("#driver_document_verification").is(':checked');
            var isVerifyOwnerDocument = $("#owner_document_verification").is(':checked');
            var defaultCountryCode = $('.defaultCountryCode').find(':selected').data('code');  // This is the ISO2 code like "US"

            var surge_zone_radius=$('#surge_zone_radius').val();
            var surge_zone_ride_count=$('#surge_zone_ride_count').val();
            var surge_zone_selection_minutes=$('#surge_zone_selection_minutes').val();
            var surge_zone_duration_minutes=$('#surge_zone_duration_minutes').val();
            var surge_zone_surge_percentage=$('#surge_zone_surge_percentage').val();
            
            if (app_version == '') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.app_version_error')}}</p>");
                window.scrollTo(0, 0);
            } else if(owner_panel_url == ''){
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.owner_panel_url_error')}}</p>");
                window.scrollTo(0, 0);
            }else if (referralAmountCustomer == '') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.enter_referral_amount_customer_error')}}</p>");
                window.scrollTo(0, 0);
            }else if (referralAmountDriver == '') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.enter_referral_amount_driver_error')}}</p>");
                window.scrollTo(0, 0);
            } else if (subject == '') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.please_enter_subject')}}</p>");
                window.scrollTo(0, 0);
            }else if (defaultCountryCode == '') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>Please enter default country</p>");
                window.scrollTo(0, 0);
            } else if (email == '') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.contact_us_email_help')}}</p>");
                window.scrollTo(0, 0);
            } else if (phone == '') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.contact_us_phone_help')}}</p>");
                window.scrollTo(0, 0);
            } else if (address == '') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.contact_us_address_help')}}</p>");
                window.scrollTo(0, 0);
            } else if (supportURL == '') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.support_url_help')}}</p>");
                window.scrollTo(0, 0);
            }else if (app_owner_logo_image == '') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.app_owner_logo_image_help')}}</p>");
                window.scrollTo(0, 0);
            }else if (app_logo_image == '') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.app_logo_image_help')}}</p>");
                window.scrollTo(0, 0);
            }  else if (app_favicon_logo_image == '') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.app_favicon_logo_image_help')}}</p>");
                window.scrollTo(0, 0);
            }else if (minimumDepositToRideAccept == '') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.enter_minimum_deposit_amount_error')}}</p>");
                window.scrollTo(0, 0);
            }else if (minimumAmountToWithdrawal == '') {
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.enter_minimum_withdrawal_amount_error')}}</p>");
                window.scrollTo(0, 0);
            }else if(senderId == ''){
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.notification_sender_id_error')}}</p>");
                window.scrollTo(0, 0);
            }else if(serviceJsonFile == ''){
                $(".error_top").show();
                $(".error_top").html("");
                $(".error_top").append("<p>{{trans('lang.notification_service_json_error')}}</p>");
                window.scrollTo(0, 0);
            }else {
                jQuery("#overlay").show();
                storeImageData().then(IMG => {
                    database.collection('settings').doc('global').update({
                        'appVersion': app_version,
                        'ownerPanelUrl': owner_panel_url,
                    });
                    database.collection('settings').doc("logo").update({
                        'appLogo': IMG.app_logo_image,
                        'appFavIconLogo': IMG.app_favicon_logo_image,
                        'ownerPanelLogo': IMG.app_owner_logo_image
                    });
                    database.collection('settings').doc("globalKey").update({
                        'googleMapKey': mapKey,
                    });
                    database.collection('settings').doc("referral").update({
                        'referralAmount': referralAmountCustomer,
                        'referralAmountDriver': referralAmountDriver,
                    });
                    
                    database.collection('settings').doc("globalValue").update({
                        'distanceType': distance,
                        'radius':radius,
                        'minimumDepositToRideAccept': minimumDepositToRideAccept,
                        'minimumAmountToWithdrawal': minimumAmountToWithdrawal,
                        'selectedMapType':selectedMapType,
                        'mapType': map_type,
                        'driverLocationUpdate': driver_location_update,
                        'isVerifyDocument':isVerifyDocument,
                        'isVerifyOwnerDocument':isVerifyOwnerDocument,
                        'regionCode':regionCode,
                        'regionCountry':regionCountry,
                        'app_customer_color': custAppColor,
                        'app_customer_light_color': custAppLightColor,
                        'app_driver_color': driverAppColor,
                        'app_driver_light_color': driverAppLightColor,
                        'app_owner_color': ownerAppColor,
                        'app_owner_light_color': ownerAppLightColor,
                        'ownerPanelColor': ownerPanelColor,
                        'defaultCountryCode': defaultCountryCode,
                        'surgeZoneRadius':surge_zone_radius,
                        'surgeZoneRideCount':surge_zone_ride_count,
                        'surgeZoneSelectionMinutes':surge_zone_selection_minutes,
                        'surgeZoneDurationMinutes':surge_zone_duration_minutes,
                        'surgeZoneSurgePercentage':surge_zone_surge_percentage,
                    });
                    database.collection('settings').doc("notification_setting").update({
                        'senderId': senderId,
                        'serviceJson': serviceJsonFile,
                    });
                    database.collection('settings').doc("contact_us").update({
                        'subject': subject,
                        'email':email,
                        'phone':phone,
                        'address': address,
                        'supportURL':supportURL,
                    }).then(function(result) {
                        window.location.href = '{{ url("settings/globals")}}';
                    })
                }).catch(err => {
                    jQuery("#overlay").hide();
                    $(".error_top").show();
                    $(".error_top").html("");
                    $(".error_top").append("<p>" + err + "</p>");
                    window.scrollTo(0, 0);
                });
            }
        })
    })
    function handleLogoFileSelect(evt) {
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
                app_logo_image = filePayload;
                logoFileName = filename;
                $(".app_logo_image").empty();
                $(".app_logo_image").append('<span class="image-item"><span class="remove-btn" data-val="app_logo"><i class="fa fa-remove"></i></span><img class="rounded" style="width:50px" src="' + filePayload + '" alt="image"></span>');
            };
        })(f);
        reader.readAsDataURL(f);
    }
    function handleOwnerFileSelect(evt) {
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
                app_owner_logo_image = filePayload;
                ownerFileName = filename;
                $(".app_owner_logo_image").empty();
                $(".app_owner_logo_image").append('<span class="image-item"><span class="remove-btn" data-val="owner_app_logo"><i class="fa fa-remove"></i></span><img class="rounded" style="width:50px" src="' + filePayload + '" alt="image"></span>');
            };
        })(f);
        reader.readAsDataURL(f);
    }
    function handleFavIconFileSelect(evt) {
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
                app_favicon_logo_image = filePayload;
                favIconFileName = filename;
                $(".app_favicon_logo_image").empty();
                $(".app_favicon_logo_image").append('<span class="image-item"><span class="remove-btn" data-val="app_favicon_logo"><i class="fa fa-remove"></i></span><img class="rounded" style="width:50px" src="' + filePayload + '" alt="image"></span>');
            };
        })(f);
        reader.readAsDataURL(f);
    }
    function handleUploadJsonFile(evt) {
        var f = evt.target.files[0];
        var reader = new FileReader();
        reader.onload = (function (theFile) {
            return function (e) {
                var filePayload = e.target.result;
                var hash = CryptoJS.SHA256(Math.random() + CryptoJS.SHA256(filePayload));
                var val = f.name;
                var ext = val.split('.')[1];
                var docName = val.split('fakepath')[1];
                var filename = (f.name).replace(/C:\\fakepath\\/i, '')
                var timestamp = Number(new Date());
                var filename = filename.split('.')[0] + "_" + timestamp + '.' + ext;
                var uploadTask = firebase.storage().ref('/').child(filename).put(theFile);
                uploadTask.on('state_changed', function (snapshot) {
                    var progress = (snapshot.bytesTransferred / snapshot.totalBytes) * 100;
                    jQuery("#uploding_json_file").text("{{trans('lang.file_is_uploading')}}");
                }, function (error) {
                }, function () {
                    uploadTask.snapshot.ref.getDownloadURL().then(function (downloadURL) {
                        jQuery("#uploding_json_file").text("{{trans('lang.upload_is_completed')}}");
                        serviceJsonFile = downloadURL;
                        setTimeout(function(){
                            jQuery("#uploding_json_file").hide();
                        },3000);
                    });
                });
            };
        })(f);
        reader.readAsDataURL(f);
    }
    $(document).on('click', '.remove-btn', function(){
        if($(this).attr('data-val') == "app_logo"){
            $(".app_logo_image").empty();
            app_logo_image = '';
            logoFileName = '';
        }else if($(this).attr('data-val') == "owner_app_logo"){
            $(".app_owner_logo_image").empty();
            app_owner_logo_image = '';
            ownerFileName = '';
        }else{
            $(".app_favicon_logo_image").empty();
            app_favicon_logo_image = '';
            favIconFileName = '';
        }
    }); 
</script>
@endsection
