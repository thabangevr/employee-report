<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Okr;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Okr> */
class OkrFactory extends Factory
{
    protected $model = Okr::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(4),
            'objective_description' => fake()->paragraph(),
            'measure_of_success' => fake()->sentence(),
            'weight' => fake()->randomElement([10, 20, 25, 30]),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => [
            'is_active' => false,
        ]);
    }
}
