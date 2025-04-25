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

            $table->foreignId('request_id')->after('id')->constrained('plan_requests')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->foreignId('roadmap_id')->constrained('roadmaps')->onDelete('cascade');

            $table->dropForeign(['request_id']);
            $table->dropColumn('request_id');
        });
    }
};
