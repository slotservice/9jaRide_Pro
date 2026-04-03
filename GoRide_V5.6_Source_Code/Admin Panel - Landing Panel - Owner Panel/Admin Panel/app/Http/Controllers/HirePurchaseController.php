<?php

namespace App\Http\Controllers;

class HirePurchaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

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
}
