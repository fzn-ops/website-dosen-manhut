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
        Schema::create('profile_dosen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('division'); // Perencanaan Kehutanan, Pemanfaatan Sumberdaya Hutan, Kebijakan Kehutanan
            $table->text('research')->nullable(); // Ketertarikan penelitian
            $table->json('educations')->nullable(); // Array riwayat pendidikan [{university, major, graduationYear}]
            $table->string('scholar_link')->nullable(); // Link Google Scholar
            $table->string('linkedin_link')->nullable(); // Link LinkedIn
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_dosen');
    }
};
