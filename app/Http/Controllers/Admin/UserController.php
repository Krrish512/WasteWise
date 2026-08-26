<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of registered users.
     */
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->get();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Toggle admin status for a user.
     */
    public function toggleAdmin(Request $request, User $user)
    {
        // Prevent an admin from removing their own admin privileges
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Security Warning: You cannot revoke your own administrator privileges.');
        }

        $user->is_admin = !$user->is_admin;
        $user->save();

        $statusLabel = $user->is_admin ? 'promoted to Administrator' : 'demoted to Normal User';

        return redirect()->route('admin.users.index')
            ->with('success', "User '{$user->email}' has been successfully {$statusLabel}.");
    }

    /**
     * Remove the specified user account.
     */
    public function destroy(User $user)
    {
        // Prevent an admin from deleting their own account
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Security Warning: You cannot delete your own logged-in account.');
        }

        $userEmail = $user->email;
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "User account '{$userEmail}' deleted successfully!");
    }
}
