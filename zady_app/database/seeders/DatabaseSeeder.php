<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Student;
use App\Models\Group;
use App\Models\Enrollment;
use App\Models\Subscription;
use App\Models\Payment;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Admins and Secretary
        $admin = User::firstOrCreate(
            ['phone' => '0500000000'],
            [
                'name' => 'المدير العام',
                'role' => 'admin',
                'access_code' => 'ZADY-ADMN',
            ]
        );

        $secretary = User::firstOrCreate(
            ['phone' => '0500000001'],
            [
                'name' => 'السكرتير',
                'role' => 'secretary',
                'access_code' => 'ZADY-SECR',
            ]
        );

        // For auditing
        auth()->login($admin);

        // 2. Create Teachers
        $teachers = User::factory(5)->create(['role' => 'teacher']);

        // 3. Create Groups
        $groups = collect();
        foreach ($teachers as $teacher) {
            $groups->push(Group::factory()->create([
                'teacher_id' => $teacher->id,
                'created_by' => $admin->id,
            ]));
            $groups->push(Group::factory()->create([
                'teacher_id' => $teacher->id,
                'created_by' => $admin->id,
            ]));
        }

        // Add Group Sessions
        foreach ($groups as $group) {
            DB::table('group_sessions')->insert([
                ['group_id' => $group->id, 'day' => 'Sunday', 'time' => '16:00:00', 'created_at' => now(), 'updated_at' => now()],
                ['group_id' => $group->id, 'day' => 'Tuesday', 'time' => '16:00:00', 'created_at' => now(), 'updated_at' => now()]
            ]);
        }

        // 4. Create Parents and Students
        $parents = User::factory(20)->create(['role' => 'parent']);
        $students = collect();
        foreach ($parents as $parent) {
            // 1-3 students per parent
            $studentCount = rand(1, 3);
            for ($i = 0; $i < $studentCount; $i++) {
                $students->push(Student::factory()->create([
                    'parent_id' => $parent->id,
                    'created_by' => $secretary->id,
                ]));
            }
        }

        // 5. Enroll Students in Groups
        $enrollments = collect();
        foreach ($students as $student) {
            // enroll in 1-2 groups
            $studentGroups = $groups->random(rand(1, 2));
            foreach ($studentGroups as $group) {
                $enrollments->push(Enrollment::factory()->create([
                    'student_id' => $student->id,
                    'group_id' => $group->id,
                    'created_by' => $secretary->id,
                ]));
            }
        }

        // 6. Generate Subscriptions and Payments for current month
        $currentMonth = date('Y-m');
        foreach ($enrollments as $enrollment) {
            $sub = Subscription::factory()->create([
                'student_id' => $enrollment->student_id,
                'group_id' => $enrollment->group_id,
                'month' => $currentMonth,
                'status' => 'unpaid',
                'created_by' => $admin->id,
            ]);

            // Randomly pay some subscriptions
            if (rand(1, 10) > 4) { // 60% paid
                $sub->update(['status' => 'paid']);
                Payment::factory()->create([
                    'subscription_id' => $sub->id,
                    'status' => 'approved',
                    'method' => 'cash',
                ]);
            } elseif (rand(1, 10) > 8) { // 20% pending
                $sub->update(['status' => 'pending']);
                Payment::factory()->create([
                    'subscription_id' => $sub->id,
                    'status' => 'pending',
                    'method' => 'transfer',
                    'proof_image' => 'fake_proof.jpg',
                ]);
            }
        }

        // 7. Generate Attendance
        $dates = [
            Carbon::now()->subDays(2)->format('Y-m-d'),
            Carbon::now()->subDays(1)->format('Y-m-d'),
            Carbon::now()->format('Y-m-d'),
        ];

        foreach ($dates as $date) {
            foreach ($groups as $group) {
                $enrolledStudents = $enrollments->where('group_id', $group->id)->pluck('student_id');
                foreach ($enrolledStudents as $studentId) {
                    Attendance::factory()->create([
                        'student_id' => $studentId,
                        'group_id' => $group->id,
                        'date' => $date,
                        'present' => rand(0, 1) === 1,
                        'taken_by' => $group->teacher_id,
                        'created_by' => $group->teacher_id,
                    ]);
                }
            }
        }
    }
}
