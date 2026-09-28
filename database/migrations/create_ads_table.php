<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ads', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->text('html_snippet')->nullable();
            $table->string('target_url')->nullable();
            $table->enum('zone', ['header', 'sidebar', 'inline', 'footer']);
            $table->boolean('is_active')->default(1);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        Schema::table('ads', function (Blueprint $table) {
            $table->index('zone');
            $table->index('is_active');
            $table->index('order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ads');
    }
};
