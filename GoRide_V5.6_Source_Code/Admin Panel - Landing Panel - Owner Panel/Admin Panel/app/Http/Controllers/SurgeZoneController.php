<?php

namespace App\Http\Controllers;

class SurgeZoneController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('surge_zone.index');
    }
    public function edit($id)
    {
        return view('surge_zone.edit')->with('id',$id);
    }
}