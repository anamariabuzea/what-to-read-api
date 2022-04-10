<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('authors')->nullable();
            $table->string('uuid')->unique()->nullable();
            $table->string('isbn_10')->nullable();
            $table->string('isbn_13')->nullable();
            $table->string('page_count')->nullable();
            $table->string('category')->nullable();
            $table->text('description')->nullable();
            $table->string('language');
            $table->string('cover_url')->nullable();
            $table->string('small_cover_url')->nullable();
            $table->string('publisher')->nullable();
            $table->string('published_at')->nullable();
            $table->string('preview_link')->nullable();
            $table->string('info_link')->nullable();
            $table->string('average_rating');
            $table->string('ratings_count');
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
        Schema::dropIfExists('books');
    }
};
