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
        Schema::create('thirdparties', function (Blueprint $table) {
            $table->id();
            $table->string('legal_form', 75);
            $table->string('vat', 45);
            $table->tinyInteger('foreign')->default(0);
            $table->string('contact', 75)->nullable();
            $table->string('address', 200)->nullable();
            $table->string('town', 75)->nullable();
            $table->string('province', 45)->nullable();
            $table->string('zip', 15)->nullable();
            $table->foreignId('country_id')->constrained('countries');
            $table->string('phone', 25)->nullable();
            $table->string('mobile', 25)->nullable();
            $table->string('email', 200)->nullable();
            $table->text('invoice_email')->nullable();
            $table->text('observations')->nullable();
            $table->tinyInteger('is_customer')->nullable();
            $table->tinyInteger('is_supplier')->nullable();
            $table->decimal('tax_retention', 12, 4)->nullable();
            $table->tinyInteger('equivalence_surcharge')->default(0);
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
        Schema::dropIfExists('thirdparties');
    }
};
