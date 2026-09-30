<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{

    public function __construct()
    {
    }
    public function dashboard(){
        $title = Auth::user()->name . "'s Dashboard";
        
        return view('back-office.dashboard', get_defined_vars());
    }
    public function profile(){
        $title = Auth::user()->name . "'s Profile";
        return view('back-office.profile', get_defined_vars());
    }
}