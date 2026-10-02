<?php

namespace Database\Seeders;

use App\Modules\ExpenseCategory\Models\ExpenseCategory;
use App\Models\Status;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExpenseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Transportation',
                'description' => 'Transportation, fuel, delivery, and travel expenses.',
            ],
            [
                'name' => 'Staff',
                'description' => 'Staff wages, overtime, allowances, and other staff-related expenses.',
            ],
            [
                'name' => 'Food & Catering',
                'description' => 'Food, beverages, catering, and related expenses.',
            ],
            [
                'name' => 'Decoration',
                'description' => 'Event decoration, flowers, lighting, and decorative materials.',
            ],
            [
                'name' => 'Equipment',
                'description' => 'Equipment rental, maintenance, and equipment-related expenses.',
            ],
            [
                'name' => 'Venue',
                'description' => 'Venue rental, booking, and venue-related charges.',
            ],
            [
                'name' => 'Marketing',
                'description' => 'Advertising, promotions, printing, and marketing expenses.',
            ],
            [
                'name' => 'Utilities',
                'description' => 'Electricity, water, internet, telephone, and other utilities.',
            ],
            [
                'name' => 'Supplies',
                'description' => 'Office supplies, event supplies, stationery, and consumable materials.',
            ],
            [
                'name' => 'Maintenance & Repairs',
                'description' => 'Maintenance, repairs, and servicing expenses.',
            ],
            [
                'name' => 'Rent',
                'description' => 'Office, shop, warehouse, or other property rental expenses.',
            ],
            [
                'name' => 'Bank & Payment Fees',
                'description' => 'Bank charges, payment gateway fees, and transaction charges.',
            ],
            [
                'name' => 'Taxes & Government Fees',
                'description' => 'Taxes, licenses, permits, and government-related fees.',
            ],
            [
                'name' => 'Miscellaneous',
                'description' => 'Other business or event expenses not covered by another category.',
            ],
        ];

        $statusId = Status::where('model', 'ExpenseCategory')
            ->where('name', 'active')
            ->firstOrFail()
            ->id;

        foreach ($categories as $category) {
            ExpenseCategory::updateOrCreate(
                [
                    'name' => $category['name'],
                    'status_id' => $statusId,
                ],
                $category
            );
        }
    }
}
