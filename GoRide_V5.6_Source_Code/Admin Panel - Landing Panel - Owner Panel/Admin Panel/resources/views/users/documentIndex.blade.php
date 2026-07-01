@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor user-name-heading">{{trans('lang.user_details')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item"><a href="{{url('/users')}}">{{trans('lang.user_plural')}}</a></li>
                <li class="breadcrumb-item active">KYC Documents</li>
            </ol>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs align-items-end card-header-tabs w-100">
                            <li class="nav-item">
                                <a class="nav-link active user-name-tab" href="{!! url()->current() !!}">KYC Documents</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div id="data-table_processing" class="dataTables_processing panel panel-default" style="display: none;">{{trans('lang.processing')}}</div>
                        <div class="table-responsive m-t-10 doc-body"></div>

                        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document" style="max-width: 50%;">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <embed id="docImage" src="" frameBorder="0" scrolling="auto" height="100%" width="100%" style="height: 540px;"></embed>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{trans('lang.close')}}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
    var userRef = database.collection('users').doc(id);
    var docsRef = database.collection('documents').where('enable', '==', true).where('type', '==', 'customer').where('isDeleted', '==', false);
    var customerDocRef = database.collection('customer_document').doc(id);
    var setLanguageCode = getCookie('setLanguage');
    var defaultLanguageCode = getCookie('defaultLanguage');
    var fcmToken = "";

    $(document).ready(async function () {
        jQuery("#overlay").show();

        $('#exampleModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var img = button.data('image');
            var modal = $(this);
            modal.find('#docImage').attr('src', img);
        });

        userRef.get().then(function (snapshot) {
            if (snapshot.exists) {
                var user = snapshot.data();
                var name = user.fullName || 'User';
                $(".user-name-heading").text(name + "'s KYC Documents");
                $(".user-name-tab").text(name + "'s KYC Documents");
                if (user.fcmToken && user.fcmToken != "") {
                    fcmToken = user.fcmToken;
                }
            }
        });

        var html = '';
        var count = 0;

        await docsRef.get().then(async function (docSnapshot) {
            html += '<table id="kycTable" class="display nowrap table table-hover table-striped table-bordered table table-striped" cellspacing="0" width="100%">';
            html += "<thead><tr>";
            html += '<th>{{trans("lang.name")}}</th>';
            html += '<th>{{trans("lang.status")}}</th>';
            html += '<th>{{trans("lang.actions")}}</th>';
            html += "</tr></thead><tbody></tbody></table>";

            if (docSnapshot.docs.length) {
                docSnapshot.docs.forEach(function (ele) {
                    var doc = ele.data();

                    customerDocRef.get().then(function (docrefSnapshot) {
                        var docRef = docrefSnapshot.data() && docrefSnapshot.data().documents
                            ? docrefSnapshot.data().documents.filter(d => d.documentId == doc.id)[0]
                            : undefined;

                        var title = getDocTitle(doc);
                        var document_name = (docRef && docRef.documentNumber) ? docRef.documentNumber : '';

                        var trhtml = '<tr>';

                        // Name column with image links
                        if (docRef && docRef.frontImage && docRef.backImage && docRef.frontImage != '' && docRef.backImage != '') {
                            trhtml += '<td>' + title + '&nbsp;&nbsp;<a href="#" class="badge badge-info" data-toggle="modal" data-target="#exampleModal" data-image="' + docRef.frontImage + '">Front</a>&nbsp;<a href="#" class="badge badge-info" data-toggle="modal" data-target="#exampleModal" data-image="' + docRef.backImage + '">Back</a></td>';
                        } else if (docRef && docRef.backImage && docRef.backImage != '') {
                            trhtml += '<td>' + title + '&nbsp;<a href="#" class="badge badge-info" data-toggle="modal" data-target="#exampleModal" data-image="' + docRef.backImage + '">Back</a></td>';
                        } else if (docRef && docRef.frontImage && docRef.frontImage != '') {
                            trhtml += '<td>' + title + '&nbsp;<a href="#" class="badge badge-info" data-toggle="modal" data-target="#exampleModal" data-image="' + docRef.frontImage + '">Front</a></td>';
                        } else {
                            trhtml += '<td>' + title + '</td>';
                        }

                        // Status
                        var status = (docRef && docRef.verified && docRef.status == "Approved") ? 'Approved'
                            : ((docRef && docRef.verified == false && docRef.status == "DisApproved") ? "DisApproved"
                            : ((typeof docRef == "object" && docRef && Object.keys(docRef).length == 0) || docRef == undefined ? 'Not Uploaded' : 'Not Approved'));

                        var display_status = '';
                        if (status == "Approved") {
                            display_status = '<span class="badge badge-success py-2 px-3">' + status + '</span>';
                        } else if (status == "DisApproved") {
                            display_status = '<span class="badge badge-danger py-2 px-3">' + status + '</span>';
                        } else if (status == "Not Approved") {
                            display_status = '<span class="badge badge-primary py-2 px-3">' + status + '</span>';
                        } else {
                            display_status = '<span class="badge badge-warning py-2 px-3">' + status + '</span>';
                        }
                        trhtml += '<td>' + display_status + '</td>';

                        // Actions
                        trhtml += '<td class="action-btn">';
                        if (status != 'Not Uploaded') {
                            if (status == "DisApproved") {
                                trhtml += '<a href="javascript:void(0);" class="btn btn-sm btn-success verify-doc" id="approve-doc" data-name="' + document_name + '" data-id="' + doc.id + '">{{trans("lang.approve")}}</a>';
                            } else if (status == "Approved") {
                                trhtml += '<a href="javascript:void(0);" class="btn btn-sm btn-danger verify-doc" id="disapprove-doc" data-name="' + document_name + '" data-id="' + doc.id + '">{{trans("lang.disapprove")}}</a>';
                            } else {
                                trhtml += '<a href="javascript:void(0);" class="btn btn-sm btn-success verify-doc" id="approve-doc" data-name="' + document_name + '" data-id="' + doc.id + '">{{trans("lang.approve")}}</a>&nbsp;';
                                trhtml += '<a href="javascript:void(0);" class="btn btn-sm btn-danger verify-doc" id="disapprove-doc" data-name="' + document_name + '" data-id="' + doc.id + '">{{trans("lang.disapprove")}}</a>';
                            }
                        }
                        trhtml += '</td></tr>';
                        $("tbody").append(trhtml);

                        count++;
                        if (count == docSnapshot.docs.length) {
                            $('#kycTable').DataTable({
                                order: [[0, 'asc']],
                                columnDefs: [{ orderable: false, targets: [1, 2] }],
                                "language": datatableLang,
                            });
                        }
                    });
                });
            }

            $(".doc-body").append(html);
            jQuery("#overlay").hide();
        });
    });

    $(document).on('click', '.verify-doc', function () {
        jQuery("#overlay").show();
        var verified = $(this).attr('id') == "approve-doc" ? true : false;
        var status = $(this).attr('id') == "approve-doc" ? "Approved" : "DisApproved";
        var docId = $(this).attr('data-id');

        customerDocRef.get().then(async function (docrefSnapshot) {
            var keydataId = docrefSnapshot.data() && docrefSnapshot.data().documents
                ? docrefSnapshot.data().documents.findIndex(d => d.documentId == docId)
                : 0;

            customerDocRef.get().then(function (doc) {
                var objects = doc.data().documents;
                var objectToupdate = objects[keydataId];
                objectToupdate.verified = verified;
                objectToupdate.status = status;
                objects[keydataId] = objectToupdate;

                customerDocRef.update({ documents: objects }).then(async function () {
                    var enableDocIds = await getCustomerDocIds();
                    await updateUserDocVerification(enableDocIds);
                    jQuery("#overlay").hide();
                    window.location.reload();
                }).catch(function (error) {
                    jQuery("#overlay").hide();
                    alert(error);
                });
            });
        });
    });

    async function getCustomerDocIds() {
        var ids = [];
        await database.collection('documents')
            .where('isDeleted', '==', false)
            .where('enable', '==', true)
            .where('type', '==', 'customer')
            .get().then(function (snapshots) {
                snapshots.forEach(doc => ids.push(doc.data().id));
            });
        return ids;
    }

    async function updateUserDocVerification(enableDocIds) {
        await customerDocRef.get().then(async function (docrefSnapshot) {
            if (docrefSnapshot.data() && docrefSnapshot.data().documents && docrefSnapshot.data().documents.length > 0) {
                var approvedIds = docrefSnapshot.data().documents
                    .filter(d => d.verified == true)
                    .map(d => d.documentId);
                var allApproved = enableDocIds.length > 0 && approvedIds.length >= enableDocIds.length;
                await userRef.update({ documentVerification: allApproved });
            } else {
                await userRef.update({ documentVerification: false });
            }
        });
    }

    function getDocTitle(doc) {
        if (Array.isArray(doc.title)) {
            var found = doc.title.find(item => item.type === setLanguageCode);
            if (found && found.title) return found.title;
            found = doc.title.find(item => item.type === defaultLanguageCode);
            if (found && found.title) return found.title;
            found = doc.title.find(item => item.type === 'en');
            if (found && found.title) return found.title;
        }
        return doc.name || 'Document';
    }
</script>
@endsection
