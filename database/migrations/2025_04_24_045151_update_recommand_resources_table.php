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
        Schema::table('recommand_resources', function (Blueprint $table) {
            $table->dropColumn('skill');

            $table->foreignId('skill_id')->after('id')->constrained('roadmap_skills')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recommand_resources', function (Blueprint $table) {
            $table->string('skill');
            
            $table->dropForeign(['skill_id']);
            $table->dropColumn('skill_id');
        });
    }
};
