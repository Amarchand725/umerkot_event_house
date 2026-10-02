<?php

namespace Database\Seeders;

use App\Models\Status;
use App\Modules\Service\Models\Service;
use App\Modules\Unit\Models\Unit;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $activeStatusId = Status::where('model', 'Service')->where('name', 'active')->value('id');

        $services = [
            [
                'name' => 'Sound System',
                'description' => 'Professional sound system setup for events.',
                'price' => 15000,
                'unit' => 'Event',
            ],
            [
                'name' => 'LED Screen',
                'description' => 'LED screen display setup for events.',
                'price' => 20000,
                'unit' => 'Day',
            ],
            [
                'name' => 'Stage Decoration',
                'description' => 'Complete stage decoration service.',
                'price' => 25000,
                'unit' => 'Event',
            ],
            [
                'name' => 'Lighting Setup',
                'description' => 'Professional event lighting setup.',
                'price' => 12000,
                'unit' => 'Event',
            ],
            [
                'name' => 'DJ Service',
                'description' => 'DJ service with professional equipment.',
                'price' => 18000,
                'unit' => 'Event',
            ],
        ];

        foreach ($services as $service) {
            $unit = Unit::where('name', $service['unit'])->first();

            Service::updateOrCreate(
                [
                    'name' => $service['name'],
                ],
                [
                    'description' => $service['description'],
                    'price' => $service['price'],
                    'unit_id' => $unit?->id,
                    'status_id' => $activeStatusId,
                ]
            );
        }
    }
}