<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100);
            $table->string('slug', 150)->unique();
            $table->text('body')->nullable();
            $table->string('template', 50)->nullable();
            $table->string('seo_keywords')->nullable();
            $table->string('seo_title')->nullable();
            $table->string('seo_description')->nullable();
            $table->string('seo_status', 3)->default("0");
            $table->foreignId('category_id')->nullable()->references("id")->on("categories")->onDelete("set null");
            $table->foreignId('created_by')->nullable()->references("id")->on("users")->onDelete("set null");
            $table->foreignId('updated_by')->nullable()->references("id")->on("users")->onDelete("set null");
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
        Schema::dropIfExists('pages');
    }
}
