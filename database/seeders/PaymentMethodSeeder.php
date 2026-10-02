<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use App\Models\Status;
use App\Modules\PaymentMethod\Models\PaymentMethod as ModelsPaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        $paymentMethods = [
            'Cash',
            'Bank Transfer',
            'JazzCash',
            'EasyPaisa',
            'Cheque',
            'Online Payment',
        ];

        $statusId = Status::where('model', 'PaymentMethod')->where('name', 'active')->value('id');
        foreach ($paymentMethods as $name) {
            ModelsPaymentMethod::updateOrCreate(
                ['name' => $name],
                [
                    'status_id' => $statusId,
                ]
            );
        }
    }
}