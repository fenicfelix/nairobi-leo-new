<?php

namespace Database\Seeders;

use App\Models\ecommerce\OrderStatus;
use Illuminate\Database\Seeder;

class EcommerceCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //draft, scheduled, published, trashed
        $statuses = ["Placed", "Payment Made", "Processing", "Delivering", "Completed"];
        foreach ($statuses as $status)
            if (!OrderStatus::where("name", "=", $status)->exists()) {
                OrderStatus::query()->create(["name" => $status]);
            }
    }
}
