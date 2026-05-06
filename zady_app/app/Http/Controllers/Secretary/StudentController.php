<?php

namespace App\Http\Controllers\Secretary;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Services\StudentService;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function __construct(private readonly StudentService $studentService) {}

    public function index()
    {
        $students = Student::with('parent')->latest()->paginate(15);
        return view('secretary.academic.students.index', compact('students'));
    }

    public function create()
    {
        return view('secretary.academic.students.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'parent_phone' => 'required|string',
            'parent_name' => 'required|string',
            'age' => 'required|integer|min:4',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        $this->studentService->createWithParent($data);

        return redirect()->route('secretary.academic.students.index')
            ->with('success', 'تم إضافة الطالب بنجاح.');
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
        
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'age' => 'required|integer|min:4',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        $student->update($data);

        return redirect()->route('secretary.academic.students.show', $student)
            ->with('success', 'تم تحديث بيانات الطالب بنجاح.');
    }

    public function destroy(string $id)
    {
        $student = Student::findOrFail($id);
        $student->delete();

        return redirect()->route('secretary.academic.students.index')
            ->with('success', 'تم حذف الطالب.');
    }
}
