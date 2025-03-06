<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Region;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    public function index()
    {
        $regions = Region::all();
        $managers = User::whereIn('role_id', [2, 3])->get();
        // dd($managers);
        return view('page.index', compact('regions' ,'managers'));
    }
}
