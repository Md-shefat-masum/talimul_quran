<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_types', function (Blueprint $table) {
            $table->id()->comment('Primary key of the user type');
            $table->string('name', 100)->comment('Human-readable user type name');
            $table->string('code', 50)->unique()->comment('Stable internal code for the user type');
            $table->boolean('status')->default(true)->index()->comment('Whether this user type can be selected');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_types');
    }
};
