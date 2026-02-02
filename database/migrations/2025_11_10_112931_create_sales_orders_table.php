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
        Schema::create('sales_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies');
            $table->foreignId('thirdparty_id')->constrained('thirdparties');
            $table->foreignId('sales_budget_id')->nullable()->constrained('sales_budgets');
            $table->foreignId('tax_id')->nullable()->constrained('taxes');
            $table->integer('fiscal_year');
            $table->integer('sequential')->nullable();
            $table->string('number', 25);
            $table->foreignId('state_id')->constrained('states');
            $table->string('reference', 100)->nullable();

            $table->date('customer_date')->nullable();
            $table->decimal('tax_retention', 12, 4)->nullable();
            $table->string('vat', 25)->nullable();
            $table->string('legal_form', 255)->nullable();
            $table->string('recipient')->nullable();
            $table->string('address', 100)->nullable();
            $table->string('zip', 15)->nullable();
            $table->string('town', 100)->nullable();
            $table->string('province', 75)->nullable();
            $table->foreignId('country_id')->nullable()->constrained('countries');
            $table->string('phone', 25)->nullable();
            $table->string('email', 255)->nullable();

            $table->text('observations')->nullable();
            $table->text('internal_note')->nullable();

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
        Schema::dropIfExists('sales_orders');
    }
};
