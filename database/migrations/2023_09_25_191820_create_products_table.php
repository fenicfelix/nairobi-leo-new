<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ecommerce_products', function (Blueprint $table) {
            $table->id();
            $table->string('sku', 10)->nullable();
            $table->double("unit_price")->nullable()->default(0);
            $table->double("discounted_price")->nullable()->default(0);
            $table->string("unit_measurement", 10)->nullable();
            $table->integer("quantity", false, true)->nullable()->default(0);
            $table->foreignId('post_id')->nullable()->references("id")->on("posts")->onDelete("set null");
            $table->json('other_images')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
