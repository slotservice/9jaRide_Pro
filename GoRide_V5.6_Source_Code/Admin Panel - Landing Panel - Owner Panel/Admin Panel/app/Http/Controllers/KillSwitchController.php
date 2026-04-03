<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KillSwitchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function lock(Request $request, $driverId)
    {
        return response()->json([
            'success' => true,
            'action' => 'lock',
            'driverId' => $driverId,
            'reason' => $request->input('reason', 'HP payment overdue - Red status'),
            'message' => 'Kill switch lock command sent. Firestore update handled client-side.'
        ]);
    }

    public function unlock(Request $request, $driverId)
    {
        return response()->json([
            'success' => true,
            'action' => 'unlock',
            'driverId' => $driverId,
            'message' => 'Kill switch unlock command sent. Firestore update handled client-side.'
        ]);
    }

    public function status($driverId)
    {
        return response()->json([
            'success' => true,
            'driverId' => $driverId,
            'message' => 'Check driver status via Firestore client-side.'
        ]);
    }
}
