<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ecommerce_order_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->nullable()->references("id")->on("ecommerce_orders")->onDelete("set null");
            $table->foreignId('product_id')->nullable()->references("id")->on("ecommerce_products")->onDelete("set null");
            $table->integer('quantity')->daefault(0);
            $table->double('unit_price')->daefault(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_products');
    }
}
