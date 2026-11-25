<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laws', function (Blueprint $table) {
            $table->id();
            $table->foreignId('law_category_id')->constrained()->cascadeOnDelete();
            $table->string('slug')->unique();
            $table->string('law_type', 80);
            $table->string('law_number', 100);
            $table->string('official_title');
            $table->text('short_description')->nullable();
            $table->longText('full_text')->nullable();
            $table->date('date_approved')->nullable();
            $table->string('official_source_url', 2048)->nullable();
            $table->string('legal_status', 120)->nullable();
            $table->string('document_path')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
            $table->index(['is_published', 'law_category_id']);
            $table->index(['law_type', 'law_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laws');
    }
};
