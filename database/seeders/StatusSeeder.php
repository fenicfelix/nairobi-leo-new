<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //draft, scheduled, published, trashed
        $statuses = ["Draft", "Scheduled", "Published", "Trashed"];
        foreach ($statuses as $status)
            if (!Status::where("name", "=", $status)->exists()) {
                Status::query()->create(["name" => $status]);
            }
    }
}
