<?php

namespace Database\Factories;

use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        return [
            'parent_id' => User::factory()->create(['role' => 'parent'])->id,
            'name' => $this->faker->name,
            'phone' => $this->faker->unique()->numerify('05########'),
            'age' => $this->faker->numberBetween(5, 20),
        ];
    }
}
