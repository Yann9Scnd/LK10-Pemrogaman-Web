<?php

namespace App\Http\Controllers;

use App\Http\Requests\FeedbackRequest;

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
    public function submitForm(FeedbackRequest $request)
    {
        // CODE REFACTOR (Refactoring 2): Clean controller using validated data from FeedbackRequest
        $validated = $request->validated();
        
        return back()->with('success_feedback', 'Feedback Anda berhasil dikirim dengan aman!');
    }
}
