<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use App\Services\StudentService;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function __construct(private readonly StudentService $studentService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Student::with('parent');

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%")
                  ->orWhereHas('parent', function($q) use ($request) {
                      $q->where('name', 'like', "%{$request->search}%")
                        ->orWhere('phone', 'like', "%{$request->search}%");
                  });
        }

        $students = $query->latest()->paginate(15);
        $archived = Student::onlyTrashed()->with('parent')->latest()->get();

        return view('admin.academic.students.index', compact('students', 'archived'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.academic.students.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_phone' => 'required|string',
            'parent_name' => 'required_if:is_new_parent,true|nullable|string',
            'age' => 'required|integer|min:4',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        try {
            $result = $this->studentService->createWithParent(
                $request->only(['name', 'age', 'phone', 'address']),
                $request->parent_phone,
                $request->parent_name
            );

            if ($result['new_code']) {
                session()->flash('new_access_code', $result['new_code']);
            }

            return redirect()->route('admin.academic.students.show', $result['student'])
                ->with('success', 'تم إضافة الطالب بنجاح.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $student = Student::with(['parent', 'activeEnrollments.group.teacher', 'subscriptions.group', 'attendance'])->findOrFail($id);
        
        return view('admin.academic.students.show', compact('student'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $student = Student::findOrFail($id);
        return view('admin.academic.students.edit', compact('student'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $student = Student::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'age' => 'required|integer|min:4',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        $student->update($request->only(['name', 'age', 'phone', 'address']));

        return redirect()->route('admin.academic.students.show', $student)
            ->with('success', 'تم تحديث بيانات الطالب بنجاح.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $student = Student::findOrFail($id);
        $student->delete();

        return redirect()->route('admin.academic.students.index')
            ->with('success', 'تم نقل الطالب للأرشيف.');
    }

    /**
     * Restore the specified resource.
     */
    public function restore(string $id)
    {
        $student = Student::onlyTrashed()->findOrFail($id);
        $student->restore();

        return redirect()->route('admin.academic.students.index')
            ->with('success', 'تم استعادة الطالب بنجاح.');
    }
}
