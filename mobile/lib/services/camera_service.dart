import 'package:image_picker/image_picker.dart';

class CameraService {
  CameraService({ImagePicker? picker}) : _picker = picker ?? ImagePicker();

  final ImagePicker _picker;

  Future<XFile?> captureReportImage() {
    return _picker.pickImage(
      source: ImageSource.camera,
      imageQuality: 85,
      maxWidth: 1600,
      maxHeight: 1600,
    );
  }

  Future<XFile?> pickReportImageFromGallery() {
    return _picker.pickImage(
      source: ImageSource.gallery,
      imageQuality: 85,
      maxWidth: 1600,
      maxHeight: 1600,
    );
  }
}
