<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('title')->nullable();
            $table->string('slug')->unique()->nullable();
            $table->string('disk')->default('public');
            $table->string('path')->nullable();
            $table->string('mime_type')->nullable(); 
            $table->string('extension', 20)->nullable(); 
            $table->unsignedBigInteger('size')->default(0)->nullable(); 
            $table->string('hash', 64)->nullable()->index();
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->string('alt')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('folder_id')
                ->nullable()
                ->constrained('asset_folders')
                ->nullOnDelete();
            $table->boolean('is_favorite')->default(false);
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['folder_id', 'is_favorite']);
            $table->index(['folder_id', 'deleted_at']);
            $table->index(['folder_id', 'mime_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};