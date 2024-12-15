<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class HomeAdminController extends Controller
{
    public function index()
    {
        // Ensure only admin can access this page
        if (Auth::user()->role !== 'admin') {
            return back()->with('error', 'You are not authorized to access this page.');
        }

        return view('admin.home');
    }
}
