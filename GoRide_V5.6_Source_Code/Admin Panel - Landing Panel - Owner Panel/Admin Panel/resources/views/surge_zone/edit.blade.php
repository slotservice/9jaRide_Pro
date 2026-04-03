@extends('layouts.app')

@section('content')
<div class="page-wrapper">

    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{trans('lang.surge_zone_plural')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item"><a href="{!! route('surgezone') !!}">{{trans('lang.surge_zone_plural')}}</a></li>
                <li class="breadcrumb-item active">{{trans('lang.surge_zone_edit')}}</li>
            </ol>
        </div>
    </div>

    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs" id="language-tabs" role="tablist"></ul>
            </div>

            <div class="card-body">
                <div class="error_top" style="display:none"></div>

                <div class="row restaurant_payout_create">
                    <div class="restaurant_payout_create-inner">
                        <fieldset>
                            <legend>{{trans('lang.surge_zone_edit')}}</legend>

                            <div class="tab-content" id="language-contents"></div>

                            <div class="form-group row width-100 mt-2">
                                <label class="col-3 control-label">{{trans('lang.surge_multiplier')}}</label>
                                <div class="col-9">
                                    <input type="number" step="0.1" id="surgeMultiplier" class="form-control">
                                </div>
                            </div>

                        </fieldset>
                    </div>

                </div>

            </div>

            <div class="form-group col-12 text-center btm-btn mt-3">
                <button type="button" class="btn btn-primary edit-setting-btn">
                    <i class="fa fa-save"></i> {{trans('lang.save')}}
                </button>

                <a href="{!! route('surgezone') !!}" class="btn btn-default">
                    <i class="fa fa-undo"></i>{{trans('lang.cancel')}}
                </a>
            </div>

        </div>
    </div>
</div>
@endsection

@section('scripts')

<script>

var id="{{ $id }}";

var database = firebase.firestore();

var ref = database.collection('surge_zones').where("id", "==", id);

async function fetchLanguages() {
    const snapshot = await database.collection('languages').where('isDeleted', '==', false).get();
    const languages = [];
    snapshot.forEach(doc => languages.push(doc.data()));
    return languages;
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
        content.id=`content-${language.code}`;
        content.role="tabpanel";
        content.innerHTML=`
        <div class="form-group row width-100">
            <label class="col-3 control-label" for="zone-${language.code}">{{trans('lang.zone_name')}} (${language.code.toUpperCase()})<span class="required-field"></span></label>
            <div class="col-7">
                <input type="text" class="form-control" id="zone-name-${language.code}">
                <div class="form-text text-muted">{{ trans("lang.zone_name_help") }}</div>
            </div>                             
        </div>
    `;
        contentsContainer.appendChild(content);
    });

    const triggerTabList=document.querySelectorAll('#language-tabs a');
    triggerTabList.forEach(tab => {
        tab.addEventListener('click',function(event) {
            event.preventDefault();

            document.querySelectorAll('.tab-pane').forEach(function(pane) {
                pane.classList.remove('active','show');
            });

            document.querySelectorAll('.nav-link').forEach(function(navTab) {
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

$(document).ready(async function() {

    const languages = await fetchLanguages();

    createLanguageTabs(languages);

    const snapshot = await ref.get();
    if (!snapshot.empty) {
        const zone = snapshot.docs[0].data();
        if(zone.name && Array.isArray(zone.name)){
            zone.name.forEach(n => {
                const input = $(`#zone-name-${n.type}`);
                if(input.length) input.val(n.name);
            });
        }
        $("#surgeMultiplier").val(zone.surgeMultiplier || 1);
    }

    $(".edit-setting-btn").click(async function() {
        let names = [];
        $("[id^='zone-name-']").each(function() {
            names.push({ type: $(this).attr('id').replace('zone-name-', ''), name: $(this).val() });
        });
        const surgeMultiplier = parseFloat($("#surgeMultiplier").val()) || 1;
        // Save to Firebase
        jQuery("#overlay").show();
        await database.collection('surge_zones').doc(id).update({
            id,
            name: names,
            surgeMultiplier,
        });
        jQuery("#overlay").hide();
        window.location.href = '{{ route("surgezone") }}';
    });
});

</script>
@endsection
