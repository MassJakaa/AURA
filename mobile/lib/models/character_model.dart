class CharacterModel {
  final String id;
  final String name;
  final String code;
  final String genderPresentation;
  final String visualStyle;
  final String description;
  final Map<String, dynamic>? personalityConfig;
  final Map<String, dynamic>? appearanceConfig;
  final String? masterReferenceStatus;

  CharacterModel({
    required this.id,
    required this.name,
    required this.code,
    required this.genderPresentation,
    required this.visualStyle,
    required this.description,
    this.personalityConfig,
    this.appearanceConfig,
    this.masterReferenceStatus,
  });

  factory CharacterModel.fromJson(Map<String, dynamic> json) {
    return CharacterModel(
      id: json['id'] as String,
      name: json['name'] as String,
      code: json['code'] as String,
      genderPresentation: json['gender_presentation'] as String? ?? 'female',
      visualStyle: json['visual_style'] as String? ?? '2d_soft_anime',
      description: json['description'] as String? ?? '',
      personalityConfig: json['personality_config'] as Map<String, dynamic>?,
      appearanceConfig: json['appearance_config'] as Map<String, dynamic>?,
      masterReferenceStatus: json['master_reference_status'] as String?,
    );
  }
}
