<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = \App\Models\User::where('is_admin', false)->withCount('orders')->latest()->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function show(\App\Models\User $user)
    {
        $user->load(['orders.service', 'orders.package', 'directMessages.sender']);

        // Mark direct messages from this client as read
        \App\Models\DirectMessage::where('client_id', $user->id)
            ->where('is_read', false)
            ->whereHas('sender', function ($q) {
            $q->where('is_admin', false);
        })->update(['is_read' => true]);

        return view('admin.users.show', compact('user'));
    }

    public function destroy(\App\Models\User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
}
