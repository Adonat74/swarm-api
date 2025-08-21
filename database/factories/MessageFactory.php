<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;


class MessageFactory extends Factory
{

    public function definition(): array
    {
        $groups = Group::all();
        $users = User::all();


        return [
            'body' => $this->faker->realText(300, 2),
            'type' => 'text',
            'group_id' => $groups->random()->id,
            'user_id' => $users->random()->id,
        ];
    }
}
