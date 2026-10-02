<?php

namespace Database\Seeders;

use App\Modules\InventoryItem\Models\InventoryItem;
use App\Modules\Package\Models\Package;
use App\Models\PackageItem;
use App\Models\Status;
use App\Models\User;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        $author = User::query()->first();

        $activeStatus = Status::query()
            ->where('name', 'Active')
            ->first();

        $items = InventoryItem::query()
            ->get()
            ->keyBy('id');

        if ($items->isEmpty()) {
            return;
        }

        $packages = [
            [
                'name' => 'Basic Event Package',
                'price' => 25000,
                'discount' => 0,
                'description' => 'Basic package for small events.',
                'items' => [
                    ['item_id' => $items->first()->id, 'quantity' => 20],
                ],
            ],
            [
                'name' => 'Standard Event Package',
                'price' => 50000,
                'discount' => 5000,
                'description' => 'Standard package suitable for medium-sized events.',
                'items' => [
                    ['item_id' => $items->first()->id, 'quantity' => 50],
                ],
            ],
            [
                'name' => 'Premium Event Package',
                'price' => 100000,
                'discount' => 10000,
                'description' => 'Premium package for large events.',
                'items' => [
                    ['item_id' => $items->first()->id, 'quantity' => 100],
                ],
            ],
        ];

        foreach ($packages as $packageData) {

            $package = Package::updateOrCreate([
                'author_id' => $author?->id,
                'status_id' => $activeStatus?->id,
                'name' => $packageData['name'],
                'price' => $packageData['price'],
                'discount' => $packageData['discount'],
                'description' => $packageData['description'],
            ]);

            foreach ($packageData['items'] as $itemData) {

                $inventoryItem = $items->get($itemData['item_id']);

                if (!$inventoryItem) {
                    continue;
                }

                PackageItem::updateOrCreate([
                    'package_id' => $package->id,
                    'inventory_item_id' => $inventoryItem->id,
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $inventoryItem->price ?? 0,
                ]);
            }
        }
    }
}
