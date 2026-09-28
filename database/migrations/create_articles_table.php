<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->text('title_fr');
            $table->text('title_en');
            $table->text('summary_fr');
            $table->text('summary_en');
            $table->longText('content_fr');
            $table->longText('content_en');
            $table->unsignedBigInteger('category_id');
            $table->string('source_url')->nullable();
            $table->string('thumbnail')->nullable();
            $table->dateTime('publication_date');
            $table->enum('language', ['fr', 'en']);
            $table->enum('source_type', ['imported', 'local']);
            $table->boolean('is_published')->default(1);
            $table->boolean('is_featured')->default(0);
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->index('category_id');
            $table->index('user_id');
            $table->index('is_published');
            $table->index('language');
            $table->index('publication_date');
            $table->index('is_featured');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
