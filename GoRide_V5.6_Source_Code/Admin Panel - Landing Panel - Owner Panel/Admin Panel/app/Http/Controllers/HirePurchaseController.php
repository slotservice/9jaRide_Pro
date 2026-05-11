<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Firestore;

class HirePurchaseController extends Controller
{
    protected $firestore;

    public function __construct()
    {
        $this->middleware('auth');
    }

    protected function db()
    {
        return app(Firestore::class)->database();
    }

    // ── Views ──────────────────────────────────────────────────

    public function index()
    {
        return view('hire-purchase.index');
    }

    public function settings()
    {
        return view('hire-purchase.settings');
    }

    public function driverHP($id)
    {
        return view('hire-purchase.driver-hp', compact('id'));
    }

    // ── API: List all HP drivers ───────────────────────────────

    public function listDrivers()
    {
        try {
            $db = $this->db();
            $drivers = $db->collection('driver_users')
                ->where('hpEnabled', '=', true)
                ->documents();

            $result = [];
            foreach ($drivers as $driver) {
                if (!$driver->exists()) continue;
                $data = $driver->data();
                $result[] = [
                    'id'               => $driver->id(),
                    'fullName'         => ($data['firstName'] ?? '') . ' ' . ($data['lastName'] ?? ''),
                    'phone'            => $data['phoneNumber'] ?? '',
                    'hpTotalCost'      => floatval($data['hpTotalCost'] ?? 0),
                    'hpAmountPaid'     => floatval($data['hpAmountPaid'] ?? 0),
                    'hpBalance'        => floatval($data['hpBalance'] ?? 0),
                    'hpDailyDeduction' => floatval($data['hpDailyDeduction'] ?? 0),
                    'hpStatus'         => $data['hpStatus'] ?? 'green',
                    'hpLastPaymentDate'=> isset($data['hpLastPaymentDate']) ? (string) $data['hpLastPaymentDate'] : null,
                    'appLocked'        => $data['appLocked'] ?? false,
                ];
            }

            return response()->json(['success' => true, 'drivers' => $result]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ── API: Get single driver HP data ─────────────────────────

    public function getDriver($id)
    {
        try {
            $db = $this->db();
            $doc = $db->collection('driver_users')->document($id)->snapshot();

            if (!$doc->exists()) {
                return response()->json(['success' => false, 'message' => 'Driver not found.'], 404);
            }

            $data = $doc->data();

            // Get payment history
            $payments = $db->collection('driver_users')->document($id)
                ->collection('hp_payments')
                ->orderBy('date', 'DESC')
                ->limit(50)
                ->documents();

            $paymentHistory = [];
            foreach ($payments as $payment) {
                if (!$payment->exists()) continue;
                $pData = $payment->data();
                $paymentHistory[] = [
                    'id'           => $payment->id(),
                    'date'         => isset($pData['date']) ? (string) $pData['date'] : null,
                    'type'         => $pData['type'] ?? '',
                    'amount'       => floatval($pData['amount'] ?? 0),
                    'balanceAfter' => floatval($pData['balanceAfter'] ?? 0),
                ];
            }

            return response()->json([
                'success' => true,
                'driver'  => [
                    'id'               => $id,
                    'firstName'        => $data['firstName'] ?? '',
                    'lastName'         => $data['lastName'] ?? '',
                    'fullName'         => ($data['firstName'] ?? '') . ' ' . ($data['lastName'] ?? ''),
                    'phone'            => $data['phoneNumber'] ?? '',
                    'email'            => $data['email'] ?? '',
                    'profilePic'       => $data['profilePic'] ?? '',
                    'vehicleNumber'    => $data['vehicleNumber'] ?? '',
                    'hpEnabled'        => $data['hpEnabled'] ?? false,
                    'hpTotalCost'      => floatval($data['hpTotalCost'] ?? 0),
                    'hpAmountPaid'     => floatval($data['hpAmountPaid'] ?? 0),
                    'hpBalance'        => floatval($data['hpBalance'] ?? 0),
                    'hpDailyDeduction' => floatval($data['hpDailyDeduction'] ?? 0),
                    'hpStatus'         => $data['hpStatus'] ?? 'green',
                    'hpStartDate'      => isset($data['hpStartDate']) ? (string) $data['hpStartDate'] : null,
                    'hpLastPaymentDate'=> isset($data['hpLastPaymentDate']) ? (string) $data['hpLastPaymentDate'] : null,
                    'appLocked'        => $data['appLocked'] ?? false,
                    'lockReason'       => $data['lockReason'] ?? null,
                    'walletAmount'     => floatval($data['walletAmount'] ?? 0),
                ],
                'payments' => $paymentHistory,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ── API: Assign HP to a driver ─────────────────────────────

    public function assignDriver(Request $request)
    {
        $request->validate([
            'driverId'       => 'required|string',
            'totalCost'      => 'required|numeric|min:1',
            'initialPayment' => 'required|numeric|min:0',
            'dailyDeduction' => 'nullable|numeric|min:0',
        ]);

        try {
            $db = $this->db();

            // Get default deduction from HP settings if not provided
            $dailyDeduction = $request->input('dailyDeduction');
            if (empty($dailyDeduction)) {
                $hpSettings = $db->collection('settings')->document('hirePurchase')->snapshot();
                $dailyDeduction = $hpSettings->exists()
                    ? floatval($hpSettings->data()['defaultDailyDeduction'] ?? 500)
                    : 500;
            }

            $totalCost = floatval($request->input('totalCost'));
            $initialPayment = floatval($request->input('initialPayment'));
            $balance = $totalCost - $initialPayment;
            $now = new \Google\Cloud\Core\Timestamp(new \DateTime());

            $db->collection('driver_users')->document($request->input('driverId'))->set([
                'hpEnabled'        => true,
                'hpTotalCost'      => $totalCost,
                'hpAmountPaid'     => $initialPayment,
                'hpBalance'        => max(0, $balance),
                'hpDailyDeduction' => floatval($dailyDeduction),
                'hpStatus'         => 'green',
                'hpStartDate'      => $now,
                'hpLastPaymentDate'=> $now,
            ], ['merge' => true]);

            // Record initial payment if > 0
            if ($initialPayment > 0) {
                $db->collection('driver_users')->document($request->input('driverId'))
                    ->collection('hp_payments')->add([
                        'date'         => $now,
                        'type'         => 'Initial Payment',
                        'amount'       => $initialPayment,
                        'balanceAfter' => max(0, $balance),
                    ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'HP plan assigned successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ── API: Update driver HP details ──────────────────────────

    public function updateDriver(Request $request, $id)
    {
        $request->validate([
            'hpTotalCost'      => 'nullable|numeric|min:0',
            'hpAmountPaid'     => 'nullable|numeric|min:0',
            'hpDailyDeduction' => 'nullable|numeric|min:0',
            'hpStatus'         => 'nullable|string|in:green,yellow,red',
        ]);

        try {
            $db = $this->db();

            $updateData = [];

            if ($request->has('hpTotalCost')) {
                $updateData['hpTotalCost'] = floatval($request->input('hpTotalCost'));
            }
            if ($request->has('hpAmountPaid')) {
                $updateData['hpAmountPaid'] = floatval($request->input('hpAmountPaid'));
            }
            if ($request->has('hpDailyDeduction')) {
                $updateData['hpDailyDeduction'] = floatval($request->input('hpDailyDeduction'));
            }
            if ($request->has('hpStatus')) {
                $updateData['hpStatus'] = $request->input('hpStatus');
            }

            // Recalculate balance if cost or paid changed
            if (isset($updateData['hpTotalCost']) || isset($updateData['hpAmountPaid'])) {
                $doc = $db->collection('driver_users')->document($id)->snapshot();
                $data = $doc->exists() ? $doc->data() : [];
                $cost = $updateData['hpTotalCost'] ?? floatval($data['hpTotalCost'] ?? 0);
                $paid = $updateData['hpAmountPaid'] ?? floatval($data['hpAmountPaid'] ?? 0);
                $updateData['hpBalance'] = max(0, $cost - $paid);
            }

            if (empty($updateData)) {
                return response()->json(['success' => false, 'message' => 'No data to update.'], 400);
            }

            $db->collection('driver_users')->document($id)->set($updateData, ['merge' => true]);

            return response()->json([
                'success' => true,
                'message' => 'HP details updated.',
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ── API: Remove HP from driver ─────────────────────────────

    public function removeDriver($id)
    {
        try {
            $db = $this->db();

            $db->collection('driver_users')->document($id)->set([
                'hpEnabled'        => false,
                'hpTotalCost'      => 0,
                'hpAmountPaid'     => 0,
                'hpBalance'        => 0,
                'hpDailyDeduction' => 0,
                'hpStatus'         => '',
                'hpStartDate'      => null,
                'hpLastPaymentDate'=> null,
            ], ['merge' => true]);

            return response()->json([
                'success' => true,
                'message' => 'HP plan removed from driver.',
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ── API: Get HP settings ───────────────────────────────────

    public function getSettings()
    {
        try {
            $db = $this->db();
            $doc = $db->collection('settings')->document('hirePurchase')->snapshot();

            $defaults = [
                'defaultDailyDeduction' => 500,
                'yellowThresholdHours'  => 24,
                'redThresholdHours'     => 48,
                'autoKillSwitch'        => true,
                'royaltyPercentage'     => 2.5,
            ];

            $data = $doc->exists() ? array_merge($defaults, $doc->data()) : $defaults;

            return response()->json(['success' => true, 'settings' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ── API: Save HP settings ──────────────────────────────────

    public function saveSettings(Request $request)
    {
        $request->validate([
            'defaultDailyDeduction' => 'required|numeric|min:0',
            'yellowThresholdHours'  => 'required|numeric|min:1',
            'redThresholdHours'     => 'required|numeric|min:1',
            'autoKillSwitch'        => 'required|boolean',
            'royaltyPercentage'     => 'required|numeric|min:0|max:100',
        ]);

        try {
            $db = $this->db();

            $db->collection('settings')->document('hirePurchase')->set([
                'defaultDailyDeduction' => floatval($request->input('defaultDailyDeduction')),
                'yellowThresholdHours'  => floatval($request->input('yellowThresholdHours')),
                'redThresholdHours'     => floatval($request->input('redThresholdHours')),
                'autoKillSwitch'        => (bool) $request->input('autoKillSwitch'),
                'royaltyPercentage'     => floatval($request->input('royaltyPercentage')),
                'updatedAt'             => new \Google\Cloud\Core\Timestamp(new \DateTime()),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'HP settings saved.',
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ── API: Record manual payment ─────────────────────────────

    public function recordPayment(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'type'   => 'nullable|string',
        ]);

        try {
            $db = $this->db();

            $doc = $db->collection('driver_users')->document($id)->snapshot();
            if (!$doc->exists()) {
                return response()->json(['success' => false, 'message' => 'Driver not found.'], 404);
            }

            $data = $doc->data();
            $amount = floatval($request->input('amount'));
            $newAmountPaid = floatval($data['hpAmountPaid'] ?? 0) + $amount;
            $newBalance = floatval($data['hpTotalCost'] ?? 0) - $newAmountPaid;
            $now = new \Google\Cloud\Core\Timestamp(new \DateTime());

            $updateData = [
                'hpAmountPaid'     => $newAmountPaid,
                'hpBalance'        => max(0, $newBalance),
                'hpStatus'         => 'green',
                'hpLastPaymentDate'=> $now,
            ];

            // Unlock if locked
            if ($data['appLocked'] ?? false) {
                $updateData['appLocked'] = false;
                $updateData['isActive'] = true;
                $updateData['lockReason'] = '';
            }

            $db->collection('driver_users')->document($id)->set($updateData, ['merge' => true]);

            // Record payment
            $db->collection('driver_users')->document($id)
                ->collection('hp_payments')->add([
                    'date'         => $now,
                    'type'         => $request->input('type', 'Manual Payment'),
                    'amount'       => $amount,
                    'balanceAfter' => max(0, $newBalance),
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment recorded.',
                'newBalance' => max(0, $newBalance),
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
