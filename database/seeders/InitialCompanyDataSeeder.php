<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class InitialCompanyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = [
            [
                "id" => 1, "created_at" => now(), "updated_at" => now(), 'code' => 'EMT', 'name' => 'Euromática',
                'legal_form' => 'Euromática Servicios Informáticos, SL', 'vat' => 'B84574227', 'email' => 'sistema@euromatica.es',
                'phone' => null, 'web_url' => 'htts://euromatica.es', 'address' => 'Miraltajo, 32', 'province' => 'Toledo',
                'town' => 'La Puebla de Montalbán', 'zip' => '45516', 'country_id' => 139, 'active' => 1, 'legal_info' => null,
                'additional_info' => null, 'parameters' => null, 'fiscal_year' => today()->format('Y'), 'fiscal_start_month' => 1, 'fiscal_end_month' => 12,
                'logo' => null, 'email_invoice_template' => null, 'email_budget_template' => null, 'verifactu_data' => null, 'certificate_path' => null,
                'certificate_password' => null, 'certificate_expiration' => null,
            ],
        ];
        Company::upsert($companies, ['id'], ['code', 'name', 'legal_form', 'vat', 'email', 'phone', 'web_url', 'address', 'province', 'town', 'zip',
            'country_id', 'active', 'legal_info', 'additional_info', 'parameters', 'fiscal_year', 'fiscal_start_month', 'fiscal_end_month', 'logo',
            'email_invoice_template', 'email_budget_template', 'verifactu_data', 'certificate_path', 'certificate_password', 'certificate_expiration',
            'created_at', 'updated_at']);
    }
}
