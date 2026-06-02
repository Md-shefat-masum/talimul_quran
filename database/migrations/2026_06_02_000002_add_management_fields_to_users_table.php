<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 30)->nullable()->after('email')->comment('Optional user contact number');
            $table->foreignId('user_type_id')
                ->nullable()
                ->after('phone')
                ->constrained('user_types')
                ->nullOnDelete()
                ->comment('Selected user type reference');
            $table->boolean('status')->default(true)->index()->after('user_type_id')->comment('Whether the user account is active');
            $table->softDeletes()->comment('Soft-delete timestamp for safe removal');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['user_type_id']);
            $table->dropColumn(['phone', 'user_type_id', 'status', 'deleted_at']);
        });
    }
};
