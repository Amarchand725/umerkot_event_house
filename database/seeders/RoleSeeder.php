<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Admin',
                'guard_name' => 'user',
            ],
            [
                'name' => 'Manager',
                'guard_name' => 'user',
            ],
            [
                'name' => 'Staff',
                'guard_name' => 'user',
            ],
            [
                'name' => 'Accountant',
                'guard_name' => 'user',
            ],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                [
                    'name' => $role['name'],
                    'guard_name' => $role['guard_name'],
                ]
            );
        }

        $this->command->info('Roles created successfully.');
    }
}