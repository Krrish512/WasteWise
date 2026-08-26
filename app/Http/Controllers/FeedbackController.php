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
        if (auth()->check()) {
            $request->merge([
                'name'  => $request->input('name') ?: auth()->user()->name,
                'email' => $request->input('email') ?: auth()->user()->email,
            ]);
        }

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|max:255',
            'rating'      => 'required|integer|between:1,5',
            'ease_of_use'  => 'required|integer|between:1,5',
            'helpful'     => 'required|boolean',
            'experience'  => 'required|string|min:5|max:2000',
            'suggestion'  => 'nullable|string|max:2000',
            'recommend'   => 'required|boolean',
        ]);

        $feedbackData = [
            'user_id'     => auth()->check() ? auth()->id() : null,
            'name'        => $validated['name'],
            'email'       => $validated['email'],
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
