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
        Schema::create('sales_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies');
            $table->foreignId('thirdparty_id')->constrained('thirdparties');
            $table->foreignId('sales_order_id')->nullable()->constrained('sales_orders');
            $table->foreignId('sales_invoice_id')->nullable()->constrained('sales_invoices');
            $table->integer('fiscal_year');
            $table->integer('sequential')->nullable();
            $table->string('number', 25);
            $table->date('customer_date')->nullable();
            $table->foreignId('state_id')->constrained('states');
            $table->string('recipient')->nullable();
            $table->string('reference', 100)->nullable();
            $table->string('address', 100);
            $table->string('zip', 15);
            $table->string('town', 100);
            $table->string('province', 75);
            $table->foreignId('country_id')->constrained('countries');
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
        Schema::dropIfExists('sales_notes');
    }
};
