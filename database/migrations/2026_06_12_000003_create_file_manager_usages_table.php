<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('file_manager_usages', function (Blueprint $table) {
            $table->id()->comment('Primary key of the tracked file usage');
            $table->string('usage_hash', 64)->unique()->comment('Stable unique hash for this usage context');
            $table->string('disk', 50)->default('ftp')->index()->comment('Filesystem disk where the file lives');
            $table->string('path', 500)->index()->comment('Normalized file path on the disk');
            $table->string('url', 1000)->nullable()->comment('Public URL saved by the consuming form');
            $table->string('module', 100)->index()->comment('Module or feature using the file');
            $table->string('owner_type', 160)->nullable()->comment('Model or entity type using the file');
            $table->string('owner_id', 80)->nullable()->comment('Model or entity id using the file');
            $table->string('field_name', 120)->index()->comment('Field name where the file is stored');
            $table->string('collection', 120)->nullable()->comment('Optional group or collection name');
            $table->string('label', 160)->nullable()->comment('Human-readable usage label');
            $table->json('metadata')->nullable()->comment('Additional usage context');
            $table->timestamps();

            $table->index(['disk', 'path']);
            $table->index(['module', 'owner_type', 'owner_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('file_manager_usages');
    }
};
