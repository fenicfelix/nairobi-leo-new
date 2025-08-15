<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('thumbnail', 100)->nullable();
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->string('display_name', 100);
            $table->text('biography')->nullable();
            $table->foreignId('group_id')->nullable()->references("id")->on("user_groups")->onDelete("set null");
            $table->string('phone_number', 20)->nullable();
            $table->string('email')->unique();
            $table->string('username')->unique();
            $table->string('password');
            $table->timestamp('email_verified_at')->nullable();
            $table->string("active", 1)->default("1");
            $table->string('user_url', 100)->nullable();
            $table->string('facebook', 100)->nullable();
            $table->string('instagram', 100)->nullable();
            $table->string('linkedin', 100)->nullable();
            $table->string('twitter', 100)->nullable();
            $table->foreignId('added_by')->nullable()->references("id")->on("users")->onDelete("set null");
            $table->foreignId('updated_by')->nullable()->references("id")->on("users")->onDelete("set null");
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
