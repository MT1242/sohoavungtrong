<?php

namespace App\Http\Controllers;

use App\Region;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    public function index()
    {
        $regions = Region::all();
        // dd($regions);
        
        return view('page.index', compact('regions'));
    }
}
