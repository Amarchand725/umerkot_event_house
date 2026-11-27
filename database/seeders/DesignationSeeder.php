<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Designation\Models\Designation;

class DesignationSeeder extends Seeder
{
    public function run(): void
    {
        $designations = [
            'Event Manager' => 'Plans and oversees event operations.',
            'Event Coordinator' => 'Coordinates event schedules, vendors, and on-site teams.',
            'Sound Engineer' => 'Sets up and operates event sound equipment.',
            'Lighting Technician' => 'Sets up and operates event lighting equipment.',
            'Electrician' => 'Maintains electrical systems and event power equipment.',
            'Accountant' => 'Maintains financial records and supports payment processing.',
            'Office Assistant' => 'Supports administrative and office tasks.',
            'Worker' => 'Provides general operational support.',
        ];

        foreach ($designations as $name => $description) {
            Designation::firstOrCreate([
                'name' => $name,
            ], [
                'description' => $description,
            ]);
        }
    }
}
