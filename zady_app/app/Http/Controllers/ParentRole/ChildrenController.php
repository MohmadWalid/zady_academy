<?php

namespace App\Http\Controllers\ParentRole;

use App\Http\Controllers\Controller;
use App\Models\Student;

class ChildrenController extends Controller
{
    public function index()
    {
        $children = Student::where('parent_id', auth()->id())
            ->with(['activeEnrollments.group.teacher', 'attendance'])
            ->get();

        return view('parent.children.index', compact('children'));
    }
}
