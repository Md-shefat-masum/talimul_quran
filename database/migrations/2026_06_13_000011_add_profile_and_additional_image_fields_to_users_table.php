<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table): void {
            if (! Schema::hasColumn('users', 'profile_image_path')) {
                $table->string('profile_image_path', 500)->nullable()->after('avatar_path');
            }

            if (! Schema::hasColumn('users', 'additional_image_paths')) {
                $table->json('additional_image_paths')->nullable()->after('profile_image_path');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table): void {
            if (Schema::hasColumn('users', 'additional_image_paths')) {
                $table->dropColumn('additional_image_paths');
            }

            if (Schema::hasColumn('users', 'profile_image_path')) {
                $table->dropColumn('profile_image_path');
            }
        });
    }
};
