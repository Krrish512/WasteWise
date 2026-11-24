<?php

namespace App\Http\Controllers;

use App\Models\CollectionCentre;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
     * Store submitted general feedback.
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
            'ease_of_use' => 'required|integer|between:1,5',
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
            'ease_of_use' => $validated['ease_of_use'],
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

    /**
     * Report an issue with a specific collection centre via AJAX.
     * Rate limited to throttle:10,1.
     */
    public function reportCentre(Request $request, CollectionCentre $collectionCentre)
    {
        $validated = $request->validate([
            'issue_type'  => ['required', 'string', Rule::in([
                'permanently_closed',
                'wrong_address_or_map',
                'does_not_accept_public_dropoff',
                'wrong_waste_category',
                'wrong_phone'
            ])],
            'experience'  => 'nullable|string|max:1000',
            'name'        => 'nullable|string|max:255',
            'email'       => 'nullable|email|max:255',
        ]);

        $userName = auth()->check() ? auth()->user()->name : ($validated['name'] ?? 'Citizen Report');
        $userEmail = auth()->check() ? auth()->user()->email : ($validated['email'] ?? null);

        $report = Feedback::create([
            'user_id'              => auth()->id(),
            'collection_centre_id' => $collectionCentre->id,
            'issue_type'           => $validated['issue_type'],
            'name'                 => $userName,
            'email'                => $userEmail,
            'experience'           => $validated['experience'] ?? "Issue reported: " . str_replace('_', ' ', $validated['issue_type']),
            'is_reviewed'          => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Thank you! Your report has been submitted to the WasteWise verification team.',
            'report_id' => $report->id,
        ], 200);
    }
}
