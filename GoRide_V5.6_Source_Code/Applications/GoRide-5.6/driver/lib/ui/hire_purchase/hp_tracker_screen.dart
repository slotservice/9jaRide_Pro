import 'package:cloud_firestore/cloud_firestore.dart';
import 'package:driver/model/driver_user_model.dart';
import 'package:driver/themes/app_colors.dart';
import 'package:driver/themes/responsive.dart';
import 'package:driver/utils/DarkThemeProvider.dart';
import 'package:driver/utils/fire_store_utils.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import 'package:provider/provider.dart';

class HpTrackerScreen extends StatelessWidget {
  final DriverUserModel driverModel;

  const HpTrackerScreen({super.key, required this.driverModel});

  Future<List<Map<String, dynamic>>> _loadPaymentHistory() async {
    final uid = FireStoreUtils.getCurrentUid();
    if (uid == null) return [];
    final snapshot = await FirebaseFirestore.instance
        .collection('driver_users')
        .doc(uid)
        .collection('hp_payments')
        .orderBy('date', descending: true)
        .limit(50)
        .get();
    return snapshot.docs.map((d) => d.data()).toList();
  }

  @override
  Widget build(BuildContext context) {
    final themeChange = Provider.of<DarkThemeProvider>(context);
    final bool isDark = themeChange.getThem();

    final double totalCost = driverModel.hpTotalCost ?? 0;
    final double amountPaid = driverModel.hpAmountPaid ?? 0;
    final double balance = driverModel.hpBalance ?? (totalCost - amountPaid);
    final double daily = driverModel.hpDailyDeduction ?? 0;
    final String status = driverModel.hpStatus ?? 'green';
    final double progress = totalCost > 0 ? (amountPaid / totalCost).clamp(0.0, 1.0) : 0.0;

    Color statusColor;
    String statusLabel;
    switch (status) {
      case 'red':
        statusColor = Colors.red;
        statusLabel = 'Overdue';
        break;
      case 'yellow':
        statusColor = Colors.orange;
        statusLabel = 'Warning';
        break;
      default:
        statusColor = Colors.green;
        statusLabel = 'Good Standing';
    }

    String _formatDate(Timestamp? ts) {
      if (ts == null) return '--';
      return DateFormat('dd MMM yyyy').format(ts.toDate());
    }

    String _formatAmount(double v) => '₦${NumberFormat('#,##0.00').format(v)}';

    return Scaffold(
      backgroundColor: AppColors.lightprimary,
      appBar: AppBar(
        backgroundColor: AppColors.lightprimary,
        elevation: 0,
        leading: InkWell(
          onTap: () => Get.back(),
          child: const Icon(Icons.arrow_back, color: Colors.white),
        ),
        title: Text(
          'Hire-Purchase Tracker'.tr,
          style: GoogleFonts.poppins(color: Colors.white, fontWeight: FontWeight.w600, fontSize: 17),
        ),
      ),
      body: Column(
        children: [
          SizedBox(height: Responsive.width(4, context)),
          Expanded(
            child: Container(
              decoration: BoxDecoration(
                color: isDark ? AppColors.darkBackground : AppColors.background,
                borderRadius: const BorderRadius.only(
                  topLeft: Radius.circular(25),
                  topRight: Radius.circular(25),
                ),
              ),
              child: SingleChildScrollView(
                padding: const EdgeInsets.fromLTRB(16, 20, 16, 20),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Status badge
                    Row(
                      children: [
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
                          decoration: BoxDecoration(
                            color: statusColor.withOpacity(0.15),
                            borderRadius: BorderRadius.circular(20),
                            border: Border.all(color: statusColor, width: 1),
                          ),
                          child: Row(
                            mainAxisSize: MainAxisSize.min,
                            children: [
                              Icon(Icons.circle, size: 10, color: statusColor),
                              const SizedBox(width: 6),
                              Text(
                                statusLabel,
                                style: GoogleFonts.poppins(
                                  color: statusColor,
                                  fontWeight: FontWeight.w600,
                                  fontSize: 13,
                                ),
                              ),
                            ],
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 16),

                    // Progress card
                    Container(
                      padding: const EdgeInsets.all(16),
                      decoration: BoxDecoration(
                        color: isDark ? AppColors.darkContainerBackground : AppColors.containerBackground,
                        borderRadius: BorderRadius.circular(12),
                        border: Border.all(
                          color: isDark ? AppColors.darkContainerBorder : AppColors.containerBorder,
                          width: 0.5,
                        ),
                        boxShadow: isDark
                            ? null
                            : [BoxShadow(color: Colors.grey.withOpacity(0.15), blurRadius: 8, offset: const Offset(0, 2))],
                      ),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              Text(
                                'Payment Progress',
                                style: GoogleFonts.poppins(fontWeight: FontWeight.w600, fontSize: 14),
                              ),
                              Text(
                                '${(progress * 100).toStringAsFixed(1)}%',
                                style: GoogleFonts.poppins(
                                  fontWeight: FontWeight.w700,
                                  fontSize: 14,
                                  color: AppColors.lightsecondprimary,
                                ),
                              ),
                            ],
                          ),
                          const SizedBox(height: 10),
                          ClipRRect(
                            borderRadius: BorderRadius.circular(6),
                            child: LinearProgressIndicator(
                              value: progress,
                              minHeight: 12,
                              backgroundColor: isDark ? AppColors.darkContainerBorder : AppColors.grey200,
                              valueColor: AlwaysStoppedAnimation<Color>(AppColors.lightsecondprimary),
                            ),
                          ),
                          const SizedBox(height: 12),
                          Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              Text('Paid', style: GoogleFonts.poppins(fontSize: 12, color: Colors.grey)),
                              Text('Total', style: GoogleFonts.poppins(fontSize: 12, color: Colors.grey)),
                            ],
                          ),
                          Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              Text(_formatAmount(amountPaid),
                                  style: GoogleFonts.poppins(fontWeight: FontWeight.w600, fontSize: 14)),
                              Text(_formatAmount(totalCost),
                                  style: GoogleFonts.poppins(fontWeight: FontWeight.w600, fontSize: 14)),
                            ],
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(height: 16),

                    // Stats grid
                    Row(
                      children: [
                        Expanded(child: _statCard(isDark, 'Balance Due', _formatAmount(balance), Icons.account_balance_wallet_outlined, Colors.redAccent)),
                        const SizedBox(width: 10),
                        Expanded(child: _statCard(isDark, 'Daily Deduction', _formatAmount(daily), Icons.today_outlined, AppColors.lightsecondprimary)),
                      ],
                    ),
                    const SizedBox(height: 10),
                    Row(
                      children: [
                        Expanded(child: _statCard(isDark, 'Start Date', _formatDate(driverModel.hpStartDate), Icons.calendar_today_outlined, AppColors.lightprimary)),
                        const SizedBox(width: 10),
                        Expanded(child: _statCard(isDark, 'Last Payment', _formatDate(driverModel.hpLastPaymentDate), Icons.payment_outlined, Colors.teal)),
                      ],
                    ),
                    const SizedBox(height: 24),

                    // Payment history
                    Text(
                      'Payment History',
                      style: GoogleFonts.poppins(fontWeight: FontWeight.w600, fontSize: 15),
                    ),
                    const SizedBox(height: 10),
                    FutureBuilder<List<Map<String, dynamic>>>(
                      future: _loadPaymentHistory(),
                      builder: (context, snapshot) {
                        if (snapshot.connectionState == ConnectionState.waiting) {
                          return const Center(child: Padding(padding: EdgeInsets.all(24), child: CircularProgressIndicator()));
                        }
                        if (!snapshot.hasData || snapshot.data!.isEmpty) {
                          return Center(
                            child: Padding(
                              padding: const EdgeInsets.all(24),
                              child: Text('No payment records found'.tr,
                                  style: GoogleFonts.poppins(color: Colors.grey, fontSize: 13)),
                            ),
                          );
                        }
                        return ListView.separated(
                          shrinkWrap: true,
                          physics: const NeverScrollableScrollPhysics(),
                          itemCount: snapshot.data!.length,
                          separatorBuilder: (_, __) => const SizedBox(height: 8),
                          itemBuilder: (context, index) {
                            final p = snapshot.data![index];
                            final Timestamp? dateTs = p['date'] as Timestamp?;
                            final String dateStr = dateTs != null ? DateFormat('dd MMM yyyy').format(dateTs.toDate()) : '--';
                            final double amount = (p['amount'] as num?)?.toDouble() ?? 0;
                            final double balanceAfter = (p['balanceAfter'] as num?)?.toDouble() ?? 0;
                            final String type = (p['type'] as String?) ?? 'deduction';
                            final bool isPayment = type == 'payment';
                            return Container(
                              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                              decoration: BoxDecoration(
                                color: isDark ? AppColors.darkContainerBackground : AppColors.containerBackground,
                                borderRadius: BorderRadius.circular(8),
                                border: Border.all(
                                  color: isDark ? AppColors.darkContainerBorder : AppColors.containerBorder,
                                  width: 0.5,
                                ),
                              ),
                              child: Row(
                                children: [
                                  Container(
                                    width: 36,
                                    height: 36,
                                    decoration: BoxDecoration(
                                      color: isPayment ? Colors.green.withOpacity(0.1) : Colors.orange.withOpacity(0.1),
                                      shape: BoxShape.circle,
                                    ),
                                    child: Icon(
                                      isPayment ? Icons.arrow_downward : Icons.arrow_upward,
                                      size: 18,
                                      color: isPayment ? Colors.green : Colors.orange,
                                    ),
                                  ),
                                  const SizedBox(width: 10),
                                  Expanded(
                                    child: Column(
                                      crossAxisAlignment: CrossAxisAlignment.start,
                                      children: [
                                        Text(
                                          isPayment ? 'Payment Received' : 'Daily Deduction',
                                          style: GoogleFonts.poppins(fontWeight: FontWeight.w500, fontSize: 13),
                                        ),
                                        Text(
                                          dateStr,
                                          style: GoogleFonts.poppins(fontSize: 11, color: Colors.grey),
                                        ),
                                      ],
                                    ),
                                  ),
                                  Column(
                                    crossAxisAlignment: CrossAxisAlignment.end,
                                    children: [
                                      Text(
                                        _formatAmount(amount),
                                        style: GoogleFonts.poppins(
                                          fontWeight: FontWeight.w600,
                                          fontSize: 13,
                                          color: isPayment ? Colors.green : Colors.orange,
                                        ),
                                      ),
                                      Text(
                                        'Bal: ${_formatAmount(balanceAfter)}',
                                        style: GoogleFonts.poppins(fontSize: 10, color: Colors.grey),
                                      ),
                                    ],
                                  ),
                                ],
                              ),
                            );
                          },
                        );
                      },
                    ),
                  ],
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _statCard(bool isDark, String label, String value, IconData icon, Color iconColor) {
    return Container(
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: isDark ? AppColors.darkContainerBackground : AppColors.containerBackground,
        borderRadius: BorderRadius.circular(10),
        border: Border.all(
          color: isDark ? AppColors.darkContainerBorder : AppColors.containerBorder,
          width: 0.5,
        ),
        boxShadow: isDark
            ? null
            : [BoxShadow(color: Colors.grey.withOpacity(0.1), blurRadius: 6, offset: const Offset(0, 2))],
      ),
      child: Row(
        children: [
          Icon(icon, size: 20, color: iconColor),
          const SizedBox(width: 8),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(label, style: GoogleFonts.poppins(fontSize: 10, color: Colors.grey)),
                Text(value, style: GoogleFonts.poppins(fontWeight: FontWeight.w600, fontSize: 12), overflow: TextOverflow.ellipsis),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
