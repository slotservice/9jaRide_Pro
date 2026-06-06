import 'package:shared_preferences/shared_preferences.dart';

class DarkThemePreference {
  static const THEME_STATUS = "THEMESTATUS";

  setDarkTheme(int value) async {
    SharedPreferences prefs = await SharedPreferences.getInstance();
    prefs.setInt(THEME_STATUS, value);
  }

  Future<int> getTheme() async {
    SharedPreferences prefs = await SharedPreferences.getInstance();
    // One-time migration: reset dark mode (0) to light mode (1) for existing users
    bool migrated = prefs.getBool('THEME_MIGRATED_V2') ?? false;
    if (!migrated) {
      await prefs.setInt(THEME_STATUS, 1);
      await prefs.setBool('THEME_MIGRATED_V2', true);
      return 1;
    }
    return prefs.getInt(THEME_STATUS) ?? 1;
  }
}
