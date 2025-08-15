<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->string('file_name');
            $table->string('title')->nullable();
            $table->string('alt_text')->nullable();
            $table->string('caption')->nullable();
            $table->string('description')->nullable();
            $table->timestamp('uploaded_at')->useCurrent();
            $table->foreignId('uploaded_by')->nullable()->references("id")->on("users")->onDelete("set null");
            $table->timestamp('updated_at')->useCurrent();
            $table->foreignId('updated_by')->nullable()->references("id")->on("users")->onDelete("set null");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('images');
    }
}
