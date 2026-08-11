import 'package:cloud_firestore/cloud_firestore.dart';

class DriverDocumentModel {
  List<Documents>? documents;
  String? id;

  DriverDocumentModel({this.documents, this.id});

  DriverDocumentModel.fromJson(Map<String, dynamic> json) {
    if (json['documents'] != null) {
      documents = <Documents>[];
      json['documents'].forEach((v) {
        documents!.add(Documents.fromJson(v));
      });
    }
    id = json['id'];
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = <String, dynamic>{};
    if (documents != null) {
      data['documents'] = documents!.map((v) => v.toJson()).toList();
    }
    data['id'] = id;
    return data;
  }
}

/// The review states a document can be in, as shown to the driver.
enum DocumentReviewStatus { notUploaded, pending, approved, rejected }

class Documents {
  String? frontImage;
  String? documentNumber;
  bool? verified;
  String? documentId;
  String? backImage;
  Timestamp? expireAt;

  /// Written by the admin panel alongside [verified] as "Approved" or
  /// "DisApproved". The driver app used to ignore it entirely, so a rejected
  /// document looked exactly the same as one nobody had reviewed yet.
  String? status;

  Documents({this.frontImage, this.documentNumber, this.verified, this.documentId, this.backImage, this.expireAt, this.status});

  Documents.fromJson(Map<String, dynamic> json) {
    frontImage = json['frontImage'];
    documentNumber = json['documentNumber'];
    verified = json['verified'];
    documentId = json['documentId'];
    backImage = json['backImage'];
    expireAt = json['expireAt'];
    status = json['status'];
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = <String, dynamic>{};
    data['frontImage'] = frontImage;
    data['documentNumber'] = documentNumber;
    data['verified'] = verified;
    data['documentId'] = documentId;
    data['backImage'] = backImage;
    data['expireAt'] = expireAt;
    data['status'] = status;
    return data;
  }

  /// Mirrors the admin panel's own mapping so the driver sees exactly what the
  /// reviewer sees. Documents approved before the status field existed only
  /// have verified == true, so that alone still counts as approved.
  DocumentReviewStatus get reviewStatus {
    if (documentId == null || documentId!.isEmpty) {
      return DocumentReviewStatus.notUploaded;
    }
    if (verified == true) {
      return DocumentReviewStatus.approved;
    }
    if (status == 'DisApproved') {
      return DocumentReviewStatus.rejected;
    }
    return DocumentReviewStatus.pending;
  }
}
