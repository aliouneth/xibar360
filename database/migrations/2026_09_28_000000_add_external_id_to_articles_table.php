<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            // Stable identifier from the upstream feed (RSS <guid>), used to
            // make repeated imports idempotent. Nullable + unique so that
            // locally-authored articles (no external id) are unaffected.
            $table->string('external_id')->nullable()->unique()->after('source_url');

            // Human-readable provenance, e.g. "Seneweb".
            $table->string('source_name')->nullable()->after('external_id');
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropUnique(['external_id']);
            $table->dropColumn(['external_id', 'source_name']);
        });
    }
};
