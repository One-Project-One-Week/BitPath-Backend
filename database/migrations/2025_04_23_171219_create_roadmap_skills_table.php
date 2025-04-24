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
        Schema::create('roadmap_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('roadmap_id')->constrained('roadmaps')->onDelete('cascade');
            $table->string('skill');
            $table->string('why');
            $table->string('duration');
            $table->string('level');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roadmap_skills');
    }
};
