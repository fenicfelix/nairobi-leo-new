<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('source')->default('Youtube');
            $table->string('video_id')->unique();
            $table->text('description')->nullable();
            $table->string('thumbnail_sm', 100)->nullable();
            $table->string('thumbnail_md', 100);
            $table->string('thumbnail_lg', 100);
            $table->string('live', 1)->default("0");
            $table->string('published', 1)->default("1");
            $table->timestamp('published_at');
            $table->foreignId('updated_by')->nullable()->references("id")->on("users")->onDelete("set null");
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('videos');
    }
}
