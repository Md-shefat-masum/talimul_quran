<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar_url', 1000)->nullable()->after('phone')->comment('Public avatar URL selected from the file manager');
            $table->string('avatar_path', 500)->nullable()->after('avatar_url')->comment('FTP disk path for the selected avatar');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avatar_url', 'avatar_path']);
        });
    }
};
