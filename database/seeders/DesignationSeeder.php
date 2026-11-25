<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Designation;

class DesignationSeeder extends Seeder
{
    public function run(): void
    {
        $designations = [
            'Event Manager',
            'Sound Engineer',
            'Lighting Technician',
            'Electrician',
            'Office Assistant',
            'Worker',
        ];

        foreach ($designations as $designation) {
            Designation::firstOrCreate([
                'name' => $designation,
            ]);
        }
    }
}
