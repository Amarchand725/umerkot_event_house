<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Admin User
        |--------------------------------------------------------------------------
        */

        $admin = User::firstOrCreate(
            [
                'email' => 'admin@gmail.com',
            ],
            [
                'status_id'          => 1,
                'name'               => 'System Admin',
                'username'           => 'admin',
                'password'           => Hash::make('admin@123'),
                'avatar_id'           => null,
                'gender'              => 'M',
                'doj'                 => now()->toDateString(),
                'phone'               => null,
                'email_verified_at'   => now(),
                'phone_verified_at'   => now(),
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Admin Role
        |--------------------------------------------------------------------------
        */

        $admin->assignRole('Admin');

        $this->command->info('Admin user created successfully.');
    }
}