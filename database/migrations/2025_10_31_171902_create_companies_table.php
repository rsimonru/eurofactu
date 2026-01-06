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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('code', 5);
            $table->string('name', 100);
            $table->string('legal_form', 150);
            $table->string('vat', 25)->nullable();
            $table->string('email', 75)->nullable();
            $table->string('phone', 25)->nullable();
            $table->string('web_url', 150)->nullable();
            $table->string('address', 75)->nullable();
            $table->string('province', 45)->nullable();
            $table->string('town', 75)->nullable();
            $table->string('zip', 15)->nullable();
            $table->foreignId('country_id')->constrained('countries')->nullable();
            $table->tinyInteger('active')->default(0);
            $table->text('legal_info')->nullable();
            $table->json('additional_info')->nullable();
            $table->json('parameters')->nullable();
            $table->foreignId('tax_id')->constrained('taxes')->nullable();
            $table->integer('fiscal_year');
            $table->smallInteger('fiscal_start_month')->default(1);
            $table->smallInteger('fiscal_end_month')->default(12);
            $table->string('logo', 250)->nullable();
            $table->text('email_invoice_template')->nullable();
            $table->text('email_budget_template')->nullable();
            $table->json('verifactu_data')->nullable();
            $table->string('certificate_path', 255)->nullable();
            $table->string('certificate_password', 100)->nullable();
            $table->date('certificate_expiration')->nullable();

            $table->timestamps();
            $table->softDeletes()->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
