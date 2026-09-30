<?php

namespace Database\Seeders;

use App\Models\Status;
use App\Modules\EventCategory\Models\EventCategory;
use Illuminate\Database\Seeder;

class EventCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Baras',
            'Anniversary',
            'Satsang',
            'Osar',
            'Wedding',
            'Engagement',
            'Mehndi',
            'Baraat',
            'Walima',
            'Birthday',
            'School / College Event',
            'Religious Event',
            'Other',
        ];

        $statusId = Status::where('model', 'EventCategory')
            ->where('name', 'active')
            ->firstOrFail()
            ->id;

        foreach ($categories as $category) {
            EventCategory::firstOrCreate([
                'name' => $category,
                'status_id' => $statusId,
            ]);
        }
    }
}