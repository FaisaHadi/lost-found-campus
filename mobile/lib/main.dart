import 'package:flutter/material.dart';

void main() {
  runApp(const LostFoundCampusApp());
}

class LostFoundCampusApp extends StatelessWidget {
  const LostFoundCampusApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Lost & Found Campus',
      theme: ThemeData(
        colorScheme: ColorScheme.fromSeed(seedColor: Colors.indigo),
        useMaterial3: true,
      ),
      home: const Scaffold(body: Center(child: Text('Lost & Found Campus'))),
    );
  }
}
