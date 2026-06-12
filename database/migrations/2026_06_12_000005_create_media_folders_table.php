<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('media_folders')) {
            return;
        }

        Schema::create('media_folders', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('product_website_id')->nullable()->index();
            $table->string('name', 150)->nullable();
            $table->string('saved_name_into_storage', 150)->nullable();
            $table->unsignedBigInteger('parent_id')->nullable()->default(0)->index();
            $table->tinyInteger('is_default')->nullable()->default(0);
            $table->unsignedBigInteger('creator')->nullable()->index();
            $table->string('slug', 50)->nullable()->index();
            $table->tinyInteger('status')->unsigned()->default(1)->index();
            $table->timestamps();

            $table->index(['parent_id', 'status']);
        });

        DB::table('media_folders')->insert([
            'name' => 'uploads',
            'saved_name_into_storage' => 'uploads',
            'parent_id' => 0,
            'is_default' => 1,
            'creator' => 1,
            'slug' => 'uploads',
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('media_folders');
    }
};
