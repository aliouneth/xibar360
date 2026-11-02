<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The admin form treats summaries as optional, but the original schema
     * declared them NOT NULL. An empty textarea is turned into null by
     * ConvertEmptyStringsToNull, so saving an article without a summary threw
     * "Column 'summary_fr' cannot be null" on MySQL strict mode.
     */
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->text('summary_fr')->nullable()->change();
            $table->text('summary_en')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->text('summary_fr')->nullable(false)->change();
            $table->text('summary_en')->nullable(false)->change();
        });
    }
};
