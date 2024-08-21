<?php

namespace Database\Seeders;


use App\Models\User;
use App\Models\Train;
use App\Models\Notification;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UserSeeder::class);

        // Create 10 users
        $users = User::factory(2)->create();

        // Create 5 trains
        $trains = Train::factory(1)->create();

        // Create 20 notifications
        Notification::factory(1)->create([
            'motion_detected' => function () use ($trains) {
                return $trains->random()->motion_name;
            },
            'level' => function (array $attributes) use ($trains) {
                $train = $trains->where('motion_name', $attributes['motion_detected'])->first();
                return $train ? $train->level : 'normal';
            },
            'user_id' => function () use ($users) {
                return $users->random()->user_id;
            },
        ]);
    }
}
