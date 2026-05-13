import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../constants/app_colors.dart';
import '../../constants/app_spacing.dart';
import '../../constants/app_typography.dart';
import '../../providers/auth_provider.dart';
import '../../routes/route_names.dart';
import '../../utils/snackbars.dart';
import '../../utils/validators.dart';
import '../../widgets/app_button.dart';
import '../../widgets/app_text_field.dart';

class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key});

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final _formKey = GlobalKey<FormState>();
  final _emailController = TextEditingController();
  final _passwordController = TextEditingController();

  @override
  void dispose() {
    _emailController.dispose();
    _passwordController.dispose();
    super.dispose();
  }

  Future<void> _submit() async {
    FocusScope.of(context).unfocus();
    if (!_formKey.currentState!.validate()) return;

    final auth = context.read<AuthProvider>();
    final ok = await auth.login(
      email: _emailController.text.trim(),
      password: _passwordController.text,
    );

    if (!ok && mounted) {
      AppSnackBars.showError(
        context,
        auth.errorMessage ?? 'Tidak dapat masuk.',
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: SafeArea(
        child: SingleChildScrollView(
          keyboardDismissBehavior: ScrollViewKeyboardDismissBehavior.onDrag,
          padding: const EdgeInsets.all(AppSpacing.screenPadding),
          child: Form(
            key: _formKey,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const SizedBox(height: AppSpacing.xxl),
                const Icon(
                  Icons.manage_search_rounded,
                  size: 52,
                  color: AppColors.primary,
                ),
                const SizedBox(height: AppSpacing.xl),
                const Text(
                  'Selamat datang kembali',
                  style: AppTypography.titleLarge,
                ),
                const SizedBox(height: AppSpacing.sm),
                const Text(
                  'Masuk untuk melapor, melihat, mengklaim, dan memantau barang kampus.',
                  style: AppTypography.bodyMuted,
                ),
                const SizedBox(height: AppSpacing.xxl),
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
                  textInputAction: TextInputAction.done,
                  prefixIcon: const Icon(Icons.lock_outline_rounded),
                  validator: (value) =>
                      Validators.required(value, label: 'Kata sandi'),
                  onFieldSubmitted: (_) => _submit(),
                ),
                const SizedBox(height: AppSpacing.xl),
                Consumer<AuthProvider>(
                  builder: (context, auth, child) {
                    return AppButton(
                      label: 'Masuk',
                      icon: Icons.login_rounded,
                      isLoading: auth.isBusy,
                      onPressed: _submit,
                    );
                  },
                ),
                const SizedBox(height: AppSpacing.lg),
                Center(
                  child: TextButton(
                    onPressed: () {
                      Navigator.of(context).pushNamed(RouteNames.register);
                    },
                    child: const Text('Buat akun baru'),
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
