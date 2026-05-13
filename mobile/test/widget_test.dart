import 'package:flutter_test/flutter_test.dart';

import 'package:lost_found_campus/main.dart';

void main() {
  testWidgets('renders phase zero app shell', (WidgetTester tester) async {
    await tester.pumpWidget(const LostFoundCampusApp());

    expect(find.text('Lost & Found Campus'), findsOneWidget);
  });
}
