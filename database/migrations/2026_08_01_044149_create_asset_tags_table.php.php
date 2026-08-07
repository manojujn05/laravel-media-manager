<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Pehle Tags Table banegi
        Schema::create('asset_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('color', 20)->default('#6366f1');
            $table->timestamps();
        });

  
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_taggables');
        Schema::dropIfExists('asset_tags');
    }
};