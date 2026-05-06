<?php

namespace App\Services;

use App\Models\Student;
use App\Models\User;
use RuntimeException;

class StudentService
{
    public function __construct(private readonly AccessCodeService $accessCodeService) {}

    /**
     * Create a student and resolve (or create) the parent account.
     *
     * Returns an array:
     *   'student'  => Student
     *   'parent'   => User (role = parent)
     *   'new_code' => string|null — non-null only when a NEW parent was just created.
     *                 The controller must show the Code Reveal Screen when this is non-null.
     *
     * Business rules enforced (Implementation-Rules §2.5, PRD §8):
     *   1. Student phone must not already exist on another student record.
     *   2. Parent phone exists + role = parent  → reuse existing parent account.
     *   3. Parent phone exists + role ≠ parent  → reject ("هذا الرقم مسجل بدور مختلف").
     *   4. Parent phone not found               → create new parent + generate access_code.
     *
     * @throws RuntimeException on any conflict
     */
    public function createWithParent(
        array   $studentData,
        string  $parentPhone,
        ?string $parentName = null
    ): array {
        // Rule 1 — student phone uniqueness
        if (
            ! empty($studentData['phone']) &&
            Student::where('phone', $studentData['phone'])->exists()
        ) {
            throw new RuntimeException('هذا الرقم مسجل باسم طالب.');
        }

        // Resolve parent
        $existingUser = User::where('phone', $parentPhone)->first();
        $newCode      = null;

        if ($existingUser) {
            // Rule 3
            if ($existingUser->role !== 'parent') {
                throw new RuntimeException('هذا الرقم مسجل بدور مختلف.');
            }
            // Rule 2 — reuse
            $parent = $existingUser;

        } else {
            // Rule 4 — create new parent
            if (empty($parentName)) {
                throw new RuntimeException('اسم ولي الأمر مطلوب لإنشاء حساب جديد.');
            }

            $newCode = $this->accessCodeService->generate();

            $parent = User::create([
                'name'        => $parentName,
                'phone'       => $parentPhone,
                'role'        => 'parent',
                'access_code' => $newCode,
            ]);
        }

        $student = Student::create(
            array_merge($studentData, ['parent_id' => $parent->id])
        );

        return [
            'student'  => $student,
            'parent'   => $parent,
            'new_code' => $newCode,
        ];
    }
}
