<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lawyer_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('professional_title')->nullable();
            $table->string('short_bio', 500)->nullable();
            $table->longText('biography')->nullable();
            $table->string('photo_path')->nullable();
            $table->string('phone')->nullable();
            $table->string('public_email')->nullable();
            $table->string('office_address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lawyer_profiles');
    }
};
