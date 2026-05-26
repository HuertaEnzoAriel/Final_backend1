<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@example.com')],
            [
                'name'     => env('ADMIN_NAME', 'Admin'),
                'password' => env('ADMIN_PASSWORD', 'password'),
                'role'     => 'admin',
            ]
        );

        $users = [
            ['name' => 'Luis Moreno', 'email' => 'luis@example.com', 'role' => 'editor'],
            ['name' => 'Marta Rivas', 'email' => 'marta@example.com', 'role' => 'user'],
            ['name' => 'Sergio Vidal', 'email' => 'sergio@example.com', 'role' => 'user'],
            ['name' => 'Paula Gil', 'email' => 'paula@example.com', 'role' => 'user'],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                [
                    'name'     => $user['name'],
                    'password' => 'password',
                    'role'     => $user['role'],
                ]
            );
        }
    }
}
