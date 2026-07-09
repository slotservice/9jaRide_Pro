<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AssistanceTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('assistance_type.index');
    }
}
