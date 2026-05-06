<?php

namespace App\Http\Controllers\Secretary;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of Parents.
     */
    public function index(Request $request)
    {
        $query = User::where('role', 'parent');

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%")
                  ->orWhere('access_code', 'like', "%{$request->search}%");
            });
        }

        $users = $query->latest()->paginate(20);

        return view('secretary.academic.users.index', compact('users'));
    }

    /**
     * Show parent details (including access code for support).
     */
    public function show(User $user)
    {
        if ($user->role !== 'parent') {
            abort(403);
        }

        $user->load('children.activeEnrollments.group');

        return view('secretary.academic.users.show', compact('user'));
    }

    /**
     * Update parent basic info.
     */
    public function update(Request $request, User $user)
    {
        if ($user->role !== 'parent') {
            abort(403);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => ['required', 'string', 'unique:users,phone,' . $user->id],
        ]);

        $user->update($data);

        return back()->with('success', 'تم تحديث بيانات ولي الأمر.');
    }
}
