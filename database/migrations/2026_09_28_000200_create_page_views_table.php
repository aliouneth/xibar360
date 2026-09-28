<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_views', function (Blueprint $table) {
            $table->id();

            // Set when the hit was an article page, so "most read" can be
            // ranked without a join back to articles.
            $table->foreignId('article_id')->nullable()->constrained('articles')->nullOnDelete();

            $table->string('path', 255);

            // sha256(ip + user agent). Raw IPs are never stored: the digest is
            // enough to count unique visitors and cannot be reversed.
            $table->char('visitor_hash', 64);

            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('referer_host', 191)->nullable();
            $table->string('device', 20)->nullable();

            $table->timestamps();

            $table->index('created_at');
            $table->index('visitor_hash');
            $table->index('article_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_views');
    }
};
