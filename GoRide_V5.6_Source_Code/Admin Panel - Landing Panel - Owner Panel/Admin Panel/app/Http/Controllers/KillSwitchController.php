<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Firestore;

class KillSwitchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function lock(Request $request, $driverId)
    {
        try {
            $database = app(Firestore::class)->database();

            $database->collection('driver_users')->document($driverId)->set([
                'appLocked'  => true,
                'isActive'   => false,
                'lockReason' => $request->input('reason', 'HP payment overdue - Red status'),
                'lockedAt'   => new \Google\Cloud\Core\Timestamp(new \DateTime()),
            ], ['merge' => true]);

            return response()->json([
                'success'  => true,
                'action'   => 'lock',
                'driverId' => $driverId,
                'message'  => 'Driver has been locked out.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to lock driver: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function unlock(Request $request, $driverId)
    {
        try {
            $database = app(Firestore::class)->database();

            $database->collection('driver_users')->document($driverId)->set([
                'appLocked'  => false,
                'isActive'   => true,
                'lockReason' => null,
                'lockedAt'   => null,
            ], ['merge' => true]);

            return response()->json([
                'success'  => true,
                'action'   => 'unlock',
                'driverId' => $driverId,
                'message'  => 'Driver has been unlocked.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to unlock driver: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function status($driverId)
    {
        try {
            $database = app(Firestore::class)->database();
            $doc = $database->collection('driver_users')->document($driverId)->snapshot();

            if (!$doc->exists()) {
                return response()->json(['success' => false, 'message' => 'Driver not found.'], 404);
            }

            $data = $doc->data();

            return response()->json([
                'success'    => true,
                'driverId'   => $driverId,
                'appLocked'  => $data['appLocked'] ?? false,
                'lockReason' => $data['lockReason'] ?? null,
                'lockedAt'   => isset($data['lockedAt']) ? (string) $data['lockedAt'] : null,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get status: ' . $e->getMessage(),
            ], 500);
        }
    }
}
