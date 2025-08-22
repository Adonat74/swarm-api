<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $events = Event::all();
        $users = User::all();


        return [
            'body' => $this->faker->realText(150, 2),
            'likes' => rand(0, 10),
            'event_id' => $events->random()->id,
            'user_id' => $users->random()->id,
        ];
    }
}
