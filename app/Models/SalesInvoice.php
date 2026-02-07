<?php

namespace App\Models;

use App\Models\Scopes\CompanyScope;
use App\Models\Scopes\FiscalYearScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\WithExtensions;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class SalesInvoice extends Model
{
    use HasFactory;
    use WithExtensions;
    use SoftDeletes;

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'invoice_date' => 'date',
        'expiration_date' => 'date',
        'paid_date' => 'date',
        'sent_date' => 'date',
        'verifactu_data' => 'array',
    ];

    protected $fillable = [
        'thirdparty_id',
        'company_id',
        'fiscal_year',
        'sequential',
        'number',
        'invoice_date',
        'state_id',
        'tax_id',
        'vat',
        'legal_form',
        'recipient',
        'reference',
        'address',
        'zip',
        'town',
        'province',
        'country_id',
        'phone',
        'email',
        'observations',
        'internal_note',
        'tax_retention',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new CompanyScope);
        static::addGlobalScope(new FiscalYearScope);
    }

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

        $oQuery = static::applyFilters($oQuery, $filters);

        $oQuery->groupBy('sales_invoices.id');

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }
        // $oQuery->dd();
        return static::getModelData($oQuery, $model_id, $records_in_page, $with, $paginatorName);
    }
    public static function applyFilters(
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
            if (empty($filters['date'][1]) || empty($filters['date'][2])) {
                return $query;
            }
            $from = (new Carbon($filters['date'][1]))->startOfDay();
            $to = (new Carbon($filters['date'][2]))->endOfDay();
            return $query->whereBetween('sales_invoices.' . $filters['date'][0], [$from, $to]);
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
    public static function emtGetSummary(
        ?array $filters = [],
        int $records_in_page = 0,
        array $group_by = []
    ) {
        $oQuery = static::selectRaw('
            COUNT(distinct sales_invoices.id) as documents,
            COALESCE(SUM(sip.base_line), 0) as base_line,
            COALESCE(SUM(sip.tax_line), 0) as tax_line,
            COALESCE(SUM(sip.total_line), 0) as total_line,
            COALESCE(SUM(if(sales_invoices.paid_date is not null, sip.total_line,0)), 0) as total_paid
        ')
        ->leftJoin('sales_invoices_products as sip', 'sip.sinvoice_id', 'sales_invoices.id')
        ->leftJoin('thirdparties', 'thirdparties.id', 'sales_invoices.thirdparty_id');

        $oQuery = static::applyFilters($oQuery, $filters);

        if (!empty($group_by)) {
            foreach ($group_by as $group) {
                if ($group == 'thirdparty_id') {
                    $oQuery->addSelect(DB::raw('sales_invoices.thirdparty_id , thirdparties.legal_form'));
                }
                $oQuery->groupBy($group);
            }
            return static::getModelData($oQuery, 0, $records_in_page);
        } else {
            return $oQuery->get()->first();
        }

        return $oQuery->get()->first();
    }

    public function emtGetProductsSummary($tax_types = null)
    {
        if (empty($tax_types)) {
            $tax_types = TaxType::emtGet(records_in_page: -1, filters: ['tax_id' => $this->tax_id], with: ['tax'])->keyBy('id');
        }
        $tax_summary = $this->sales_invoices_products->where('units', '<>', 0)->groupBy('tax_type')
            ->map(function ($items, $key) use ($tax_types) {
                $base = $items->sum('base_line');
                $tax = $items->sum('tax_line');
                $es = $items->sum('es_line');
                $total = $items->sum('total_line');
                return [
                    'tax_name' => ($tax_types[$items->first()->tax_type_id]->tax->description . ' ' . $tax_types[$items->first()->tax_type_id]->type) ?? '',
                    'tax_rate' => $key,
                    'base_line' => $base,
                    'tax_line' => $tax,
                    'es_rate' => $items->first()->es_type,
                    'es_line' => $es,
                    'total_line' => $total,
                    'retention' => $base * $this->tax_retention,
                ];
            })->values()->toArray();

        $summary = [
            'tax_summary' => $tax_summary,
            'base_line' => $this->sales_invoices_products->sum('base_line'),
            'tax_line' => $this->sales_invoices_products->sum('tax_line'),
            'es_line' => $this->sales_invoices_products->sum('es_line'),
            'total_line' => $this->sales_invoices_products->sum('total_line'),
            'retention' => $this->sales_invoices_products->sum('base_line') * $this->tax_retention,
        ];

        return $summary;
    }

    public function save(array $options = array(), $do_log = true)
    {
        $is_new = empty($this->id) ? true : false;
        if ($is_new){
            $current_year = date("Y");
            $this->company_id = empty($this->company_id) ? session('company')->id : $this->company_id;
            $this->tax_id = empty($this->tax_id) ? session('company')->tax_id ?? 1 : $this->tax_id;
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
            $this->number = 'FV' . ($this->fiscal_year % 100) . '-' . sprintf('%04d', $sequential);

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
    public function tax() {
        return $this->belongsTo(Tax::class);
    }
    public function banks_account() {
        return $this->hasOne(BanksAccount::class, 'id', 'bank_account_id');
    }
    public function sales_invoices_products() {
        return $this->hasMany(SalesInvoicesProduct::class, 'sinvoice_id', 'id');
    }
}
