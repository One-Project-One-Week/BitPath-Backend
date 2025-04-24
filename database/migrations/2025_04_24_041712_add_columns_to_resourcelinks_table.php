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
        Schema::table('resourcelinks', function (Blueprint $table) {
            $table->string('name')->after('recommand_resource_id');
            $table->string('link')->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resourcelinks', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->dropColumn('link');
        });
    }
};
