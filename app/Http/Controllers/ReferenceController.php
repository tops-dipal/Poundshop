<?php

namespace App\Http\Controllers;
use App\References;
use Illuminate\Http\Request;
use Route;

class ReferenceController extends Controller
{
    //
    public function index()
    {
    	$reference_data=References::get();
        return view('reference.index', compact('reference_data'));
    }
}
