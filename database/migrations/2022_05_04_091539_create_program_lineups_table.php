<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProgramLineupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('program_lineups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('show_id')->nullable()->references("id")->on("shows")->onDelete("set null");
            $table->string('day', 10);
            $table->time('start_time');
            $table->time('end_time');
            $table->foreignId('created_by')->nullable()->references("id")->on("users")->onDelete("set null");
            $table->foreignId('last_updated_by')->nullable()->references("id")->on("users")->onDelete("set null");
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
        Schema::dropIfExists('program_lineups');
    }
}
