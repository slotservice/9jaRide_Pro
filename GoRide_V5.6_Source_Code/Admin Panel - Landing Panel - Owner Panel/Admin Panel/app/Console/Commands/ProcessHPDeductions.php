<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Kreait\Firebase\Factory;

class ProcessHPDeductions extends Command
{
    protected $signature = 'hp:process-deductions';
    protected $description = 'Process daily hire-purchase deductions from driver wallets';

    public function handle()
    {
        try {
            $factory = (new Factory)->withServiceAccount(base_path('firebase.json'));
            $firestore = $factory->createFirestore()->database();

            // Get HP settings
            $hpSettings = $firestore->collection('settings')->document('hirePurchase')->snapshot();
            $settings = $hpSettings->data();
            $yellowThreshold = ($settings['yellowThresholdHours'] ?? 24) * 3600;
            $redThreshold = ($settings['redThresholdHours'] ?? 48) * 3600;
            $autoKill = $settings['autoKillSwitch'] ?? true;

            // Get all HP-enabled drivers
            $drivers = $firestore->collection('driver_users')
                ->where('hpEnabled', '=', true)
                ->documents();

            $processed = 0;
            $warnings = 0;
            $lockouts = 0;

            foreach ($drivers as $driver) {
                if (!$driver->exists()) continue;

                $data = $driver->data();
                $driverId = $driver->id();
                $balance = floatval($data['hpBalance'] ?? 0);
                $dailyDeduction = floatval($data['hpDailyDeduction'] ?? 0);
                $walletBalance = floatval($data['walletAmount'] ?? 0);

                if ($balance <= 0 || $dailyDeduction <= 0) continue;

                $now = time();
                $lastPayment = isset($data['hpLastPaymentDate']) ? $data['hpLastPaymentDate']->get()->getTimestamp() : $now;
                $timeSincePayment = $now - $lastPayment;

                // Check if wallet has enough for deduction
                if ($walletBalance >= $dailyDeduction) {
                    // Process deduction
                    $newWallet = $walletBalance - $dailyDeduction;
                    $newAmountPaid = floatval($data['hpAmountPaid'] ?? 0) + $dailyDeduction;
                    $newBalance = floatval($data['hpTotalCost'] ?? 0) - $newAmountPaid;

                    $updateData = [
                        ['path' => 'walletAmount', 'value' => $newWallet],
                        ['path' => 'hpAmountPaid', 'value' => $newAmountPaid],
                        ['path' => 'hpBalance', 'value' => max(0, $newBalance)],
                        ['path' => 'hpStatus', 'value' => 'green'],
                        ['path' => 'hpLastPaymentDate', 'value' => new \Google\Cloud\Core\Timestamp(new \DateTime())],
                    ];

                    if ($data['appLocked'] ?? false) {
                        $updateData[] = ['path' => 'appLocked', 'value' => false];
                        $updateData[] = ['path' => 'isActive', 'value' => true];
                        $updateData[] = ['path' => 'lockReason', 'value' => ''];
                    }

                    $firestore->collection('driver_users')->document($driverId)->update($updateData);

                    // Record payment
                    $firestore->collection('driver_users')->document($driverId)
                        ->collection('hp_payments')->add([
                            'date' => new \Google\Cloud\Core\Timestamp(new \DateTime()),
                            'type' => 'Auto Deduction',
                            'amount' => $dailyDeduction,
                            'balanceAfter' => max(0, $newBalance),
                            'walletAfter' => $newWallet,
                        ]);

                    $processed++;
                } else {
                    // Wallet insufficient - update status based on time
                    if ($timeSincePayment >= $redThreshold) {
                        $updateData = [['path' => 'hpStatus', 'value' => 'red']];
                        if ($autoKill && !($data['appLocked'] ?? false)) {
                            $updateData[] = ['path' => 'appLocked', 'value' => true];
                            $updateData[] = ['path' => 'isActive', 'value' => false];
                            $updateData[] = ['path' => 'lockReason', 'value' => 'HP payment overdue - automatic lockout'];
                            $updateData[] = ['path' => 'lockedAt', 'value' => new \Google\Cloud\Core\Timestamp(new \DateTime())];
                            $lockouts++;
                        }
                        $firestore->collection('driver_users')->document($driverId)->update($updateData);
                    } elseif ($timeSincePayment >= $yellowThreshold) {
                        $firestore->collection('driver_users')->document($driverId)->update([
                            ['path' => 'hpStatus', 'value' => 'yellow'],
                        ]);
                        $warnings++;
                    }
                }
            }

            $this->info("HP Deductions processed: {$processed} deducted, {$warnings} warnings, {$lockouts} lockouts");

        } catch (\Exception $e) {
            $this->error('HP Deduction Error: ' . $e->getMessage());
        }
    }
}
