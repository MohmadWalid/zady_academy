<?php

namespace App\Http\Controllers\Secretary;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Services\StudentService;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function __construct(private readonly StudentService $studentService) {}

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

        return view('secretary.academic.students.index', compact('students', 'archived'));
    }

    public function create()
    {
        return view('secretary.academic.students.create');
    }

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

            return redirect()->route('secretary.academic.students.show', $result['student'])
                ->with('success', 'تم إضافة الطالب بنجاح.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(string $id)
    {
        $student = Student::with(['parent', 'activeEnrollments.group.teacher', 'subscriptions.group', 'attendance'])->findOrFail($id);
        return view('secretary.academic.students.show', compact('student'));
    }

    public function edit(string $id)
    {
        $student = Student::findOrFail($id);
        return view('secretary.academic.students.edit', compact('student'));
    }

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

        return redirect()->route('secretary.academic.students.show', $student)
            ->with('success', 'تم تحديث بيانات الطالب بنجاح.');
    }

    public function destroy(string $id)
    {
        $student = Student::findOrFail($id);
        $student->delete();

        return redirect()->route('secretary.academic.students.index')
            ->with('success', 'تم حذف الطالب ونقله للأرشيف.');
    }

    public function restore(string $id)
    {
        $student = Student::onlyTrashed()->findOrFail($id);
        $student->restore();

        return redirect()->route('secretary.academic.students.index')
            ->with('success', 'تم استعادة الطالب بنجاح.');
    }
}
