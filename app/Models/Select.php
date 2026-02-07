<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use App\Models\Role;
use App\Models\Color;
use App\Models\Province;

class Select extends Model
{

    public static function get_day($d) {
        $aDays = [
            1 => ['es' => 'Lunes', 'en' => 'Monday'],
            2 => ['es' => 'Martes', 'en' => 'Tuesday'],
            3 => ['es' => 'Miércoles', 'en' => 'Wednesday'],
            4 => ['es' => 'Jueves', 'en' => 'Thursday'],
            5 => ['es' => 'Viernes', 'en' => 'Friday'],
            6 => ['es' => 'Sábado', 'en' => 'Saturday'],
            7 => ['es' => 'Domingo', 'en' => 'Sunday'],
        ];
        return ['value' => $d, "option" => $aDays[$d][app()->getLocale()]];
    }

    /**
     * Get select.
     *
     * @param string $vcSelect
     * @param string $parameter1
     * @param string $parameter2
     * @param string $parameter3
     * @param string $parameter4
     * @return mixed Colletion
     *
     */
    public static function emtGet(
        string $vcSelect,
        $parameter1 = '',
        $parameter2 = '',
        $parameter3 = '',
        $parameter4 = '',
        $search = '',
        $selected = null
    ) {

        $oSelect = null;

        switch ($vcSelect) {
            case "banks_accounts":
                $oSelect = BanksAccount::select('banks_accounts.id as value')
                    ->selectRaw('concat(banks_accounts.bank, " - ", banks_accounts.name) as `option`')
                    ->when(!empty($parameter1), function ($query) use ($parameter1) {
                        $query->where('banks_accounts.company_id', $parameter1);
                    })
                    ->where('banks_accounts.active', 1)
                    ->orderBy('banks_accounts.default', 'desc')
                    ->orderBy('banks_accounts.name', 'asc')
                    ->get()->toArray();
                break;
            case "companies":
                $oSelect = Company::select('companies.id as value', 'companies.name as option')
                    ->where('companies.active', 1)
                    ->orderBy('companies.name', 'asc')
                    ->get()->toArray();
                break;
            case "countries":
                $oSelect = Country::select('countries.id as value', 'countries.name->'.app()->getLocale().' as option')
                    ->orderByRaw('countries.name->"$.'.app()->getLocale().'" asc')
                    ->get()->toArray();
                break;
            case "dates_expenses":
                $oSelect = [
                    ["value" => 'date', "option" => __('generic.invoice_date')],
                    ["value" => 'paid_date', "option" => __('generic.payment_date')],
                ];
                break;
            case "dates_invoices":
                $oSelect = [
                    ["value" => 'invoice_date', "option" => __('generic.invoice_date')],
                    ["value" => 'paid_date', "option" => __('generic.payment_date')],
                ];
                break;
            case "dates_projects":
                $oSelect = [
                    ["value" => 'created_at', "option" => __('generic.create_date')],
                ];
                break;
            case "fiscal_years":
                $year = date("Y")+1;
                for ($i = $year; $i >= 2024; $i--) {
                    $oSelect[] = ["value" => $i, "option" => $i];
                }
                break;
            case "levels":
                $oSelect = Level::select('id as value', 'name->'.app()->getLocale().' as option')
                    ->when(!empty($parameter1), function ($query) use ($parameter1) {
                        $query->where('level', '<=', $parameter1);
                    })
                    ->orderBy('level', 'asc')
                    ->get()->toArray();
                break;
            case "months":
                $oSelect = [
                    ["value" => 1, "option" => "Enero"], ["value" => 2, "option" => "Febrero"], ["value" => 3, "option" => "Marzo"],
                    ["value" => 4, "option" => "Abril"], ["value" => 5, "option" => "Mayo"], ["value" => 6, "option" => "Junio"],
                    ["value" => 7, "option" => "Julio"], ["value" => 8, "option" => "Agosto"], ["value" => 9, "option" => "Septiembre"],
                    ["value" => 10, "option" => "Octubre"], ["value" => 11, "option" => "Noviembre"], ["value" => 12, "option" => "Diciembre"]
                ];
                break;
            case "products":
                $oSelect = Product::select('products.id as value', 'products.description as option')
                    ->when(!empty($search), function ($query) use ($search) {
                        $query->where('products.description', 'like', '%'.$search.'%');
                    })
                    ->when($selected, function ($query) use ($selected) {
                        $query->where('products.id', '>', 0)->orWhereInto('products.id', $selected);
                    })
                    ->orderBy('products.description', 'asc')->get()
                    ->toArray();
                break;
            case "products_variants":
                $oSelect = ProductsVariant::select('products_variants.id as value', 'products_variants.description as option')
                    ->when(!empty($parameter1), function ($query) use ($parameter1) {
                        $query->where('products_variants.product_id', $parameter1);
                    })
                    ->when(!empty($search), function ($query) use ($search) {
                        $query->where('products_variants.description', 'like', '%'.$search.'%');
                    })
                    ->when($selected, function ($query) use ($selected) {
                        $query->where('products_variants.id', '>', 0)->orWhereInto('products_variants.id', $selected);
                    })
                    ->orderBy('products_variants.description', 'asc')->get()
                    ->toArray();
                break;
            case "provinces":
                $oSelect = Province::select('id as value', 'province as option')
                    ->orderBy('id', 'asc')
                    ->get()->toArray();
                break;
            case "states":
                $oSelect = State::select('states.id as value', 'states.description->'.app()->getLocale().' as option')
                    ->when(!empty($parameter1), function ($query) use ($parameter1) {
                        $query->join('states_models as sm', function ($join) use ($parameter1) {
                            $join->on( 'sm.states_id', 'states.id')
                                ->where('sm.model', $parameter1);
                        })
                        ->orderBy('sm.order', 'asc');
                    })
                    ->when(empty($parameter1), function ($query) {
                        $query->orderBy('states.description->'.app()->getLocale(), 'asc');
                    })
                    ->get()->toArray();
                break;
            case "tax_types":
                $oSelect = TaxType::select('value as value', 'type as option')
                    ->orderBy('order', 'asc')
                    ->get()->toArray();
                break;
            case "tax_types_ids":
                $oSelect = TaxType::select('id as value', 'type as option')
                    ->orderBy('order', 'asc')
                    ->get()->toArray();
                break;
            case "thirdparties":
                $oSelect = Thirdparty::select('thirdparties.id as value', 'thirdparties.legal_form as option')
                    ->when(!empty($parameter1), function ($query) {
                        $query->where('thirdparties.is_customer', true);
                    })
                    ->when(!empty($parameter2), function ($query) {
                        $query->where('thirdparties.is_supplier', true);
                    })
                    ->when(!empty($search), function ($query) use ($search) {
                        $query->where('thirdparties.legal_form', 'like', '%'.$search.'%');
                    })
                    ->when($selected, function ($query) use ($selected) {
                        $query->where('thirdparties.id', '>', 0)->orWhereInto('thirdparties.id', $selected);
                    })
                    ->orderBy('thirdparties.legal_form', 'asc')->get()
                    ->toArray();
                break;
            case "users":
                $oSelect = User::select('users.id as value', 'users.name as option')
                    ->when(!empty($parameter1), function ($query) use ($parameter1) {
                        $query->whereNotIn('users.id', $parameter1);
                    })
                    ->where('users.active', 1)
                    ->orderBy('users.name', 'asc')
                    ->get()->toArray();
                break;
            case "users_active":
                $oSelect = [
                    ["value" => 0, "option" => "No activos"],
                    ["value" => 1, "option" => "Activos"]
                ];
                break;
            case "weekdays":
                $a = array(1, 2, 3, 4, 5, 6, 7);
                $oSelect = array_map("static::get_day", $a);
                break;
            case "years":
                $iYear = date("Y");
                $iPre = $iYear - (($parameter1 != '') ? $parameter1 : 5);
                $iPos = $iYear + (($parameter2 != '') ? $parameter2 : 1);

                for ($i = $iPre; $i <= $iPos; $i++) {
                    $oSelect[] = ["value" => $i, "option" => $i];
                }
                break;
            case "yes_no":
                $oSelect = [
                    ["value" => 0, "option" => __('generic.false')],
                    ["value" => 1, "option" => __('generic.true')]
                ];
                break;
        }

        return $oSelect;
    }
}
