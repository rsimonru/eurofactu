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
        Schema::create('sales_notes_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_note_id')->constrained('sales_notes');
            $table->foreignId('sales_orders_product_id')->nullable()->constrained('sales_orders_products');
            $table->text('description')->nullable();
            $table->integer('units');
            $table->integer('confirmed_units');


            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_notes_products');
    }
};
