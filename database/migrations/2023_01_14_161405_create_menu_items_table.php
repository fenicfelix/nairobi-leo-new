<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenuItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('display_title');
            $table->string('slug')->unique();
            $table->foreignId('menu_id')->nullable()->references("id")->on("menus")->onDelete("set null");
            $table->string('type');
            $table->string('reference_id')->nullable();
            $table->string('url');
            $table->string('order', 2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menu_items');
    }
}
