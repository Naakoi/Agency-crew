import 'package:flutter/material.dart';

class AppTheme {
  static const Color navy = Color(0xFF0D1B2A);
  static const Color navyLighter = Color(0xFF1B2D42);
  static const Color accent = Color(0xFF1A78C2);
  static const Color accentLight = Color(0xFF0E9AE0);
  static const Color teal = Color(0xFF0CB8A8);
  static const Color green = Color(0xFF22C55E);
  static const Color red = Color(0xFFEF4444);
  static const Color textBody = Color(0xFFE2E8F0);
  static const Color muted = Color(0xFF94A3B8);

  static ThemeData get darkTheme {
    return ThemeData(
      brightness: Brightness.dark,
      scaffoldBackgroundColor: navy,
      primaryColor: accent,
      fontFamily: 'Inter',
      cardTheme: CardThemeData(
        color: navyLighter.withOpacity(0.85),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
        elevation: 0,
        margin: const EdgeInsets.only(bottom: 12),
      ),
      appBarTheme: const AppBarTheme(
        backgroundColor: Colors.transparent,
        elevation: 0,
        titleTextStyle: TextStyle(fontSize: 20, fontWeight: FontWeight.w800, color: Colors.white),
      ),
      elevatedButtonTheme: ElevatedButtonThemeData(
        style: ElevatedButton.styleFrom(
          backgroundColor: accent,
          foregroundColor: Colors.white,
          padding: const EdgeInsets.symmetric(vertical: 14, horizontal: 24),
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
          textStyle: const TextStyle(fontWeight: FontWeight.bold, fontSize: 15),
        ),
      ),
      inputDecorationTheme: InputDecorationTheme(
        filled: true,
        fillColor: Colors.white.withOpacity(0.06),
        border: OutlineInputBorder(borderRadius: BorderRadius.circular(10), borderSide: BorderSide.none),
        focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(10), borderSide: const BorderSide(color: accent)),
        hintStyle: const TextStyle(color: Colors.white24),
      ),
    );
  }
}
