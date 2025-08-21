<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GroupUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $groups = Group::all();
        $users = User::all();


        for ($i = 0; $i < count($groups)+5; $i++) {
            DB::table('group_user')->insert([
                'group_id' => $groups->random()->id,
                'user_id' => $users->random()->id,
                'participate' => rand(1, 2) === 1,
                'is_creator' => rand(1, 5) === 1,
            ]);
        }
    }
}
