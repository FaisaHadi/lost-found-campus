import 'dart:io';

import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../constants/app_colors.dart';
import '../../constants/app_spacing.dart';
import '../../constants/app_typography.dart';
import '../../models/create_report_payload.dart';
import '../../providers/reports_provider.dart';
import '../../services/camera_service.dart';
import '../../services/location_service.dart';
import '../../utils/snackbars.dart';
import '../../utils/validators.dart';
import '../../widgets/app_button.dart';
import '../../widgets/app_card.dart';
import '../../widgets/app_text_field.dart';

class CreateReportScreen extends StatefulWidget {
  const CreateReportScreen({super.key});

  @override
  State<CreateReportScreen> createState() => _CreateReportScreenState();
}

class _CreateReportScreenState extends State<CreateReportScreen> {
  final _formKey = GlobalKey<FormState>();
  final _titleController = TextEditingController();
  final _descriptionController = TextEditingController();
  final _locationController = TextEditingController();

  String _reportType = 'lost';
  int? _categoryId;
  String? _imagePath;
  CapturedLocation? _capturedLocation;
  bool _isCapturingImage = false;
  bool _isCapturingLocation = false;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<ReportsProvider>().loadCategories();
    });
  }

  @override
  void dispose() {
    _titleController.dispose();
    _descriptionController.dispose();
    _locationController.dispose();
    super.dispose();
  }

  Future<void> _captureImage() async {
    setState(() => _isCapturingImage = true);
    try {
      final file = await context.read<CameraService>().captureReportImage();
      if (file != null) {
        setState(() => _imagePath = file.path);
      }
    } catch (_) {
      if (mounted) {
        AppSnackBars.showError(
          context,
          'Kamera tidak dapat dibuka. Periksa izin kamera lalu coba lagi.',
        );
      }
    } finally {
      if (mounted) setState(() => _isCapturingImage = false);
    }
  }

  Future<void> _pickFromGallery() async {
    setState(() => _isCapturingImage = true);
    try {
      final file = await context
          .read<CameraService>()
          .pickReportImageFromGallery();
      if (file != null) {
        setState(() => _imagePath = file.path);
      }
    } catch (_) {
      if (mounted) {
        AppSnackBars.showError(
          context,
          'Gambar tidak dapat dipilih. Periksa izin foto lalu coba lagi.',
        );
      }
    } finally {
      if (mounted) setState(() => _isCapturingImage = false);
    }
  }

  Future<void> _captureLocation() async {
    setState(() => _isCapturingLocation = true);
    try {
      final location = await context
          .read<LocationService>()
          .getCurrentLocation();
      setState(() {
        _capturedLocation = location;
        if (_locationController.text.trim().isEmpty) {
          _locationController.text = 'Lokasi kampus saat ini';
        }
      });
    } on LocationFailure catch (error) {
      if (mounted) AppSnackBars.showError(context, error.message);
    } catch (_) {
      if (mounted) {
        AppSnackBars.showError(context, 'Lokasi tidak dapat diambil.');
      }
    } finally {
      if (mounted) setState(() => _isCapturingLocation = false);
    }
  }

  Future<void> _submit() async {
    FocusScope.of(context).unfocus();
    if (!_formKey.currentState!.validate()) return;

    final provider = context.read<ReportsProvider>();
    final ok = await provider.createReport(
      CreateReportPayload(
        title: _titleController.text.trim(),
        description: _descriptionController.text.trim(),
        reportType: _reportType,
        categoryId: _categoryId,
        imagePath: _imagePath,
        latitude: _capturedLocation?.latitude,
        longitude: _capturedLocation?.longitude,
        locationText: _locationController.text.trim(),
      ),
    );

    if (!mounted) return;
    if (ok) {
      AppSnackBars.showSuccess(
        context,
        'Laporan dikirim dan menunggu moderasi.',
      );
      Navigator.of(context).pop();
    } else {
      AppSnackBars.showError(
        context,
        provider.errorMessage ?? 'Laporan tidak dapat dikirim.',
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final reports = context.watch<ReportsProvider>();

    return Scaffold(
      appBar: AppBar(title: const Text('Buat laporan')),
      body: SafeArea(
        top: false,
        child: Form(
          key: _formKey,
          child: ListView(
            keyboardDismissBehavior: ScrollViewKeyboardDismissBehavior.onDrag,
            padding: const EdgeInsets.fromLTRB(
              AppSpacing.screenPadding,
              AppSpacing.md,
              AppSpacing.screenPadding,
              AppSpacing.xl,
            ),
            children: [
              const Text('Apa yang terjadi?', style: AppTypography.titleMedium),
              const SizedBox(height: AppSpacing.lg),
              SegmentedButton<String>(
                segments: const [
                  ButtonSegment(
                    value: 'lost',
                    label: Text('Hilang'),
                    icon: Icon(Icons.search_rounded),
                  ),
                  ButtonSegment(
                    value: 'found',
                    label: Text('Ditemukan'),
                    icon: Icon(Icons.inventory_2_outlined),
                  ),
                ],
                selected: {_reportType},
                onSelectionChanged: (selection) {
                  setState(() => _reportType = selection.first);
                },
              ),
              const SizedBox(height: AppSpacing.lg),
              DropdownButtonFormField<int?>(
                initialValue: _categoryId,
                decoration: const InputDecoration(
                  labelText: 'Kategori',
                  prefixIcon: Icon(Icons.category_outlined),
                ),
                items: [
                  const DropdownMenuItem<int?>(
                    value: null,
                    child: Text('Tanpa kategori'),
                  ),
                  ...reports.categories.map(
                    (category) => DropdownMenuItem<int?>(
                      value: category.id,
                      child: Text(category.name),
                    ),
                  ),
                ],
                onChanged: (value) => setState(() => _categoryId = value),
              ),
              const SizedBox(height: AppSpacing.lg),
              AppTextField(
                label: 'Judul barang',
                controller: _titleController,
                textInputAction: TextInputAction.next,
                prefixIcon: const Icon(Icons.short_text_rounded),
                validator: (value) =>
                    Validators.required(value, label: 'Judul'),
              ),
              const SizedBox(height: AppSpacing.lg),
              AppTextField(
                label: 'Deskripsi',
                hintText: 'Tambahkan warna, merek, ciri khusus, dan konteks.',
                controller: _descriptionController,
                keyboardType: TextInputType.multiline,
                minLines: 4,
                maxLines: 7,
                prefixIcon: const Icon(Icons.notes_rounded),
                validator: (value) =>
                    Validators.minLength(value, 10, label: 'Deskripsi'),
              ),
              const SizedBox(height: AppSpacing.xl),
              _ImageCaptureCard(
                imagePath: _imagePath,
                isLoading: _isCapturingImage,
                onCapture: _captureImage,
                onGallery: _pickFromGallery,
                onRemove: () => setState(() => _imagePath = null),
              ),
              const SizedBox(height: AppSpacing.xl),
              AppTextField(
                label: 'Catatan lokasi',
                hintText: 'Contoh: Perpustakaan Utama, lantai 2',
                controller: _locationController,
                textInputAction: TextInputAction.next,
                prefixIcon: const Icon(Icons.place_outlined),
              ),
              const SizedBox(height: AppSpacing.md),
              _LocationCaptureCard(
                location: _capturedLocation,
                isLoading: _isCapturingLocation,
                onCapture: _captureLocation,
                onClear: () => setState(() => _capturedLocation = null),
              ),
              const SizedBox(height: AppSpacing.xl),
              AppButton(
                label: 'Kirim laporan',
                icon: Icons.send_rounded,
                isLoading: reports.isSubmitting,
                onPressed: _submit,
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class _ImageCaptureCard extends StatelessWidget {
  const _ImageCaptureCard({
    required this.imagePath,
    required this.isLoading,
    required this.onCapture,
    required this.onGallery,
    required this.onRemove,
  });

  final String? imagePath;
  final bool isLoading;
  final VoidCallback onCapture;
  final VoidCallback onGallery;
  final VoidCallback onRemove;

  @override
  Widget build(BuildContext context) {
    return AppCard(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text('Foto', style: AppTypography.label),
          const SizedBox(height: AppSpacing.sm),
          Text(
            'Ambil foto barang yang jelas agar pengguna lain mudah mengenalinya.',
            style: AppTypography.bodyMuted,
          ),
          const SizedBox(height: AppSpacing.lg),
          if (imagePath != null) ...[
            ClipRRect(
              borderRadius: BorderRadius.circular(AppSpacing.radius),
              child: AspectRatio(
                aspectRatio: 4 / 3,
                child: Image.file(File(imagePath!), fit: BoxFit.cover),
              ),
            ),
            const SizedBox(height: AppSpacing.md),
          ],
          Row(
            children: [
              Expanded(
                child: AppButton(
                  label: 'Kamera',
                  icon: Icons.photo_camera_rounded,
                  isLoading: isLoading,
                  onPressed: onCapture,
                ),
              ),
              const SizedBox(width: AppSpacing.sm),
              Expanded(
                child: AppButton(
                  label: 'Galeri',
                  icon: Icons.photo_library_outlined,
                  variant: AppButtonVariant.ghost,
                  isLoading: isLoading,
                  onPressed: onGallery,
                ),
              ),
            ],
          ),
          if (imagePath != null) ...[
            const SizedBox(height: AppSpacing.sm),
            TextButton.icon(
              onPressed: onRemove,
              icon: const Icon(Icons.delete_outline_rounded),
              label: const Text('Hapus foto'),
            ),
          ],
        ],
      ),
    );
  }
}

class _LocationCaptureCard extends StatelessWidget {
  const _LocationCaptureCard({
    required this.location,
    required this.isLoading,
    required this.onCapture,
    required this.onClear,
  });

  final CapturedLocation? location;
  final bool isLoading;
  final VoidCallback onCapture;
  final VoidCallback onClear;

  @override
  Widget build(BuildContext context) {
    return AppCard(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text('Lokasi GPS', style: AppTypography.label),
          const SizedBox(height: AppSpacing.sm),
          if (location == null)
            const Text(
              'Gunakan lokasi saat ini untuk melampirkan koordinat yang akurat.',
              style: AppTypography.bodyMuted,
            )
          else
            Container(
              width: double.infinity,
              padding: const EdgeInsets.all(AppSpacing.md),
              decoration: BoxDecoration(
                color: AppColors.primary.withValues(alpha: 0.08),
                borderRadius: BorderRadius.circular(AppSpacing.radius),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    '${location!.latitude.toStringAsFixed(6)}, ${location!.longitude.toStringAsFixed(6)}',
                    style: AppTypography.body,
                  ),
                  const SizedBox(height: AppSpacing.xs),
                  Text(
                    'Akurasi sekitar ${location!.accuracy.toStringAsFixed(0)} m',
                    style: AppTypography.bodyMuted,
                  ),
                ],
              ),
            ),
          const SizedBox(height: AppSpacing.md),
          Row(
            children: [
              Expanded(
                child: AppButton(
                  label: location == null ? 'Gunakan GPS' : 'Perbarui GPS',
                  icon: Icons.my_location_rounded,
                  isLoading: isLoading,
                  onPressed: onCapture,
                ),
              ),
              if (location != null) ...[
                const SizedBox(width: AppSpacing.sm),
                IconButton.filledTonal(
                  tooltip: 'Hapus GPS',
                  onPressed: onClear,
                  icon: const Icon(Icons.close_rounded),
                ),
              ],
            ],
          ),
        ],
      ),
    );
  }
}
