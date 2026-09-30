<?php

namespace Database\Seeders;

use App\Modules\Unit\Models\Unit;
use Illuminate\Database\Seeder;
use App\Models\Status;
use App\Models\User;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            [
                'name' => 'Piece',
                'short_name' => 'pcs',
            ],
            [
                'name' => 'Set',
                'short_name' => 'set',
            ],
            [
                'name' => 'Box',
                'short_name' => 'box',
            ],
            [
                'name' => 'Dozen',
                'short_name' => 'dz',
            ],
            [
                'name' => 'Meter',
                'short_name' => 'm',
            ],
            [
                'name' => 'Kilogram',
                'short_name' => 'kg',
            ],
            [
                'name' => 'Gram',
                'short_name' => 'g',
            ],
            [
                'name' => 'Liter',
                'short_name' => 'L',
            ],
            [
                'name' => 'Milliliter',
                'short_name' => 'ml',
            ],
            [
                'name' => 'Hour',
                'short_name' => 'hr',
            ],
            [
                'name' => 'Day',
                'short_name' => 'day',
            ],
        ];

        $authorId = User::query()->value('id');
        $statusId = Status::where('model', 'Unit')->value('id');

        foreach ($units as $unit) {
            Unit::firstOrCreate([
                ...$unit,
                'author_id' => $authorId,
                'status_id' => $statusId,
            ]);
        }
    }
}
