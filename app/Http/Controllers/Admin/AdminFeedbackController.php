<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;

class AdminFeedbackController extends Controller
{
    /**
     * Display a listing of user feedbacks.
     */
    public function index(Request $request)
    {
        $query = Feedback::with('user');

        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        if ($request->filled('reviewed')) {
            $isReviewed = $request->reviewed === '1';
            $query->where('is_reviewed', $isReviewed);
        }

        $feedbacks = $query->orderBy('created_at', 'desc')->get();

        return view('admin.feedback.index', compact('feedbacks'));
    }

    /**
     * Display the specified feedback detail.
     */
    public function show(Feedback $feedback)
    {
        $feedback->load('user');

        return view('admin.feedback.show', compact('feedback'));
    }

    /**
     * Toggle the reviewed status of feedback.
     */
    public function toggleReviewed(Request $request, Feedback $feedback)
    {
        $feedback->is_reviewed = !$feedback->is_reviewed;
        $feedback->save();

        $statusText = $feedback->is_reviewed ? 'marked as Reviewed' : 'marked as Pending Review';

        return redirect()->back()
            ->with('success', "Feedback #{$feedback->id} has been {$statusText}.");
    }

    /**
     * Remove the specified feedback.
     */
    public function destroy(Feedback $feedback)
    {
        $feedbackId = $feedback->id;
        $feedback->delete();

        return redirect()->route('admin.feedback.index')
            ->with('success', "Feedback #{$feedbackId} deleted successfully!");
    }
}
