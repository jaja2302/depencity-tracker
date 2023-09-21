<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TrackerController extends Controller
{
    //
    public function dashboard()
    {
        // Your dashboard logic goes here
        return view('Tracking.dashboard');
    }
}
