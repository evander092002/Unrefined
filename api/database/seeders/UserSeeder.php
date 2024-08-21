<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'first_name' => 'Luis',
                'last_name' => 'Suizo',
                'middle_initial' => 'G',
                'gender' => 'male',
                'phone_no' => '+639810921795',
                'status' => 'active',
                'role' => 'client',
                'username' => 'default1',
                'password' => Hash::make('default'), 
            ],
            [
                'first_name' => 'Reynaldo',
                'last_name' => 'Baja',
                'middle_initial' => 'A',
                'gender' => 'male',
                'phone_no' => '+639777466101',
                'status' => 'active',
                'role' => 'admin',
                'username' => 'default2',
                'password' => Hash::make('default'), 
            ],
            [
                'first_name' => 'Ralph',
                'last_name' => 'Hernandez',
                'middle_initial' => 'B',
                'gender' => 'male',
                'phone_no' => '+639458941145',
                'status' => 'active',
                'role' => 'admin',
                'username' => 'default3',
                'password' => Hash::make('default'), 
            ],
            [
                'first_name' => 'Rochel Mae',
                'last_name' => 'Cocjin',
                'middle_initial' => 'R',
                'gender' => 'female',
                'phone_no' => '+639156783366',
                'status' => 'active',
                'role' => 'admin',
                'username' => 'default4',
                'password' => Hash::make('default'), 
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
