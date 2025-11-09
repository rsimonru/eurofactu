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
        Schema::create('sales_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies');
            $table->foreignId('thirdparty_id')->constrained('thirdparties');
            $table->integer('fiscal_year');
            $table->integer('sequential')->nullable();
            $table->string('number', 25);
            $table->date('invoice_date');
            $table->date('sent_date')->nullable();
            $table->date('paid_date')->nullable();
            $table->foreignId('state_id')->constrained('states');

            $table->string('vat', 25);
            $table->string('legal_form', 255)->nullable();
            $table->string('recipient')->nullable();
            $table->string('reference', 100)->nullable();
            $table->string('address', 100);
            $table->string('zip', 15);
            $table->string('town', 100);
            $table->string('province', 75);
            $table->foreignId('country_id')->constrained('countries');
            $table->string('phone', 25)->nullable();
            $table->text('email')->nullable();

            $table->foreignId('bank_account_id')->nullable()->constrained('banks_accounts');
            $table->text('observations')->nullable();
            $table->text('internal_note')->nullable();

            $table->json('verifactu_data')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_invoices');
    }
};
