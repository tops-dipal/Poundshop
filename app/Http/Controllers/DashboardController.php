<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(){
        $welcomeMessage="Welcome to PoundShop Dashboard";
        return view('dashboard',compact('welcomeMessage'));
    }
}
