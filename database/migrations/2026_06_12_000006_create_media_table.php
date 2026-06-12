<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('media')) {
            return;
        }

        Schema::create('media', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('product_website_id')->nullable()->index();
            $table->string('disk', 20)->default('ftp')->index();
            $table->text('path')->nullable();
            $table->string('filename', 191)->nullable()->index();
            $table->string('extension', 20)->nullable()->index();
            $table->string('mime_type', 100)->nullable()->index();
            $table->unsignedBigInteger('size')->nullable();
            $table->json('folders')->nullable();
            $table->unsignedBigInteger('media_folder_id')->nullable()->index();
            $table->unsignedBigInteger('creator')->nullable()->index();
            $table->string('slug', 50)->nullable()->index();
            $table->tinyInteger('status')->unsigned()->default(1)->index();
            $table->timestamps();

            $table->index(['media_folder_id', 'status']);
            $table->index(['disk', 'media_folder_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
