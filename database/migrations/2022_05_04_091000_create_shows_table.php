<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shows', function (Blueprint $table) {
            $table->id();
            $table->string('title')->unique();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('synopsis')->nullable();
            $table->string('seo_keywords')->nullable();
            $table->string('seo_title')->nullable();
            $table->string('hosts')->nullable();
            $table->string('seo_description')->nullable();
            $table->string('seo_status', 3)->default("0");
            $table->string('active', 1)->default("1");
            $table->foreignId('banner_img')->nullable()->references("id")->on("images")->onDelete("set null");
            $table->foreignId('mobile_img')->nullable()->references("id")->on("images")->onDelete("set null");
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
        Schema::dropIfExists('shows');
    }
}
