class Validators {
  const Validators._();

  static String? required(String? value, {String label = 'Kolom ini'}) {
    if (value == null || value.trim().isEmpty) {
      return '$label wajib diisi.';
    }
    return null;
  }

  static String? email(String? value) {
    final requiredMessage = required(value, label: 'Email');
    if (requiredMessage != null) return requiredMessage;
    final emailPattern = RegExp(r'^[^@\s]+@[^@\s]+\.[^@\s]+$');
    if (!emailPattern.hasMatch(value!.trim())) {
      return 'Masukkan alamat email yang valid.';
    }
    return null;
  }

  static String? minLength(String? value, int min, {String label = 'Kolom'}) {
    final requiredMessage = required(value, label: label);
    if (requiredMessage != null) return requiredMessage;
    if (value!.trim().length < min) {
      return '$label minimal $min karakter.';
    }
    return null;
  }
}
