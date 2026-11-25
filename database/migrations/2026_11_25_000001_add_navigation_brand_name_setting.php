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

        DB::table('page_contents')->insert([
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
