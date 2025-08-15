<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!User::where("username", "=", "akika.digital")->exists()) {
            $user = [
                "first_name" => "Akika",
                "last_name" => "Digital",
                "display_name" => "akika.digital",
                "username" => "akika.digital",
                "email" => "akika.digital@gmail.com",
                "password" => Hash::make('password'),
                "group_id" => "1",
            ];
            User::query()->create($user);
        }
    }
}
