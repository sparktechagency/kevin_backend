<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(SubscriptionSeeder::class);

        $demoUsers = [
            [
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('12345678'),
                'email_verified_at'=>now(),
                'contact_number' => '01710000001',
                'avatar' => 'default/profile.png',
                'role' => 'ADMIN',
                'is_banned' => false,
            ],
            [
                'name' => 'Manager',
                'email' => 'manager@gmail.com',
                'password' => Hash::make('12345678'),
                'email_verified_at'=>now(),
                'contact_number' => '01710000002',
                'avatar' => 'default/profile.png',
                'role' => 'MANAGER',
                'is_banned' => false,
            ],
            [
                'name' => 'User',
                'email' => 'user@gamil.com',
                'password' => Hash::make('12345678'),
                'email_verified_at'=>now(),
                'contact_number' => '01710000003',
                'avatar' => 'default/profile.png',
                'role' => 'USER',
                'is_banned' => false,
            ],
            [
                'name' => 'Employee',
                'email' => 'employee@gmail.com',
                // 'password' => Hash::make('12345678'),
                'employee_pin' => '123456',
                'contact_number' => '01710000004',
                'avatar' => 'default/profile.png',
                'role' => 'EMPLOYEE',
                'is_banned' => false,
            ],
            [
                'name' => 'Mentor',
                'email' => 'mentor@gmail.com',
                // 'password' => Hash::make('12345678'),
                'employee_pin' => '123123',
                'contact_number' => '01710000005',
                'avatar' => 'default/profile.png',
                'role' => 'MENTOR',
                'is_banned' => false,
            ],
        ];

        foreach ($demoUsers as $user) {
            User::create($user);
        }
       $this->call([
            CategorySeeder::class,
        ]);
    }

}
