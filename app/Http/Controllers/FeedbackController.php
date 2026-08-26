<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    /**
     * Show the public feedback form.
     */
    public function create()
    {
        return view('feedback');
    }

    /**
     * Store submitted feedback.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'nullable|string|max:255',
            'email'       => 'nullable|email|max:255',
            'rating'      => 'required|integer|between:1,5',
            'ease_of_use'  => 'required|integer|between:1,5',
            'helpful'     => 'required|boolean',
            'experience'  => 'required|string|max:2000',
            'suggestion'  => 'nullable|string|max:2000',
            'recommend'   => 'required|boolean',
        ]);

        $feedbackData = [
            'user_id'     => auth()->check() ? auth()->id() : null,
            'name'        => auth()->check() ? (auth()->user()->name ?: $validated['name']) : $validated['name'],
            'email'       => auth()->check() ? (auth()->user()->email ?: $validated['email']) : $validated['email'],
            'rating'      => $validated['rating'],
            'ease_of_use'  => $validated['ease_of_use'],
            'helpful'     => $validated['helpful'],
            'experience'  => $validated['experience'],
            'suggestion'  => $validated['suggestion'] ?? null,
            'recommend'   => $validated['recommend'],
            'is_reviewed' => false,
        ];

        Feedback::create($feedbackData);

        return redirect()->route('feedback.create')
            ->with('success', 'Thank you for your feedback! Your review helps us improve WasteWise.');
    }
}
