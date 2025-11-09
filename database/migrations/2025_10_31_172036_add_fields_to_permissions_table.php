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
        Schema::table('permissions', function (Blueprint $table) {
            $table->json('description', 45)->after('name');
            $table->string('class', 45)->after('guard_name')->nullable();
            $table->string('model', 255)->after('class')->nullable();
            $table->foreignId('model_id')->after('model')->nullable();
            $table->smallInteger('level')->after('model_id');
            $table->json('data')->after('level')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn('description', 'class', 'model', 'model_id', 'level', 'data');
        });
    }
};
