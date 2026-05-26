<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear admin desde .env
        User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL')],
            [
                'name'     => env('ADMIN_NAME'),
                'password' => bcrypt(env('ADMIN_PASSWORD')),
                'role'     => 'admin',
            ]
        );

        // Otros usuarios fijos
        $users = [
            ['name' => 'Ana Torres', 'email' => 'ana@example.com', 'password' => 'password', 'role' => 'admin'],
            ['name' => 'Luis Moreno', 'email' => 'luis@example.com', 'password' => 'password', 'role' => 'editor'],
            ['name' => 'Marta Rivas', 'email' => 'marta@example.com', 'password' => 'password', 'role' => 'user'],
            ['name' => 'Sergio Vidal', 'email' => 'sergio@example.com', 'password' => 'password', 'role' => 'user'],
            ['name' => 'Paula Gil', 'email' => 'paula@example.com', 'password' => 'password', 'role' => 'user'],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                [
                    'name'     => $user['name'],
                    'password' => bcrypt($user['password']),
                    'role'     => $user['role'],
                ]
            );
        }
    }
}