class AssistanceTypeModel {
  String? id;
  String? title;
  bool? enable;
  int? order;

  AssistanceTypeModel({this.id, this.title, this.enable, this.order});

  AssistanceTypeModel.fromJson(Map<String, dynamic> json) {
    id = json['id'];
    title = json['title'];
    enable = json['enable'] is bool ? json['enable'] : (json['enable']?.toString() == 'true');
    order = json['order'] is int ? json['order'] : int.tryParse(json['order']?.toString() ?? '');
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'title': title,
      'enable': enable,
      'order': order,
    };
  }
}
