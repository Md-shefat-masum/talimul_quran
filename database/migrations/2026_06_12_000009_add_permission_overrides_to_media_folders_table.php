<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('media_folders') || Schema::hasColumn('media_folders', 'permission_overrides')) {
            return;
        }

        Schema::table('media_folders', function (Blueprint $table): void {
            $table->json('permission_overrides')->nullable()->after('is_default');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('media_folders') || ! Schema::hasColumn('media_folders', 'permission_overrides')) {
            return;
        }

        Schema::table('media_folders', function (Blueprint $table): void {
            $table->dropColumn('permission_overrides');
        });
    }
};
