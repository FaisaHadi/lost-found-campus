import 'package:flutter_test/flutter_test.dart';
import 'package:provider/provider.dart';

import 'package:lost_found_campus/main.dart';
import 'package:lost_found_campus/providers/auth_provider.dart';
import 'package:lost_found_campus/services/api_client.dart';
import 'package:lost_found_campus/services/auth_service.dart';
import 'package:lost_found_campus/services/token_storage_service.dart';

void main() {
  testWidgets('renders auth bootstrap shell', (WidgetTester tester) async {
    final tokenStorage = TokenStorageService();
    final apiClient = ApiClient(tokenStorage);

    await tester.pumpWidget(
      ChangeNotifierProvider(
        create: (_) => AuthProvider(AuthService(apiClient, tokenStorage)),
        child: const LostFoundCampusApp(),
      ),
    );

    expect(find.text('Lost & Found Campus'), findsOneWidget);
  });
}
