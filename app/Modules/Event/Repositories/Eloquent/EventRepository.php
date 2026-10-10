<?php

namespace App\Modules\Event\Repositories\Eloquent;

use App\Repositories\Eloquent\BaseRepository;
use App\Modules\Event\Repositories\Contracts\EventContract;
use App\Modules\Event\Models\Event;
use Illuminate\Support\Facades\DB;

class EventRepository extends BaseRepository implements EventContract
{
    public function __construct(Event $model)
    {
        parent::__construct($model);
    }

    public function storeModel(array $data): Event
    {
        return DB::transaction(function () use ($data) {

            $items = $data['items'] ?? [];
            $services = $data['services'] ?? [];

            unset($data['items'], $data['services']);

            $event = $this->model->create($data);

            // Save inventory items
            foreach ($items as $item) {
                $event->items()->create([
                    'inventory_category_id' => $item['inventory_category_id'],
                    'inventory_item_id' => $item['inventory_item_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['quantity'] * $item['unit_price'],
                ]);
            }

            // Save services
            foreach ($services as $service) {
                $event->services()->create([
                    'service_id' => $service['service_id'],
                    'quantity' => $service['quantity'],
                    'unit_price' => $service['unit_price'],
                    'subtotal' => $service['quantity'] * $service['unit_price'],
                ]);
            }

            return $event;
        });
    }
}
