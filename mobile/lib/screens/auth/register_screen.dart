import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../constants/app_colors.dart';
import '../../constants/app_spacing.dart';
import '../../constants/app_typography.dart';
import '../../providers/auth_provider.dart';
import '../../utils/snackbars.dart';
import '../../utils/validators.dart';
import '../../widgets/app_button.dart';
import '../../widgets/app_text_field.dart';

class RegisterScreen extends StatefulWidget {
  const RegisterScreen({super.key});

  @override
  State<RegisterScreen> createState() => _RegisterScreenState();
}

class _RegisterScreenState extends State<RegisterScreen> {
  final _formKey = GlobalKey<FormState>();
  final _nameController = TextEditingController();
  final _emailController = TextEditingController();
  final _passwordController = TextEditingController();
  final _confirmationController = TextEditingController();

  @override
  void dispose() {
    _nameController.dispose();
    _emailController.dispose();
    _passwordController.dispose();
    _confirmationController.dispose();
    super.dispose();
  }

  Future<void> _submit() async {
    FocusScope.of(context).unfocus();
    if (!_formKey.currentState!.validate()) return;

    final auth = context.read<AuthProvider>();
    final ok = await auth.register(
      name: _nameController.text.trim(),
      email: _emailController.text.trim(),
      password: _passwordController.text,
      passwordConfirmation: _confirmationController.text,
    );

    if (!mounted) return;
    if (ok) {
      Navigator.of(context).pop();
    } else {
      AppSnackBars.showError(
        context,
        auth.errorMessage ?? 'Tidak dapat membuat akun.',
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(),
      body: SafeArea(
        child: SingleChildScrollView(
          keyboardDismissBehavior: ScrollViewKeyboardDismissBehavior.onDrag,
          padding: const EdgeInsets.all(AppSpacing.screenPadding),
          child: Form(
            key: _formKey,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Icon(
                  Icons.person_add_alt_rounded,
                  size: 48,
                  color: AppColors.primary,
                ),
                const SizedBox(height: AppSpacing.lg),
                const Text('Buat akun', style: AppTypography.titleLarge),
                const SizedBox(height: AppSpacing.sm),
                const Text(
                  'Gunakan identitas kampus agar klaim dan laporan mudah ditelusuri.',
                  style: AppTypography.bodyMuted,
                ),
                const SizedBox(height: AppSpacing.xxl),
                AppTextField(
                  label: 'Nama lengkap',
                  controller: _nameController,
                  textInputAction: TextInputAction.next,
                  prefixIcon: const Icon(Icons.badge_outlined),
                  validator: (value) =>
                      Validators.required(value, label: 'Nama'),
                ),
                const SizedBox(height: AppSpacing.lg),
                AppTextField(
                  label: 'Email',
                  controller: _emailController,
                  keyboardType: TextInputType.emailAddress,
                  textInputAction: TextInputAction.next,
                  prefixIcon: const Icon(Icons.mail_outline_rounded),
                  validator: Validators.email,
                ),
                const SizedBox(height: AppSpacing.lg),
                AppTextField(
                  label: 'Kata sandi',
                  controller: _passwordController,
                  obscureText: true,
                  textInputAction: TextInputAction.next,
                  prefixIcon: const Icon(Icons.lock_outline_rounded),
                  validator: (value) =>
                      Validators.minLength(value, 8, label: 'Kata sandi'),
                ),
                const SizedBox(height: AppSpacing.lg),
                AppTextField(
                  label: 'Konfirmasi kata sandi',
                  controller: _confirmationController,
                  obscureText: true,
                  textInputAction: TextInputAction.done,
                  prefixIcon: const Icon(Icons.lock_reset_rounded),
                  validator: (value) {
                    final required = Validators.required(
                      value,
                      label: 'Konfirmasi kata sandi',
                    );
                    if (required != null) return required;
                    if (value != _passwordController.text) {
                      return 'Kata sandi tidak sama.';
                    }
                    return null;
                  },
                  onFieldSubmitted: (_) => _submit(),
                ),
                const SizedBox(height: AppSpacing.xl),
                Consumer<AuthProvider>(
                  builder: (context, auth, child) {
                    return AppButton(
                      label: 'Buat akun',
                      icon: Icons.check_rounded,
                      isLoading: auth.isBusy,
                      onPressed: _submit,
                    );
                  },
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
