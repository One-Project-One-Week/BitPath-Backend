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
        Schema::table('plans', function (Blueprint $table) {
            // drop foreign key constraint
            $table->dropForeign(['roadmap_id']);
            // drop column
            $table->dropColumn('roadmap_id');

            $table->foreignId('skill_id')->after('id')->constrained('roadmap_skills')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->foreignId('roadmap_id')->constrained('roadmaps')->onDelete('cascade');

            $table->dropForeign(['skill_id']);
            $table->dropColumn('skill_id');
        });
    }
};
