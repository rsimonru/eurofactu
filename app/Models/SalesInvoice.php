<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\WithExtensions;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesInvoice extends Model
{
    use HasFactory;
    use WithExtensions;
    use SoftDeletes;

    protected $casts = [
        'invoice_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'paid_date' => 'date',
        'sent_date' => 'date',
        'verifactu_data' => 'array',
    ];

    /**
     * Get invoices
     *
     * @param int $iModels_id
     * @param int $records_in_page
     * @param array $aSort (attribute => 'asc'/'desc')
     * @param array $aFilters
     * @return mixed Collection
     *
     */
    public static function emtGet(
        int $model_id=0,
        int $records_in_page = 0,
        array $sort = [],
        array $filters = [],
        array $with = [],
        string $paginatorName = 'page'
    ) {

        $oQuery = static::select('sales_invoices.*')
        ->selectRaw('sum(sip.base_line) as base_line, sum(sip.tax_line) as tax_line, sum(sip.total_line) as total_line')
        ->leftJoin('sales_invoices_products as sip', 'sip.sinvoice_id', 'sales_invoices.id')
        ->when($model_id>0, function($query) use ($model_id) {
            return $query->where('sales_invoices.id', $model_id);
        });

        $oQuery = static::emtApplyFilters($oQuery, $filters);

        $oQuery->groupBy('sales_invoices.id');

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }
        // $oQuery->dd();
        return static::getModelData($oQuery, $model_id, $records_in_page, $with, $paginatorName);
    }
    public static function emtApplyFilters(
        $oQuery,
        ?array $filters = []
    ) {

        $oQuery->when(isset($filters['sinvoice_ids']) && !empty($filters['sinvoice_ids']), function($query) use ($filters) {
            return $query->whereIn('sales_invoices.id', $filters['sinvoice_ids']);
        })
        ->when(isset($filters['state_id']) && !empty($filters['state_id']), function($query) use ($filters) {
            return $query->whereInto('sales_invoices.state_id', $filters['state_id']);
        })
        ->when(isset($filters['company_id']) && !empty($filters['company_id']), function($query) use ($filters) {
            return $query->where('sales_invoices.company_id', $filters['company_id']);
        })
        ->when(isset($filters['thirdparty_id']) && !empty($filters['thirdparty_id']), function($query) use ($filters) {
            return $query->whereInto('sales_invoices.thirdparty_id', $filters['thirdparty_id']);
        })
        ->when(isset($filters['legal_form']) && !empty($filters['legal_form']), function($query) use ($filters) {
            return $query->where('sales_invoices.legal_form', 'like', '%'.$filters['legal_form'].'%');
        })
        ->when(isset($filters['email']) && !empty($filters['email']), function($query) use ($filters) {
            return $query->where('sales_invoices.email', 'email', '%'.$filters['email'].'%');
        })
        ->when(isset($filters['with_out_invoice_email']) && !empty($filters['with_out_invoice_email']), function($query) use ($filters) {
            return $query->where(function ($query) {
                $query->whereNull('sales_invoices.email')
                    ->orWhere('sales_invoices.email', '');
            });
        })
        ->when(isset($filters['with_invoice_email']) && !empty($filters['with_invoice_email']), function($query) use ($filters) {
            return $query->whereNotNull('sales_invoices.email')->where('sales_invoices.email', '<>', '');
        })
        ->when(isset($filters['mark_sent']) && !empty($filters['mark_sent']), function($query) use ($filters) {
            return $query->whereNotNull('sales_invoices.sent_date');
        })
        ->when(isset($filters['vat']) && !empty($filters['vat']), function($query) use ($filters) {
            return $query->where('sales_invoices.vat', 'vat', '%'.$filters['email'].'%');
        })
        ->when(isset($filters['fiscal_year']) && !empty($filters['fiscal_year']), function($query) use ($filters) {
            return $query->where('sales_invoices.fiscal_year', $filters['fiscal_year']);
        })
        ->when(isset($filters['assigned_to']) && !empty($filters['assigned_to']), function($query) use ($filters) {
            return $query->whereIn('sales_invoices.assigned_to',$filters['assigned_to']);
        })
        ->when(isset($filters['verifactu_pending']) && !empty($filters['verifactu_pending']), function($query) use ($filters) {
            return $query->whereNull('sales_invoices.verifactu_data');
        })
        ->when(isset($filters['date']) && !empty($filters['date']), function ($query) use ($filters) {
            return $query->whereBetween('sales_invoices.' . $filters['date'][0], [$filters['date'][1], $filters['date'][2]]);
        })
        ->when(isset($filters['search']) && !empty($filters['search']), function($query) use ($filters) {
            return $query->where(function ($query) use ($filters){
                $query->where('sales_invoices.legal_form', 'like', '%'.$filters['search'].'%')
                ->orWhere('sales_invoices.number', 'like', '%'.$filters['search'].'%')
                ->orWhere('sales_invoices.vat', 'like', '%'.$filters['search'].'%');
            });
        });

        return $oQuery;
    }
    /**
     * Get sales summary.
     *
     * @param array $aFilters
     * @return mixed Colletion
     *
     */
    public static function emtGetSummary(
        ?array $filters = []
    ) {
        $oQuery = static::selectRaw('
            count(distinct sales_invoices.id) as documents,
            sum(sip.base_line) as base_line,
            sum(sip.tax_line) as tax_line,
            sum(sip.total_line) as total_line,
            sum(if(sales_invoices.paid_date is not null, sip.total_line,0)) as total_paid
        ')
        ->leftJoin('sales_invoices_products as sip', 'sip.sinvoice_id', 'sales_invoices.id');
        $oQuery = static::emtApplyFilters($oQuery, $filters);

        return $oQuery->get()->first();
    }

    public static function emtGetSummaryByCustomers(
        int $records_in_page = 0,
        array $sort = [],
        array $filters = []
    ) {
        $oQuery = static::selectRaw('
            t.id,
            t.legal_form,
            sum(sip.base_line) as base_line,
            sum(sip.tax_line) as tax_line,
            sum(sip.total_line) as total_line,
            sum(if(sales_invoices.paid_date is not null, sip.total_line,0)) as total_paid
        ')
        ->join('thirdparties as t', 't.id', 'sales_invoices.thirdparty_id')
        ->leftJoin('sales_invoices_products as sip', 'sip.sinvoice_id', 'sales_invoices.id');
        $oQuery = static::emtApplyFilters($oQuery, $filters);

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }

        $oQuery->groupBy('sales_invoices.thirdparty_id');

        return static::getModelData($oQuery, 0, $records_in_page);
    }
    public static function emtGetSummaryByCompanies(
        int $records_in_page = 0,
        array $sort = [],
        array $filters = []
    ) {
        $filters['company_id'] = null;
        $oQuery = static::selectRaw('
            c.id,
            c.legal_form,
            sum(sip.base_line) as base_line,
            sum(sip.tax_line) as tax_line,
            sum(sip.total_line) as total_line,
            sum(if(sales_invoices.paid_date is not null, sip.total_line,0)) as total_paid,
            if(o.amount is not null, o.amount, 0) as total_objective
        ')
        ->join('companies as c', 'c.id', 'sales_invoices.company_id')
        ->leftJoin('objectives as o', function($join) {
            $join->on('o.company_id', 'sales_invoices.company_id')
                ->whereColumn('o.fiscal_year', 'sales_invoices.fiscal_year');
        })
        ->leftJoin('sales_invoices_products as sip', 'sip.sinvoice_id', 'sales_invoices.id');
        $oQuery = static::emtApplyFilters($oQuery, $filters);

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }

        $oQuery->groupBy('sales_invoices.company_id');

        return static::getModelData($oQuery, 0, $records_in_page);
    }
    public static function emtGetSummaryByYearsCompanies()
    {
        $oQuery = static::selectRaw('
            c.id as company_id,
            c.legal_form,
            sales_invoices.fiscal_year,
            sum(sip.base_line) as base_line,
            sum(sip.tax_line) as tax_line,
            sum(sip.total_line) as total_line,
            sum(if(sales_invoices.paid_date is not null, sip.total_line,0)) as total_paid,
            if(o.amount is not null, o.amount, 0) as total_objective
        ')
        ->join('companies as c', 'c.id', 'sales_invoices.company_id')
        ->leftJoin('objectives as o', function($join) {
            $join->on('o.company_id', 'sales_invoices.company_id')
                ->whereColumn('o.fiscal_year', 'sales_invoices.fiscal_year');
        })
        ->leftJoin('sales_invoices_products as sip', 'sip.sinvoice_id', 'sales_invoices.id');

        $oQuery->groupBy('sales_invoices.company_id','sales_invoices.fiscal_year');

        $oQuery->orderBy('sales_invoices.company_id', 'asc');
        $oQuery->orderBy('sales_invoices.fiscal_year', 'asc');

        return $oQuery->get();

    }

    public function save(array $options = array(), $do_log = true)
    {
        $is_new = empty($this->id) ? true : false;
        if ($is_new){
            $current_year = date("Y");
            $this->company_id = empty($this->company_id) ? session('company')->id : $this->company_id;
            $this->fiscal_year = empty($this->fiscal_year) ? session('working_year') : $this->fiscal_year;

            $last_invoice = static::where('fiscal_year', $this->fiscal_year)->where('company_id', $this->company_id)->orderBy('sequential', 'desc')->take(1)->get()->first();
            if (empty($this->invoice_date)) {
                if (empty($last_invoice)) {
                    $this->invoice_date = today();
                } else {
                    if ($current_year == $this->fiscal_year) {
                        $this->invoice_date = (today() >= $last_invoice->invoice_date) ? today() : $last_invoice->invoice_date;
                    } else {
                        $this->invoice_date = $last_invoice->invoice_date;
                    }
                }
            } else {
                $this->invoice_date = (empty($last_invoice) || $this->invoice_date >= $last_invoice->invoice_date) ? $this->invoice_date : $last_invoice->invoice_date;
            }
            $sequential = ($last_invoice) ? $last_invoice->sequential + 1 : 1;
            $this->sequential = $sequential;
            $this->number = $this->fiscal_year.'-'.sprintf('%04d', $sequential);

        }
        parent::save($options, $do_log);
    }
    public function delete($do_log = true)
    {
        $last_invoice = static::where('fiscal_year', $this->fiscal_year)->where('company_id', $this->company_id)->orderBy('id', 'desc')->take(1)->get()->first();
        if ($last_invoice->id == $this->id && $this->verifactu_data == null) {
            SalesInvoicesProduct::where('sinvoice_id', $this->id)->delete();
            parent::delete($do_log);
        }
    }

    public function state() {
        return $this->hasOne(State::class, 'id', 'state_id');
    }
    public function company() {
        return $this->hasOne(Company::class, 'id', 'company_id');
    }
    public function thirdparty() {
        return $this->hasOne(Thirdparty::class, 'id', 'thirdparty_id');
    }
    public function banks_account() {
        return $this->hasOne(BanksAccount::class, 'id', 'bank_account_id');
    }
    public function sales_invoices_products() {
        return $this->hasMany(SalesInvoicesProduct::class, 'sinvoice_id', 'id');
    }
}
