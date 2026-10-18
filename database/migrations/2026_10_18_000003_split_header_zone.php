<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Widen first so existing 'header' rows stay valid, then backfill.
        Schema::table('ads', function (Blueprint $table) {
            $table->enum('zone', ['header', 'header_left', 'header_center', 'header_right', 'sidebar', 'inline', 'footer', 'wrapper'])->change();
        });

        DB::table('ads')->where('zone', 'header')->update(['zone' => 'header_left']);

        Schema::table('ads', function (Blueprint $table) {
            $table->enum('zone', ['header_left', 'header_center', 'header_right', 'sidebar', 'inline', 'footer', 'wrapper'])->change();
        });
    }

    public function down(): void
    {
        Schema::table('ads', function (Blueprint $table) {
            $table->enum('zone', ['header', 'header_left', 'header_center', 'header_right', 'sidebar', 'inline', 'footer', 'wrapper'])->change();
        });

        DB::table('ads')->whereIn('zone', ['header_left', 'header_center', 'header_right'])->update(['zone' => 'header']);

        Schema::table('ads', function (Blueprint $table) {
            $table->enum('zone', ['header', 'sidebar', 'inline', 'footer', 'wrapper'])->change();
        });
    }
};
