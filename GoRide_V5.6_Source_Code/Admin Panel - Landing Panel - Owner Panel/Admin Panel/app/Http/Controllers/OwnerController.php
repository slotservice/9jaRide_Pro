<?php

namespace App\Http\Controllers;

class OwnerController extends Controller
{   

    public function __construct()
    {
        $this->middleware('auth');
    }
	public function index()
    {
        return view("owners.index");
    }
    public function edit($id)
    {
    	return view('owners.edit')->with('id', $id);
    }
    public function view($id)
    {
        return view('owners.view')->with('id', $id);
    }
    public function ownerDocuments($id)
    {
        return view('owners.documentIndex', compact('id'));
    }
    public function ownerDocumentUpload($ownerId, $id)
    {
        return view('owners.documentUpload', compact('ownerId', 'id'));
    }
    public function ownerChat($id)
    {
        return view('owners.chat', compact('id'));
    }
}


