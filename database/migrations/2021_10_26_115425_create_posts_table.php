<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('excerpt')->nullable();
            $table->text('in_summary')->nullable();
            $table->longText('body')->nullable();
            $table->foreignId('category_id')->nullable()->references("id")->on("categories")->onDelete("set null");
            $table->string('seo_keywords')->nullable();
            $table->string('seo_title')->nullable();
            $table->string('seo_description')->nullable();
            $table->string('seo_status', 3)->default("0");
            $table->string("is_breaking", 1)->default("0");
            $table->string("is_featured", 1)->default("0");
            $table->string("is_sponsored", 1)->default("0");
            $table->foreignId('featured_image')->nullable()->references("id")->on("images")->onDelete("set null");
            $table->string('post_label', 50)->nullable();
            $table->string("homepage_ordering", 1)->default(0);
            $table->integer("total_views", false, true)->default(0);
            $table->integer("total_shares", false, true)->default(0);
            $table->integer("total_likes", false, true)->default(0);
            $table->integer("total_dislikes", false, true)->default(0);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('last_updated_at')->useCurrent();
            $table->timestamp('published_at')->nullable();
            $table->foreignId('created_by')->nullable()->references("id")->on("users")->onDelete("set null");
            $table->foreignId('published_by')->nullable()->references("id")->on("users")->onDelete("set null");
            $table->foreignId('current_editor')->nullable()->references("id")->on("users")->onDelete("set null");
            $table->foreignId('status_id')->nullable()->references("id")->on("statuses")->onDelete("set null");
            $table->string("display_ads", 1)->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
}
