<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\JadwalKajian;

class DashboardController extends Controller
{
    public function index() {
        
        $totalUser = User::count();
        $totalKajian = JadwalKajian::count();

        return view('dashboard.dashboard', compact('totalUser', 'totalKajian'));
    }
}
