<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('media_in_uses')) {
            return;
        }

        Schema::create('media_in_uses', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('product_website_id')->nullable()->index();
            $table->unsignedBigInteger('media_id')->nullable()->index();
            $table->string('model', 100)->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->string('col_name', 100)->nullable()->index();
            $table->unsignedBigInteger('creator')->nullable()->index();
            $table->string('slug', 50)->nullable()->index();
            $table->tinyInteger('status')->unsigned()->default(1)->index();
            $table->timestamps();

            $table->index(['model', 'model_id']);
            $table->index(['media_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_in_uses');
    }
};
