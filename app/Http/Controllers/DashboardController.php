<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the protected dashboard.
     */
    public function index()
    {
        return view('dashboard');
    }

    /**
     * Handle the input validation form submission.
     */
    public function submitForm(Request $request)
    {
        // Terapkan validasi input yang ketat
        $validated = $request->validate([
            'name' => 'required|string|min:3|max:50',
            'email' => 'required|email|max:100',
            'feedback' => 'required|string|min:10|max:1000',
        ]);
        
        return back()->with('success_feedback', 'Feedback Anda berhasil dikirim dengan aman!');
    }
}
