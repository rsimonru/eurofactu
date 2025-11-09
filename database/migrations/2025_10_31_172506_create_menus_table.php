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
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->json('description');
            $table->string('route',75);
            $table->foreignId('pmenus_id')->nullable()->constrained('menus');
            $table->smallInteger('order');
            $table->smallInteger('deep');
            $table->string('type',10);
            $table->string('icon',75);
            $table->smallInteger('level');
            $table->string('group', 45)->nullable();
            $table->json('group_description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
