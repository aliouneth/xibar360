<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Feed <guid> values are unbounded: Google News emits ~500 character base64
     * blobs, and this table's charset caps a unique index at well under that.
     *
     * external_id therefore stores a SHA-256 hex digest of the upstream guid
     * rather than the guid itself. It stays a fixed 64 characters, remains
     * unique-indexable, and is still deterministic so repeat refreshes
     * de-duplicate. The human-readable URL is kept in source_url.
     */
    public function up(): void
    {
        // Re-hash anything already stored, so rows imported before this
        // migration keep matching the digests the importer will now produce.
        // Values that are already a 64-character hex digest are left alone,
        // which also makes this migration safe to re-run.
        DB::table('articles')
            ->whereNotNull('external_id')
            ->orderBy('id')
            ->chunkById(200, function ($articles) {
                foreach ($articles as $article) {
                    $value = (string) $article->external_id;

                    if (strlen($value) === 64 && ctype_xdigit($value)) {
                        continue;
                    }

                    DB::table('articles')
                        ->where('id', $article->id)
                        ->update(['external_id' => hash('sha256', $value)]);
                }
            });

        Schema::table('articles', function (Blueprint $table) {
            $table->string('external_id', 64)->nullable()->change();
            $table->string('source_url', 512)->nullable()->change();
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->unique('external_id');
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropUnique(['external_id']);
            $table->string('external_id', 512)->nullable()->change();
            $table->unique('external_id');
        });
    }
};
