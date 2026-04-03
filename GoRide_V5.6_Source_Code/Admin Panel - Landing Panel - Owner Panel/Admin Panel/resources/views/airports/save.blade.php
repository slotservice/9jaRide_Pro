@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{ trans('lang.airport_plural') }}</h3>
        </div>

        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ trans('lang.dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{!! route('airports') !!}">{{ trans('lang.airports') }}</a>
                </li>
                <li class="breadcrumb-item active">
                    {{ $id == '' ? trans('lang.airport_add') : trans('lang.airport_edit') }}
                </li>
            </ol>
        </div>
    </div>
    <div class="container-fluid">
        <div class="card pb-4">

            <div class="card-body">

                <div class="error_top"></div>

                <div class="row restaurant_payout_create">
                    <div class="restaurant_payout_create-inner">
                        <fieldset>
                            <legend>{{ trans('lang.airports_details') }}</legend>


                            <div class="form-group row width-100" id="add_ones_div">
                                <label class="col-3 control-label">{{trans('lang.airport_location')}}</label>
                                <div class="row">
                                    <div class="col-md-12 d-flex">
                                        <div class="col-12">

                                            <input type="text" class="form-control airport" id="airport"
                                                autocomplete="on">
                                            <div class="form-text text-muted">{{ trans("lang.airport_help") }} </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="form-group row width-50">
                                <div class="form-check">
                                    <input type="checkbox" class="service_active" id="active">
                                    <label class="col-3 control-label" for="active">{{ trans('lang.enable') }}</label>
                                </div>
                            </div>





                        </fieldset>
                    </div>
                </div>

                <div class="form-group col-12 text-center btm-btn">
                    <button type="button" class="btn btn-primary  edit-form-btn"><i class="fa fa-save"></i>
                        {{ trans('lang.save') }}</button>
                    <a href="{!! route('airports') !!}" class="btn btn-default"><i class="fa fa-undo"></i>{{
                        trans('lang.cancel') }}</a>
                </div>

            </div>

        </div>
    </div>

</div>
@endsection
@section('scripts')

<script type="text/javascript">

var database = firebase.firestore();
var requestId = "{{$id}}";
var id = (requestId == '') ? database.collection("tmp").doc().id : requestId;

var mapType = 'ONLINE';
var osmTimer = null;


// ================= PAGE LOAD =================
$(document).ready(async function () {

    $('.airport_menu').addClass('active');
    $("#data-table_processing").show();

    // Load map setting
    let settingRef = await database.collection('settings').doc('globalValue').get();
    var settingData = settingRef.data();
    if (settingData && settingData.selectedMapType === "osm") {
        mapType = "OFFLINE";
    }

    // Load existing data
    if (requestId != '') {

        database.collection('airports')
        .where('id', '==', requestId)
        .get()
        .then(function (snapshots) {
            if (snapshots.docs[0].data()) {
                var doc = snapshots.docs[0].data();
                if (doc.enable) {
                    $('.service_active').prop('checked', true);
                }
                $('#airport')
                .val(doc.airportName)
                .attr('lat', doc.airportLat)
                .attr('lng', doc.airportLng)
                .attr('city', doc.cityLocation)
                .attr('state', doc.state)
                .attr('country', doc.country);
            }
        });
    }

    // INIT AUTOCOMPLETE
    initializeAutocomplete("airport");

    $("#data-table_processing").hide();
});


// ================= MAIN AUTOCOMPLETE SWITCH =================
function initializeAutocomplete(inputId){
    if(mapType === "ONLINE"){
        initGoogleAutocomplete(inputId);
    }else{
        initOSMAutocomplete(inputId);
    }
}

// ================= GOOGLE AUTOCOMPLETE =================
function initGoogleAutocomplete(id){

    var input = document.getElementById(id);
    var autocomplete = new google.maps.places.Autocomplete(input);

    autocomplete.addListener('place_changed', function () {

        var place = autocomplete.getPlace();
        var comp = place.address_components || [];

        const findType = t =>
            (comp.find(c => c.types.includes(t)) || {}).long_name || '';

        $("#" + id)
            .val(place.name)
            .attr('lat', place.geometry.location.lat())
            .attr('lng', place.geometry.location.lng())
            .attr('city', findType('locality'))
            .attr('state', findType('administrative_area_level_1'))
            .attr('country', findType('country'));
    });
}

// ================= OSM AUTOCOMPLETE =================
function initOSMAutocomplete(id){

    var $input = $("#" + id);

    // create dropdown
    var dropdown = $('<div id="osm-list" style="position:absolute;z-index:9999;background:#fff;border:1px solid #ddd;width:100%;"></div>');
    $input.after(dropdown);

    $input.on("keyup", function(){

        clearTimeout(osmTimer);
        let query = $(this).val();

        if(query.length < 3){
            dropdown.empty();
            return;
        }

        osmTimer = setTimeout(async function(){

            let res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&addressdetails=1&q=${query}`);
            let data = await res.json();

            dropdown.empty();

            data.forEach(d => {

                let item = $(`<div style="padding:8px;cursor:pointer;border-bottom:1px solid #eee;">${d.display_name}</div>`);

                item.on("click", function(){

                    $input.val(d.display_name)
                        .attr('lat', d.lat)
                        .attr('lng', d.lon)
                        .attr('city', d.address.city || d.address.town || '')
                        .attr('state', d.address.state || '')
                        .attr('country', d.address.country || '');

                    dropdown.empty();
                });

                dropdown.append(item);
            });

        }, 400);
    });

    // close dropdown on outside click
    $(document).on("click", function(e){
        if(!$(e.target).closest("#osm-list, #"+id).length){
            dropdown.empty();
        }
    });
}

// ================= SAVE =================
$(document).on('click', '.edit-form-btn', function () {

    var airportName = $('.airport').val();
    var airportLat = $('.airport').attr('lat');
    var airportLng = $('.airport').attr('lng');
    var cityLocation = $('.airport').attr('city');
    var state = $('.airport').attr('state');
    var country = $('.airport').attr('country');

    $('.error_top').html("");

    if (airportName == "") {
        window.scroll(0, 0);
        $('.error_top').show();
        $('.error_top').html("<p>{{trans('lang.enter_airport_location')}}</p>");
        return false;
    }

    var enable = $(".service_active").is(':checked');

    $("#data-table_processing").show();

    database.collection('airports').doc(id).set({
        id: id,
        cityLocation: cityLocation,
        country: country,
        state: state,
        airportName: airportName,
        airportLat: airportLat,
        airportLng: airportLng,
        enable: enable,
        date: new Date(),
    }).then(function () {

        $("#data-table_processing").hide();
        window.location.href = '{{ url("airports")}}';

    });
});

</script>

@endsection