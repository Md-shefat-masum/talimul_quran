<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('media_imports')) {
            return;
        }

        Schema::create('media_imports', function (Blueprint $table): void {
            $table->id();
            $table->string('disk', 80)->index();
            $table->string('root', 500)->index();
            $table->boolean('recursive')->default(true);
            $table->boolean('dry_run')->default(false)->index();
            $table->unsignedInteger('limit')->nullable();
            $table->string('status', 40)->default('completed')->index();
            $table->unsignedInteger('scanned')->default(0);
            $table->unsignedInteger('created')->default(0);
            $table->unsignedInteger('updated')->default(0);
            $table->unsignedInteger('skipped')->default(0);
            $table->unsignedInteger('failed')->default(0);
            $table->json('items')->nullable();
            $table->json('errors')->nullable();
            $table->unsignedBigInteger('creator')->nullable()->index();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_imports');
    }
};
