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
        Schema::table('roadmaps', function (Blueprint $table) {
            $table->foreignId('created_user_id')->after('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->enum('visibility', ['public', 'private'])->default('private')->after('created_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roadmaps', function (Blueprint $table) {
            $table->dropForeign(['created_user_id']);
            $table->dropColumn('created_user_id');
            $table->dropColumn('visibility');   
        });
    }
};
