<?php

namespace Database\Seeders;

use App\Modules\Customer\Models\Customer;
use App\Models\Status;
use App\Models\User;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $authorId = User::query()->value('id');
        $statusId = Status::where('model', 'Customer')->value('id');

        $customers = [
            [
                'name' => 'Ahmed Ali',
                'cnic_no' => '41301-1234567-1',
                'phone' => '03001234567',
                'alter_phone' => '03111234567',
                'email' => 'ahmed@example.com',
                'address' => 'Umerkot, Sindh',
                'note' => 'Regular customer',
            ],
            [
                'name' => 'Muhammad Asif',
                'cnic_no' => '41301-2345678-3',
                'phone' => '03211234567',
                'alter_phone' => null,
                'email' => 'asif@example.com',
                'address' => 'Mirpurkhas, Sindh',
                'note' => 'Wedding event customer',
            ],
            [
                'name' => 'Sajid Hussain',
                'cnic_no' => '41301-3456789-5',
                'phone' => '03331234567',
                'alter_phone' => '03451234567',
                'email' => null,
                'address' => 'Umerkot, Sindh',
                'note' => null,
            ],
        ];

        foreach ($customers as $customer) {
            Customer::firstOrCreate([
                ...$customer,
                'author_id' => $authorId,
                'status_id' => $statusId,
            ]);
        }
    }
}
