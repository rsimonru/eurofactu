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
        Schema::create('sales_budgets_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_budget_id')->constrained('sales_budgets');
            $table->foreignId('product_variant_id')->nullable()->constrained('products_variants');
            $table->text('description')->nullable();
            $table->integer('units');
            $table->string('discount_type', 5)->nullable()->default('P');
            $table->decimal('discountp', 12, 4)->nullable()->default(0);
            $table->decimal('discounti', 12, 3)->nullable()->default(0);
            $table->decimal('base_unit', 12, 3);
            $table->decimal('base_result', 12, 3);
            $table->decimal('base_line', 12, 3);
            $table->foreignId('tax_type_id')->constrained('tax_types');
            $table->decimal('tax_type', 12, 4);
            $table->decimal('tax_unit', 12, 3);
            $table->decimal('tax_line', 12, 3);
            $table->decimal('es_type', 12, 4);
            $table->decimal('es_unit', 12, 3);
            $table->decimal('es_line', 12, 3);
            $table->decimal('total_line', 12, 3);

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
        Schema::dropIfExists('sales_budgets_products');
    }
};
