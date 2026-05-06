<?php

namespace Database\Factories;

use App\Models\Subscription;
use App\Models\Student;
use App\Models\Group;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'group_id' => Group::factory(),
            'month' => '2026-05',
            'status' => 'unpaid',
        ];
    }
}
