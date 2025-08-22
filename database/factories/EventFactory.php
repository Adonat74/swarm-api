<?php

namespace Database\Factories;

use App\Models\Group;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $groups = Group::all();

        $status = ['pending', 'approved', 'rejected'];

        return [
            'name' => $this->faker->jobTitle(),
            'description' => $this->faker->realText(300, 2),
            'date_time' => $this->faker->dateTimeBetween($startDate = '+ 5 days', $endDate = '+ 100 days'),
            'location' => $this->faker->city() . ',' . $this->faker->streetAddress(),
            'status' => $status[rand(0, 2)],
            'group_id' => $groups->random()->id,
        ];
    }
}
