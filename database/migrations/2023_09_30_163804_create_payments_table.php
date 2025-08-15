<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->uuid("identifier");
            $table->foreignId('order_id')->nullable()->references("id")->on("ecommerce_orders")->onDelete("set null");
            $table->double('requested_amount')->default(0);
            $table->double('amount_paid')->default(0);
            $table->string("merchant_request_id")->nullable();
            $table->string("checkout_request_id")->nullable();
            $table->string("request_response")->nullable();
            $table->string("customer_request_response")->nullable();
            $table->string("trx_result_response")->nullable();
            $table->string("trx_result_message")->nullable();
            $table->string("trx_receipt")->nullable();
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
        Schema::dropIfExists('payments');
    }
}
