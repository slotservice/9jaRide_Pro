@extends('layouts.app')

@section('content')

<div class="page-wrapper">

    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">9ja-Assist Types</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item active">9ja-Assist Types</li>
            </ol>
        </div>
    </div>

    <div class="container-fluid">
        <div class="admin-top-section">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex top-title-section pb-4 justify-content-between">
                        <div class="d-flex top-title-left align-self-center">
                            <span class="icon mr-3"><i class="mdi mdi-wheelchair-accessibility" style="font-size:26px"></i></span>
                            <h3 class="mb-0">9ja-Assist Types</h3>
                            <span class="counter ml-3 total_count"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card border">
                    <div class="card-header border-0">
                        <h3 class="text-dark-2 mb-2 h4">Assistance options riders can request</h3>
                        <p class="mb-0 text-dark-2">These appear as a checklist when a rider chooses 9ja-Assist while booking. Add, rename, enable/disable or remove them here — changes reflect in the app with no update needed.</p>
                    </div>
                    <div class="card-body">

                        <div class="row mb-4">
                            <div class="col-md-8">
                                <div class="input-group">
                                    <input type="text" id="new_title" class="form-control" placeholder="e.g. Wheelchair (Foldable)" maxlength="80">
                                    <div class="input-group-append">
                                        <button type="button" id="add_btn" class="btn btn-primary">Add type</button>
                                    </div>
                                </div>
                                <small class="text-muted">Press Add to create a new assistance option.</small>
                            </div>
                        </div>

                        <div id="overlay" style="display:none;">
                            <div class="overlay__inner"><div class="overlay__content"><span class="spinner"></span></div></div>
                        </div>

                        <div class="table-responsive m-t-10">
                            <table class="table table-hover table-striped table-bordered" width="100%">
                                <thead>
                                    <tr>
                                        <th style="width:60px">Order</th>
                                        <th>Assistance type</th>
                                        <th style="width:120px">Status</th>
                                        <th style="width:160px">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="assist_list"></tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script type="text/javascript">
    var database = firebase.firestore();
    var ref = database.collection('assistance_type');

    function esc(s){ return (s==null?'':(''+s)).replace(/[&<>"']/g, function(c){ return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]; }); }

    function loadList(){
        jQuery('#overlay').show();
        ref.get().then(function(snap){
            var rows = [];
            snap.forEach(function(d){ var v = d.data(); v._id = d.id; rows.push(v); });
            rows.sort(function(a,b){ return (a.order||0) - (b.order||0); });
            $('.total_count').html(rows.length);
            var html = '';
            if(rows.length === 0){
                html = '<tr><td colspan="4" class="text-center text-muted py-4">No assistance types yet. Add one above.</td></tr>';
            } else {
                rows.forEach(function(v){
                    var checked = (v.enable === true || v.enable === 'true') ? 'checked' : '';
                    html += '<tr>'
                        + '<td>' + (v.order != null ? v.order : '') + '</td>'
                        + '<td class="title-cell" data-id="' + v._id + '">' + esc(v.title) + '</td>'
                        + '<td><label class="switch mb-0"><input type="checkbox" class="toggle-enable" data-id="' + v._id + '" ' + checked + '><span class="slider round"></span></label></td>'
                        + '<td>'
                        + '<button class="btn btn-sm btn-outline-primary edit-btn" data-id="' + v._id + '" data-title="' + esc(v.title) + '">Edit</button> '
                        + '<button class="btn btn-sm btn-outline-danger delete-btn" data-id="' + v._id + '">Delete</button>'
                        + '</td>'
                        + '</tr>';
                });
            }
            $('#assist_list').html(html);
            jQuery('#overlay').hide();
        }).catch(function(err){
            jQuery('#overlay').hide();
            $('#assist_list').html('<tr><td colspan="4" class="text-danger">Could not load: ' + esc(err.message) + '</td></tr>');
        });
    }

    function nextOrder(cb){
        ref.get().then(function(snap){
            var max = 0;
            snap.forEach(function(d){ var o = d.data().order || 0; if(o > max) max = o; });
            cb(max + 1);
        });
    }

    $(document).ready(function(){
        loadList();

        $('#add_btn').on('click', function(){
            var title = $.trim($('#new_title').val());
            if(title === ''){ alert('Please enter a name for the assistance type.'); return; }
            jQuery('#overlay').show();
            nextOrder(function(order){
                var docRef = ref.doc();
                docRef.set({
                    id: docRef.id,
                    title: title,
                    enable: true,
                    order: order,
                    createdAt: firebase.firestore.Timestamp.now()
                }).then(function(){
                    $('#new_title').val('');
                    loadList();
                }).catch(function(err){ jQuery('#overlay').hide(); alert('Add failed: ' + err.message); });
            });
        });

        $('#new_title').on('keypress', function(e){ if(e.which === 13){ $('#add_btn').click(); } });

        $(document.body).on('change', '.toggle-enable', function(){
            var id = $(this).data('id');
            var val = $(this).is(':checked');
            ref.doc(id).update({ enable: val }).catch(function(err){ alert('Update failed: ' + err.message); loadList(); });
        });

        $(document.body).on('click', '.edit-btn', function(){
            var id = $(this).data('id');
            var current = $(this).data('title');
            var title = prompt('Rename assistance type:', current);
            if(title === null) return;
            title = $.trim(title);
            if(title === ''){ alert('Name cannot be empty.'); return; }
            jQuery('#overlay').show();
            ref.doc(id).update({ title: title }).then(loadList).catch(function(err){ jQuery('#overlay').hide(); alert('Rename failed: ' + err.message); });
        });

        $(document.body).on('click', '.delete-btn', function(){
            var id = $(this).data('id');
            if(!confirm('Remove this assistance type? Riders will no longer see it.')) return;
            jQuery('#overlay').show();
            ref.doc(id).delete().then(loadList).catch(function(err){ jQuery('#overlay').hide(); alert('Delete failed: ' + err.message); });
        });
    });
</script>
@endsection
