<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class GroupFactory extends Factory
{
    protected $model = Group::class;

    public function definition(): array
    {
        return [
            'teacher_id' => User::factory()->create(['role' => 'teacher'])->id,
            'name' => $this->faker->word . ' Group',
            'type' => 'general',
            'monthly_price' => 500,
        ];
    }
}
