<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_taggables', function (Blueprint $table) {
            $table->id();

            $table->foreignId('asset_id')
                ->constrained('assets')
                ->cascadeOnDelete();

            // Abhi sirf column create hoga
            $table->unsignedBigInteger('tag_id');

            $table->timestamps();

            $table->unique(['asset_id', 'tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_taggables');
    }
};