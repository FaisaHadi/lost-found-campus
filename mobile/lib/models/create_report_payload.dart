class CreateReportPayload {
  const CreateReportPayload({
    required this.title,
    required this.description,
    required this.reportType,
    this.categoryId,
    this.imagePath,
    this.latitude,
    this.longitude,
    this.locationText,
  });

  final String title;
  final String description;
  final String reportType;
  final int? categoryId;
  final String? imagePath;
  final double? latitude;
  final double? longitude;
  final String? locationText;
}
