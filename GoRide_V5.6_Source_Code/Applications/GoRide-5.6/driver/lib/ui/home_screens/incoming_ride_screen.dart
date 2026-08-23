import 'dart:async';

import 'package:driver/constant/constant.dart';
import 'package:driver/constant/show_toast_dialog.dart';
import 'package:driver/model/order_model.dart';
import 'package:driver/themes/app_colors.dart';
import 'package:driver/ui/home_screens/order_map_screen.dart';
import 'package:driver/utils/fire_store_utils.dart';
import 'package:driver/utils/ride_ringtone.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';

/// The incoming ride request, shown the way a phone shows an incoming call.
///
/// This screen only alerts. It deliberately does not accept the ride itself:
/// Accept hands over to the existing OrderMapScreen, which already carries the
/// bidding, the fare, the race guard against another driver taking the ride,
/// and everything else that has been tested. Re-implementing any of that here
/// would be a second copy to keep correct.
class IncomingRideScreen extends StatefulWidget {
  const IncomingRideScreen({super.key, required this.orderId});

  final String orderId;

  @override
  State<IncomingRideScreen> createState() => _IncomingRideScreenState();
}

class _IncomingRideScreenState extends State<IncomingRideScreen> {
  OrderModel? _order;
  bool _loading = true;
  Timer? _expiryTimer;

  @override
  void initState() {
    super.initState();
    _loadOrder();
    // Stop showing a request the driver never answered, so they do not come
    // back to a stale ring screen sitting over the app.
    _expiryTimer = Timer(RideRingtone.maxRingDuration, () {
      if (mounted) {
        _dismiss();
      }
    });
  }

  Future<void> _loadOrder() async {
    try {
      final OrderModel? order = await FireStoreUtils.getOrder(widget.orderId);
      if (!mounted) return;
      // Another driver may already have taken it while the phone was ringing.
      // Ringing on for a job that is gone is worse than not ringing at all.
      final bool stillOpen = order != null && order.status == Constant.ridePlaced;
      setState(() {
        _order = order;
        _loading = false;
      });
      if (!stillOpen) {
        ShowToastDialog.showToast("This ride is no longer available.".tr);
        _dismiss();
      }
    } catch (e) {
      if (!mounted) return;
      // A failed lookup must not hide the request. Show it with what we have
      // and let the driver decide; OrderMapScreen re-reads it anyway.
      setState(() => _loading = false);
    }
  }

  void _dismiss() {
    RideRingtone.stop();
    _expiryTimer?.cancel();
    if (mounted && Navigator.of(context).canPop()) {
      Get.back();
    }
  }

  void _accept() {
    RideRingtone.stop();
    _expiryTimer?.cancel();
    // off, not to: the ring screen has done its job and should not sit behind
    // the map waiting to be popped back to.
    Get.off(() => const OrderMapScreen(), arguments: {"orderModel": widget.orderId});
  }

  @override
  void dispose() {
    _expiryTimer?.cancel();
    // Covers every way out of here, including the back button and the system
    // killing the route, so the tone can never outlive the screen.
    RideRingtone.stop();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final OrderModel? order = _order;
    // No PopScope here on purpose. dispose() stops the tone on every route out,
    // including the hardware back button, so there is nothing left to intercept.
    return Scaffold(
      backgroundColor: AppColors.darkBackground,
      body: SafeArea(
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 32),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
                const SizedBox(height: 24),
                Text(
                  "New ride request".tr,
                  textAlign: TextAlign.center,
                  style: TextStyle(color: AppColors.lightsecondprimary, fontSize: 26, fontWeight: FontWeight.w700),
                ),
                const SizedBox(height: 8),
                Text(
                  "Incoming".tr,
                  textAlign: TextAlign.center,
                  style: const TextStyle(color: Colors.white70, fontSize: 15),
                ),
                const SizedBox(height: 40),
                if (_loading)
                  const Center(child: CircularProgressIndicator())
                else if (order != null) ...[
                  _locationRow(Icons.my_location, "Pickup".tr, order.sourceLocationName ?? ''),
                  const SizedBox(height: 20),
                  _locationRow(Icons.location_on, "Drop off".tr, order.destinationLocationName ?? ''),
                  if (order.offerRate != null && order.offerRate!.isNotEmpty) ...[
                    const SizedBox(height: 24),
                    Text(
                      Constant.amountShow(amount: order.offerRate),
                      textAlign: TextAlign.center,
                      style: TextStyle(color: AppColors.lightsecondprimary, fontSize: 30, fontWeight: FontWeight.w700),
                    ),
                  ],
                ],
                const Spacer(),
                Row(
                  children: [
                    Expanded(
                      child: _actionButton(
                        label: "Decline".tr,
                        color: Colors.red.shade700,
                        icon: Icons.call_end,
                        onTap: _dismiss,
                      ),
                    ),
                    const SizedBox(width: 16),
                    Expanded(
                      child: _actionButton(
                        label: "Accept".tr,
                        color: Colors.green.shade700,
                        icon: Icons.check,
                        onTap: _accept,
                      ),
                    ),
                  ],
                ),
              const SizedBox(height: 12),
            ],
          ),
        ),
      ),
    );
  }

  Widget _locationRow(IconData icon, String label, String value) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Icon(icon, color: AppColors.lightsecondprimary, size: 22),
        const SizedBox(width: 12),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(label, style: const TextStyle(color: Colors.white54, fontSize: 13)),
              const SizedBox(height: 2),
              Text(
                value.isEmpty ? "-" : value,
                style: const TextStyle(color: Colors.white, fontSize: 16, fontWeight: FontWeight.w500),
              ),
            ],
          ),
        ),
      ],
    );
  }

  Widget _actionButton({required String label, required Color color, required IconData icon, required VoidCallback onTap}) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(12),
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 16),
        decoration: BoxDecoration(color: color, borderRadius: BorderRadius.circular(12)),
        child: Column(
          children: [
            Icon(icon, color: Colors.white, size: 26),
            const SizedBox(height: 6),
            Text(label, style: const TextStyle(color: Colors.white, fontSize: 16, fontWeight: FontWeight.w600)),
          ],
        ),
      ),
    );
  }
}
