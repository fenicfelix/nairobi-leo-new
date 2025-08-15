<?php

namespace Database\Seeders;

use App\Models\UserGroup;
use Illuminate\Database\Seeder;

class UserGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user_groups = [
            ["name" => "Admin", "description" => "Has all user priviledges"],
            ["name" => "Editor", "description" => "Can create, edit, publish and trash posts"],
            ["name" => "Author", "description" => "Can create posts but can not publish"],
            ["name" => "Contributor", "description" => "Can be used as a byline"],
            ["name" => "Subscriber", "description" => "Is a subscriber to other services"]
        ];

        if ($user_groups) {
            foreach ($user_groups as $user_group) {
                if (!UserGroup::where("name", "=", $user_group["name"])->exists()) {
                    $data = [
                        "name" => $user_group["name"],
                        "description" => $user_group["description"]
                    ];
                    UserGroup::query()->create($data);
                }
            }
        }
    }
}
