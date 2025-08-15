<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShowHostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('show_hosts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('show_id')->nullable()->references("id")->on("shows")->onDelete("set null");
            $table->foreignId('host_id')->nullable()->references("id")->on("users")->onDelete("set null");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('show_hosts');
    }
}
