<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('page_contents')) {
            return;
        }

        if (DB::table('page_contents')->where('key', 'nav_brand_name')->exists()) {
            return;
        }

        // TiDB imports can lack AUTO_INCREMENT on existing IDs, so assign this new row explicitly.
        $nextId = ((int) DB::table('page_contents')->max('id')) + 1;

        DB::table('page_contents')->insert([
            'id' => $nextId,
            'key' => 'nav_brand_name',
            'title' => 'Brand name',
            'content' => '',
            'updated_by' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        if (! Schema::hasTable('page_contents')) {
            return;
        }

        DB::table('page_contents')
            ->where('key', 'nav_brand_name')
            ->where('content', '')
            ->whereNull('updated_by')
            ->delete();
    }
};
