<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ecommerce_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 20)->nullable();
            $table->string('requested_by', 100)->nullable();
            $table->string('phone_number', 20);
            $table->string('email', 100)->nullable();
            $table->double('order_value')->default(0);
            $table->timestamp('requested_on')->useCurrent();
            $table->foreignId('status_id')->nullable()->references("id")->on("ecommerce_order_statuses")->onDelete("set null");
            $table->timestamp('completed_on')->useCurrent();
            $table->foreignId('completed_by')->nullable()->references("id")->on("users")->onDelete("set null");
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
        Schema::dropIfExists('orders');
    }
}
