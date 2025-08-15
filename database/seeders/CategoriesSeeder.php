<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!Category::where("slug", "=", "blog")->exists()) {
            $category = [
                "name" => "Blog",
                "slug" => "blog",
                "default" => "1",
                "added_by" => "1",
                "updated_by" => "1",
            ];
            Category::query()->create($category);
        }
    }
}
