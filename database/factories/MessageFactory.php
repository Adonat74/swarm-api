<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;


class MessageFactory extends Factory
{

    public function definition(): array
    {
        $user = User::all()->random();
        $group = $user->groups->random();

        return [
            'body' => $this->faker->realText(300, 2),
            'type' => 'text',
            'group_id' => $group->id,
            'user_id' => $user->id,
        ];
    }
}
