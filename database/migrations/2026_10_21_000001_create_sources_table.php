<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sources', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('url')->unique();
            $table->string('language', 5)->default('fr');
            $table->boolean('enabled')->default(true);
            $table->string('user_agent', 500)->nullable();
            $table->timestamp('last_checked_at')->nullable();
            $table->string('last_status', 20)->nullable();
            $table->text('last_error')->nullable();
            $table->timestamps();

            $table->index('enabled');
        });

        // Seed the table with the feeds that were previously managed in
        // config/news_sources.php so the first admin visit is not empty.
        $now = now();
        $rows = [];

        foreach (config('news_sources.sources', []) as $source) {
            if (empty($source['url'])) {
                continue;
            }

            $rows[] = [
                'name' => $source['name'] ?? $source['url'],
                'url' => $source['url'],
                'language' => $source['language'] ?? 'fr',
                'enabled' => (bool) ($source['enabled'] ?? true),
                'user_agent' => $source['user_agent'] ?? null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if ($rows !== []) {
            DB::table('sources')->insert($rows);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('sources');
    }
};
