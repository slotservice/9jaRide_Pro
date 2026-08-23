import 'dart:async';
import 'dart:developer';

import 'package:just_audio/just_audio.dart';

/// The tone a driver hears when a ride request lands.
///
/// A request is meant to behave like an incoming call, so the tone loops until
/// the driver accepts, declines, or the request runs out of time. The alert
/// notification channel is deliberately silent: if the channel carried a sound
/// of its own Android would play it over the top of this loop and the two would
/// overlap.
///
/// Everything here is defensive. A ride request that fails to make a noise is
/// bad, but a ride request that crashes the app is far worse.
class RideRingtone {
  RideRingtone._();

  static const String _asset = 'assets/audio/mixkit-happy-bells-notification-937.mp3';

  /// Hard stop, so a request nobody answers cannot ring until the battery dies.
  static const Duration maxRingDuration = Duration(seconds: 45);

  static AudioPlayer? _player;

  static bool get isRinging => _player != null;

  /// Starts the loop. Safe to call again while it is already ringing.
  static Future<void> start() async {
    // A second request arriving mid-ring keeps the first tone rather than
    // restarting it, which would sound like a stutter rather than a ring.
    if (_player != null) return;

    AudioPlayer? player;
    try {
      player = AudioPlayer();
      _player = player;
      await player.setAsset(_asset);
      await player.setLoopMode(LoopMode.one);
      await player.setVolume(1.0);
      // Not awaited on purpose. play() completes when playback ENDS, and on a
      // loop it never ends, so awaiting it would hang this call for ever.
      unawaited(player.play());

      final AudioPlayer started = player;
      Future.delayed(maxRingDuration, () {
        // Only stop the run we started. If the driver acted and a later request
        // is ringing now, that one owns the player and this timer must not
        // silence it.
        if (identical(_player, started)) {
          stop();
        }
      });
    } catch (e) {
      log('RideRingtone start error: $e');
      _player = null;
      try {
        await player?.dispose();
      } catch (_) {}
    }
  }

  /// Stops the loop and releases the player. Safe to call when not ringing.
  static Future<void> stop() async {
    final AudioPlayer? player = _player;
    _player = null;
    if (player == null) return;
    try {
      await player.stop();
      await player.dispose();
    } catch (e) {
      log('RideRingtone stop error: $e');
    }
  }
}
