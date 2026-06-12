<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users') || Schema::hasColumn('users', 'document_urls')) {
            return;
        }

        Schema::table('users', function (Blueprint $table): void {
            $table->json('document_urls')->nullable()->after('avatar_path');
            $table->json('document_paths')->nullable()->after('document_urls');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table): void {
            if (Schema::hasColumn('users', 'document_urls')) {
                $table->dropColumn('document_urls');
            }

            if (Schema::hasColumn('users', 'document_paths')) {
                $table->dropColumn('document_paths');
            }
        });
    }
};
