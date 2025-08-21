<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $events = Event::all();
        $users = User::all();

        $status = ['pending', 'approved', 'rejected'];

        for ($i = 0; $i < count($events)+5; $i++) {
            DB::table('group_user')->insert([
                'group_id' => $events->random()->id,
                'user_id' => $users->random()->id,
                'is_creator' => rand(1, 5) === 1,
                'status' => $status[rand(0, 2)],
            ]);
        }    }
}
