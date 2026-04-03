@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <div class="row page-titles">

        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor restaurantTitle">{{trans('lang.owner_document_details')}}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/dashboard')}}">{{trans('lang.dashboard')}}</a></li>
                <li class="breadcrumb-item"><a href="{!! route('owners') !!}">{{trans('lang.owner_plural')}}</a>
                </li>
                <li class="breadcrumb-item active">{{trans('lang.owner_document_details')}}</li>
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
                                <a class="nav-link active owner-name"
                                    href="{!! url()->current() !!}">{{trans('lang.owner_document_details')}}</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div id="data-table_processing" class="dataTables_processing panel panel-default"
                            style="display: none;">{{trans('lang.processing')}}
                        </div>

                        <div class="table-responsive m-t-10 doc-body"></div>
                        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document" style="max-width: 50%;">
                                <div class="modal-content">

                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>

                                    <div class="modal-body">
                                        <div class="form-group">
                                            <embed id="docImage" src="" frameBorder="0" scrolling="auto" height="100%"
                                                width="100%" style="height: 540px;"></embed>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-dismiss="modal">{{trans('lang.close')}}</button>
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

@section('scripts')z

<script>

    var id="<?php echo $id; ?>";
    var database=firebase.firestore();
    var ref=database.collection('owner_users').where("id","==",id);
    var storageRef=firebase.storage().ref('images');
    var storage=firebase.storage();
    var placeholderImage="{{ asset('/images/default_user.png') }}";
    var owner_details="{{trans('lang.owner_details')}}";
    var notFound="{{ trans('lang.doc_not_found') }}";
    var docsRef=database.collection('documents').where('enable','==',true).where('type','==','owner');
    var docref=database.collection('driver_document').doc(id);
    
    var requestUrl="{{request()->is('owners/document/*')}}";
    var back_photo='';
    var front_photo='';
    var backFileName='';
    var frontFileName='';
    var backFileOld='';
    var frontFileOld='';
    var setLanguageCode=getCookie('setLanguage');
    var defaultLanguageCode=getCookie('defaultLanguage');
    var fcmToken="";

    $(document).ready(async function() {
        jQuery("#overlay").show();
        $('#exampleModal').on('show.bs.modal',function(event) {
            var button=$(event.relatedTarget);
            var img=button.data('image');
            var modal=$(this);
            modal.find('#docImage').attr('src',img);
        });
        ref.get().then(async function(snapshots) {
            var owner=snapshots.docs[0].data();

            if(owner.hasOwnProperty('fcmToken')&&owner.fcmToken!=""&&owner.fcmToken!=null) {
                fcmToken=owner.fcmToken;
            }
            $(".owner-name").text(owner.fullName+"'s"+" {{trans('lang.owner_document_details')}}")

        });
        var html='';

        var count=0;

        await docsRef.get().then(async function(docSnapshot) {
            html+='<table id="taxTable" class="display nowrap table table-hover table-striped table-bordered table table-striped" cellspacing="0" width="100%">';
            html+="<thead>";
            html+='<tr>';
            html+='<th>{{trans("lang.name")}}</th>';
            html+='<th>{{trans("lang.status")}}</th>';
            html+='<th>{{trans("lang.actions")}}</th>';
            html+='</tr>';
            html+='</thead>';
            html+='<tbody>';
            html+='</tbody>';
            html+='</table>';

            if(docSnapshot.docs.length) {
                var documents=docSnapshot.docs;

                documents.forEach((ele) => {
                    var doc=ele.data();
                    var docRefs=database.collection('driver_document').doc(id);
                    docRefs.get().then(async function(docrefSnapshot) {

                        var docRef=docrefSnapshot.data()&&docrefSnapshot.data().documents? docrefSnapshot.data().documents.filter(docId => docId.documentId==doc.id)[0]:[];

                        var trhtml='';
                        trhtml+='<tr>';
                        var document_name='';
                        if(docRef&&docRef.hasOwnProperty('documentNumber')) {
                            document_name=docRef.documentNumber;
                        }
                       
                        var title='';
                        if(Array.isArray(doc.title)) {
                            var foundItem=doc.title.find(item => item.type===setLanguageCode);
                            if(foundItem&&foundItem.title!='') {
                                title=foundItem.title;
                            } else {
                                var foundItem=doc.title.find(item => item.type===defaultLanguageCode);
                                if(foundItem&&foundItem.title!='') {
                                    title=foundItem.title;
                                } else {
                                    var foundItem=doc.title.find(item => item.type==='en');
                                    title=foundItem.title;
                                }
                            }

                        }

                        if(docRef&&docRef.hasOwnProperty('backImage')&&docRef.hasOwnProperty('frontImage')) {
                            if(docRef.backImage!=''&&docRef.frontImage!='') {
                                trhtml+='<td>'+title+'&nbsp;&nbsp;<a href="#" class="badge badge-info" data-toggle="modal" data-target="#exampleModal" data-image="'+docRef.frontImage+'" data-id="front" class="open-image">Front</a>&nbsp;<a href="#" class="badge badge-info" data-toggle="modal" data-target="#exampleModal"  data-image="'+docRef.backImage+'" data-id="back" class="open-image">Back</a></td>';
                            } else if(docRef.backImage!='') {
                                trhtml+='<td>'+title+'&nbsp;<a href="#" data-toggle="modal" class="badge badge-info" data-target="#exampleModal" data-id="back" data-image="'+docRef.backImage+'" class="open-image">Back</a></td>';
                            } else if(docRef.frontImage!='') {
                                trhtml+='<td>'+title+'&nbsp;<a href="#" data-toggle="modal" class="badge badge-info" data-target="#exampleModal" data-id="front" class="open-image" data-image="'+docRef.frontImage+'">Front</a></td>';
                            } else {
                                trhtml+='<td>'+title+'</td>';

                            }
                        } else {
                            trhtml+='<td>'+title+'</td>';
                        }

                        var status=docRef&&docRef.verified&&docRef.status=="Approved"? 'Approved':((docRef&&docRef.verified==false&&docRef.status=="DisApproved")? "DisApproved":((typeof docRef=="object"&&docRef.length==0)||docRef==undefined? 'Not Uploaded':'Not Approved'));

                        var display_status='';

                        if(status=="Approved") {
                            display_status='<span class="badge badge-success py-2 px-3">'+status+'</span>';
                        } else if(status=="DisApproved") {
                            display_status='<span class="badge badge-danger py-2 px-3">'+status+'</span>';

                        } else if(status=="Not Approved") {
                            display_status='<span class="badge badge-primary py-2 px-3">'+status+'</span>';

                        } else if(status=="Not Uploaded") {
                            display_status='<span class="badge badge-warning py-2 px-3">'+status+'</span>';

                        }

                        trhtml+='<td>'+display_status+'</td>';
                        trhtml+='<td class="action-btn">';
                        trhtml+='<a href="'+(`/owners/document/upload/`+id.trim()+`/`+doc.id.trim())+'" data-id="'+doc.id+'"><i class="fa fa-edit"></i></a>&nbsp;';

                        if(status!=='Not Uploaded') {

                            if(status=="DisApproved") {
                                trhtml+='&nbsp;<a href="javascript:void(0);" class="btn btn-sm btn-success verify-doc" id="approve-doc" data-title="'+title+'" data-name="'+document_name+'" data-id="'+doc.id+'">{{trans('lang.approve')}}</a>';

                            } else if(status=="Approved") {
                                trhtml+='&nbsp;<a href="javascript:void(0);" class="btn btn-sm btn-danger verify-doc" id="disapprove-doc" data-title="'+title+'" data-name="'+document_name+'" data-id="'+doc.id+'">{{trans('lang.disapprove')}}</a>';

                            } else {
                                trhtml+='&nbsp;<a href="javascript:void(0);" class="btn btn-sm btn-success verify-doc" id="approve-doc" data-title="'+title+'" data-name="'+document_name+'" data-id="'+doc.id+'">{{trans('lang.approve')}}</a>&nbsp;<a href="javascript:void(0);" class="btn btn-sm btn-danger verify-doc" id="disapprove-doc" data-title="'+title+'" data-name="'+document_name+'" data-id="'+doc.id+'">{{trans('lang.disapprove')}}</a>';

                            }
                        }
                        trhtml+='</td>';
                        trhtml+='</tr>';
                        $("tbody").append(trhtml);

                        count=count+1;

                        if(count==docSnapshot.docs.length) {
                            $('#taxTable').DataTable({
                                order: [[0,'asc']],
                                columnDefs: [
                                    {orderable: false,targets: [1,2]}
                                ],
                                "language": datatableLang,
                            });

                        }
                    })
                });
            }
            $(".doc-body").append(html);
            jQuery("#overlay").hide();

        });

        $('.owner_sub_menu li').each(function() {
            var url=$(this).find('a').attr('href');
            if(url==document.referrer) {
                $(this).find('a').addClass('active');
                $('.owner_menu').addClass('active').attr('aria-expanded',true);
            }
            $('.owner_sub_menu').addClass('in').attr('aria-expanded',true);
        });

    });

    $(document.body).on('click','.redirecttopage',function() {
        var url=$(this).attr('data-url');
        window.location.href=url;
    });

    $(document).on('click','.verify-doc',function() {

        jQuery("#overlay").show();
        var verified=$(this).attr('id')=="approve-doc"? true:false;
        var status=$(this).attr('id')=="approve-doc"? "Approved":"DisApproved";
        var docId=$(this).attr('data-id');
        var docTitle=$(this).attr('data-title');
        var docName=$(this).attr('data-name');
      
        var docRefsTmp=database.collection('driver_document').doc(id);
        docRefsTmp.get().then(async function(docrefSnapshot) {
            var keydataId=docrefSnapshot.data()&&docrefSnapshot.data().documents? docrefSnapshot.data().documents.findIndex((doc) => doc.documentId==docId):0;
            database.collection('driver_document').doc(id)
                .get().then((doc) => {
                    var objects=doc.data().documents;
                    var objectToupdate=objects[keydataId];
                    objectToupdate.verified=verified;
                    objectToupdate.status=status;
                    objects[keydataId]=objectToupdate;
                    database.collection('driver_document').doc(id).update({
                        documents: objects
                    }).then(async function() {
                        var enableDocIds=await getDocId();
                        await ref.get().then(async function(snapshotsowner) {
                            if(snapshotsowner.docs.length>0) {
                                var verification=await ownerDocVerification(enableDocIds,snapshotsowner);
                                if(verification) {
                                    if(status=="DisApproved") {
                                        var sendNotificationStatus=await sendNotification(fcmToken,'Disapproved of your document','Admin is disapproved your '+docTitle+' ('+docName+'). Please submit again.');
                                        if(sendNotificationStatus) {
                                            jQuery("#overlay").hide();
                                            window.location.reload();
                                        }
                                    } else {
                                        jQuery("#overlay").hide();
                                        window.location.reload();
                                    }
                                }
                            } else {
                                jQuery("#overlay").hide();
                                window.location.reload();
                            }
                        })
                        $('li').removeClass('active');
                        $("#documents-tab").addClass('active');
                        $("#documents-tab").click();
                        $(".error_top").html("");
                    }).catch(function(error) {
                        $(".error_top").show();
                        $(".error_top").html("");
                        $(".error_top").append("<p>"+error+"</p>");
                    });
                })
        });

    });
    async function getDocId() {
        var enableDocIds=[];
        await database.collection('documents').where('isDeleted','==',false).where('enable',"==",true).where('type','==','owner').get().then(async function(snapshots) {
            await snapshots.forEach((doc) => {
                enableDocIds.push(doc.data().id);
            });
        });
        return enableDocIds;
    }
    async function ownerDocVerification(enableDocIds,snapshotsowner) {
        var isCompleted=false;
        await Promise.all(snapshotsowner.docs.map(async (owner) => {
            await database.collection('driver_document').doc(owner.id).get().then(async function(docrefSnapshot) {
                if(docrefSnapshot.data()&&docrefSnapshot.data().documents.length>0) {
                    var ownerDocId=await docrefSnapshot.data().documents.filter((doc) => doc.verified==true).map((docData) => docData.documentId);
                    if(ownerDocId.length>=enableDocIds.length) {
                        await database.collection('owner_users').doc(owner.id).update({'documentVerification': true});
                    } else {
                        await enableDocIds.forEach(async (docId) => {
                            if(!ownerDocId.includes(docId)) {
                                await database.collection('owner_users').doc(owner.id).update({'documentVerification': false});
                            }
                        });
                    }
                } else {
                    await database.collection('owner_users').doc(owner.id).update({'documentVerification': false});
                }
            });
            isCompleted=true;
        }));
        return isCompleted;
    }
</script>
@endsection