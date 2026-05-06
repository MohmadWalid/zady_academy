<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\GroupSession;
use App\Models\User;
use App\Services\GroupService;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function __construct(private readonly GroupService $groupService) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $groups = Group::with('teacher')
            ->withCount('activeEnrollments')
            ->latest()
            ->paginate(12);

        $archived = Group::onlyTrashed()->with('teacher')->get();

        return view('admin.academic.groups.index', compact('groups', 'archived'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $teachers = User::where('role', 'teacher')->get();
        return view('admin.academic.groups.create', compact('teachers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:general,private,course',
            'monthly_price' => 'required|numeric|min:0',
            'teacher_id' => 'required|exists:users,id',
        ]);

        $this->groupService->create($data);

        return redirect()->route('admin.academic.groups.index')
            ->with('success', 'تم إنشاء المجموعة بنجاح.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $group = Group::with(['teacher', 'sessions', 'activeEnrollments.student', 'attendance'])
            ->findOrFail($id);

        return view('admin.academic.groups.show', compact('group'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $group = Group::findOrFail($id);
        $teachers = User::where('role', 'teacher')->get();
        return view('admin.academic.groups.edit', compact('group', 'teachers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $group = Group::findOrFail($id);
        
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:general,private,course',
            'monthly_price' => 'required|numeric|min:0',
            'teacher_id' => 'required|exists:users,id',
        ]);

        $group->update($data);

        return redirect()->route('admin.academic.groups.show', $group)
            ->with('success', 'تم تحديث بيانات المجموعة بنجاح.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $group = Group::findOrFail($id);
        $group->delete();

        return redirect()->route('admin.academic.groups.index')
            ->with('success', 'تم نقل المجموعة للأرشيف.');
    }

    /**
     * Restore the specified resource.
     */
    public function restore(string $id)
    {
        $group = Group::onlyTrashed()->findOrFail($id);
        $group->restore();

        return redirect()->route('admin.academic.groups.index')
            ->with('success', 'تم استعادة المجموعة بنجاح.');
    }

    /**
     * Add a session to a group.
     */
    public function addSession(Request $request, Group $group)
    {
        $data = $request->validate([
            'day' => 'required|string|in:Saturday,Sunday,Monday,Tuesday,Wednesday,Thursday,Friday',
            'time' => 'required',
        ]);

        try {
            $this->groupService->addSession($group, $data);
            return back()->with('success', 'تم إضافة الجلسة بنجاح.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove a session from a group.
     */
    public function removeSession(Group $group, GroupSession $session)
    {
        $this->groupService->deleteSession($session);
        return back()->with('success', 'تم حذف الجلسة بنجاح.');
    }
}
