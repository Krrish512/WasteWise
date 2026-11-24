<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;

class AdminFeedbackController extends Controller
{
    /**
     * Display a listing of user feedbacks and centre issue reports.
     */
    public function index(Request $request)
    {
        $query = Feedback::with(['user', 'collectionCentre.city']);

        if ($request->filled('type')) {
            if ($request->type === 'centres') {
                $query->whereNotNull('collection_centre_id');
            } elseif ($request->type === 'general') {
                $query->whereNull('collection_centre_id');
            }
        }

        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        if ($request->filled('reviewed')) {
            $isReviewed = $request->reviewed === '1';
            $query->where('is_reviewed', $isReviewed);
        }

        $feedbacks = $query->orderBy('created_at', 'desc')->get();

        $centresNeedingReviewCount = Feedback::whereNotNull('collection_centre_id')
            ->where('is_reviewed', false)
            ->count();

        return view('admin.feedback.index', compact('feedbacks', 'centresNeedingReviewCount'));
    }

    /**
     * Display the specified feedback detail.
     */
    public function show(Feedback $feedback)
    {
        $feedback->load(['user', 'collectionCentre.city']);

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
     * One-click action: Mark reported collection centre as pending_verification.
     */
    public function markCentrePending(Feedback $feedback)
    {
        if ($feedback->collectionCentre) {
            $feedback->collectionCentre->update(['status' => 'pending_verification']);
        }

        $feedback->update(['is_reviewed' => true]);

        return redirect()->back()->with(
            'success',
            "Collection centre '{$feedback->collectionCentre?->name}' has been marked as pending_verification and report #{$feedback->id} resolved."
        );
    }

    /**
     * One-click action: Resolve report.
     */
    public function resolveReport(Feedback $feedback)
    {
        $feedback->update(['is_reviewed' => true]);

        return redirect()->back()->with('success', "Report #{$feedback->id} has been resolved.");
    }

    /**
     * Remove the specified feedback.
     */
    public function destroy(Feedback $feedback)
    {
        $feedback->delete();

        return redirect()->route('admin.feedback.index')
            ->with('success', 'Feedback entry deleted successfully!');
    }
}
