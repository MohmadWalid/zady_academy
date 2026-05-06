<?php

namespace App\Http\Controllers\Secretary;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\GroupSession;
use App\Models\User;
use App\Services\GroupService;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function __construct(private readonly GroupService $groupService) {}

    public function index()
    {
        $groups = Group::with('teacher')
            ->withCount('activeEnrollments')
            ->latest()
            ->paginate(12);

        return view('secretary.academic.groups.index', compact('groups'));
    }

    public function create()
    {
        $teachers = User::where('role', 'teacher')->get();
        return view('secretary.academic.groups.create', compact('teachers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:general,private,course',
            'monthly_price' => 'required|numeric|min:0',
            'teacher_id' => 'required|exists:users,id',
        ]);

        $this->groupService->create($data);

        return redirect()->route('secretary.academic.groups.index')
            ->with('success', 'تم إنشاء المجموعة بنجاح.');
    }

    public function show(string $id)
    {
        $group = Group::with(['teacher', 'sessions', 'activeEnrollments.student', 'attendance'])
            ->findOrFail($id);

        return view('secretary.academic.groups.show', compact('group'));
    }

    public function edit(string $id)
    {
        $group = Group::findOrFail($id);
        $teachers = User::where('role', 'teacher')->get();
        return view('secretary.academic.groups.edit', compact('group', 'teachers'));
    }

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

        return redirect()->route('secretary.academic.groups.show', $group)
            ->with('success', 'تم تحديث بيانات المجموعة بنجاح.');
    }

    public function destroy(string $id)
    {
        $group = Group::findOrFail($id);
        $group->delete();

        return redirect()->route('secretary.academic.groups.index')
            ->with('success', 'تم حذف المجموعة.');
    }

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

    public function removeSession(Group $group, GroupSession $session)
    {
        $this->groupService->deleteSession($session);
        return back()->with('success', 'تم حذف الجلسة بنجاح.');
    }
}
