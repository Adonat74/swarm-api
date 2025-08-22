<?php

namespace Database\Seeders;

use App\Models\Event;
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

        for ($i = 0; $i < count($events)+5; $i++) {
            DB::table('event_user')->insert([
                'event_id' => $events->random()->id,
                'user_id' => $users->random()->id,
                'participate' => rand(1, 2) === 1,
                'is_creator' => rand(1, 5) === 1,
            ]);
        }
    }
}
