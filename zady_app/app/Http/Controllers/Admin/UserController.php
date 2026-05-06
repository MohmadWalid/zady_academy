<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AccessCodeService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct(private readonly AccessCodeService $accessCodeService) {}

    /**
     * Display a listing of all users (Staff and Parents).
     */
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%")
                  ->orWhere('access_code', 'like', "%{$request->search}%");
            });
        }

        $users = $query->latest()->paginate(20);
        $archived = User::onlyTrashed()->get();

        return view('admin.academic.users.index', compact('users', 'archived'));
    }

    /**
     * Show form to create staff members.
     */
    public function create()
    {
        return view('admin.academic.users.create');
    }

    /**
     * Store a newly created staff member.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|unique:users,phone',
            'role' => 'required|in:admin,secretary,teacher',
        ]);

        $data['access_code'] = $this->accessCodeService->generate();
        $data['created_by'] = auth()->id();

        $user = User::create($data);

        // Redirect to a specialized reveal screen as per PRD §4.0
        return redirect()->route('admin.academic.users.show', $user)
            ->with('reveal_code', true);
    }

    /**
     * Display user profile and access code.
     */
    public function show(User $user)
    {
        return view('admin.academic.users.show', compact('user'));
    }

    /**
     * Show form to edit user.
     */
    public function edit(User $user)
    {
        return view('admin.academic.users.edit', compact('user'));
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => ['required', 'string', Rule::unique('users')->ignore($user->id)],
            'role' => 'required|in:admin,secretary,teacher,parent',
        ]);

        $user->update($data);

        return redirect()->route('admin.academic.users.index')
            ->with('success', 'تم تحديث بيانات المستخدم بنجاح.');
    }

    /**
     * Remove the specified user (Soft delete).
     */
    public function destroy(User $user)
    {
        // Business rule (PRD §8.323): Block deletion of teacher with active groups
        if ($user->role === 'teacher' && $user->assignedGroups()->exists()) {
            return back()->with('error', 'لا يمكن حذف معلم مكلف بحلقات نشطة. قم بإعادة تكليف الحلقات أولاً.');
        }

        $user->delete();

        return redirect()->route('admin.academic.users.index')
            ->with('success', 'تم نقل المستخدم للأرشيف.');
    }

    /**
     * Restore a soft-deleted user.
     */
    public function restore(string $id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();

        return redirect()->route('admin.academic.users.index')
            ->with('success', 'تم استعادة المستخدم بنجاح.');
    }
}
