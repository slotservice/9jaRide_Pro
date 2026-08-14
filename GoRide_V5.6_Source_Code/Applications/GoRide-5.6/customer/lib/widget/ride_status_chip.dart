import 'package:customer/constant/constant.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';

/// The stage of a trip drawn as a filled pill rather than coloured text, so it
/// reads at a glance.
///
/// The fill comes from Constant.rideStatusColor, and the driver app has an
/// identical widget over its own copy of the same palette, so a rider and a
/// driver looking at the same trip see the same colour.
class RideStatusChip extends StatelessWidget {
  final String? status;
  final bool driverArrived;

  const RideStatusChip({super.key, required this.status, this.driverArrived = false});

  @override
  Widget build(BuildContext context) {
    // Aligned rather than bare, because the call sites sit inside an Expanded
    // and a raw Container would stretch the pill across the whole row.
    return Align(
      alignment: Alignment.centerLeft,
      child: Container(
        decoration: BoxDecoration(
          color: Constant.rideStatusColor(status, driverArrived: driverArrived),
          borderRadius: BorderRadius.circular(20),
        ),
        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
        child: Text(
          Constant.statusLabel(status, driverArrived: driverArrived).tr,
          style: GoogleFonts.poppins(color: Colors.white, fontWeight: FontWeight.w600, fontSize: 12),
        ),
      ),
    );
  }
}
