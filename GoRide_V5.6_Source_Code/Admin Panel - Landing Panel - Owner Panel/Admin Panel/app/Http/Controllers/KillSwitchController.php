<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Factory;

class KillSwitchController extends Controller
{
    private $firestore;

    public function __construct()
    {
        $factory = (new Factory)->withServiceAccount(base_path('firebase.json'));
        $this->firestore = $factory->createFirestore()->database();
    }

    public function lock(Request $request, $driverId)
    {
        try {
            $this->firestore->collection('driver_users')->document($driverId)->update([
                ['path' => 'isActive', 'value' => false],
                ['path' => 'appLocked', 'value' => true],
                ['path' => 'lockReason', 'value' => $request->input('reason', 'HP payment overdue - Red status')],
                ['path' => 'lockedAt', 'value' => new \Google\Cloud\Core\Timestamp(new \DateTime())],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Driver app locked successfully',
                'driverId' => $driverId
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to lock driver: ' . $e->getMessage()
            ], 500);
        }
    }

    public function unlock(Request $request, $driverId)
    {
        try {
            $this->firestore->collection('driver_users')->document($driverId)->update([
                ['path' => 'isActive', 'value' => true],
                ['path' => 'appLocked', 'value' => false],
                ['path' => 'lockReason', 'value' => ''],
                ['path' => 'unlockedAt', 'value' => new \Google\Cloud\Core\Timestamp(new \DateTime())],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Driver app unlocked successfully',
                'driverId' => $driverId
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to unlock driver: ' . $e->getMessage()
            ], 500);
        }
    }

    public function status($driverId)
    {
        try {
            $doc = $this->firestore->collection('driver_users')->document($driverId)->snapshot();

            if (!$doc->exists()) {
                return response()->json(['success' => false, 'message' => 'Driver not found'], 404);
            }

            $data = $doc->data();

            return response()->json([
                'success' => true,
                'driverId' => $driverId,
                'isActive' => $data['isActive'] ?? true,
                'appLocked' => $data['appLocked'] ?? false,
                'lockReason' => $data['lockReason'] ?? '',
                'hpStatus' => $data['hpStatus'] ?? 'none'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get status: ' . $e->getMessage()
            ], 500);
        }
    }
}
