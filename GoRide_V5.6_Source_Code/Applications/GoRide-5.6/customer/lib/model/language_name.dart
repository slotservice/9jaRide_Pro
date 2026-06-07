class LanguageName {
  String? name;
  String? type;
  String? title;

  LanguageName({this.name, this.type, this.title});

  factory LanguageName.fromJson(Map<String, dynamic> json) {
    return LanguageName(
      name: json['name'] as String?,
      title: (json['title'] ?? json['name']) as String?,
      type: (json['type'] ?? json['language_code'] ?? json['languageName']) as String?,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      if (name != null) 'name': name,
      if (title != null) 'title': title,
      if (type != null) 'type': type,
    };
  }
}
