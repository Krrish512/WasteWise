<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    /**
     * Display listing of published announcements.
     */
    public function index()
    {
        $announcements = Announcement::published()
            ->orderBy('published_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(9);

        return view('announcements.index', compact('announcements'));
    }

    /**
     * Display a specific published announcement by slug.
     */
    public function show(Announcement $announcement)
    {
        if (!$announcement->is_published) {
            abort(404);
        }

        return view('announcements.show', compact('announcement'));
    }
}
