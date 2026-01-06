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
            $table->foreignId('sales_orders_id')->constrained('sales_orders');
            $table->foreignId('product_variant_id')->nullable()->constrained('products_variants');
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
