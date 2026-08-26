<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->string('NIP')->nullable()->after('id');
            $table->string('username')->unique()->after('NIP');
            $table->string('role')->default('user')->after('username');
            $table->string('profile_picture')->nullable()->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('NIP');
            $table->dropColumn('username');
            $table->dropColumn('role');
            $table->dropColumn('profile_picture');
        });
    }
};
