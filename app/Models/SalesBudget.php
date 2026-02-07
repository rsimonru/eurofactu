<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\WithExtensions;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use App\Models\Scopes\CompanyScope;
use App\Models\Scopes\FiscalYearScope;

class SalesBudget extends Model
{
    use HasFactory;
    use WithExtensions;
    use SoftDeletes;

    protected $casts = [
        'date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'valid_until' => 'datetime',
        'sent_date' => 'date',
    ];

    protected $fillable = [
        'thirdparty_id',
        'company_id',
        'fiscal_year',
        'sequential',
        'number',
        'date',
        'sent_date',
        'valid_until',
        'state_id',
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
    ];

    protected static function booted()
    {
        static::addGlobalScope(new CompanyScope);
        static::addGlobalScope(new FiscalYearScope);
    }

    /**
     * Get budgets
     *
     * @param int $model_id
     * @param int $records_in_page
     * @param array $sort (attribute => 'asc'/'desc')
     * @param array $filters
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
        // Use subquery for totals to avoid expensive groupBy before pagination
        // This is much more efficient than leftJoin + groupBy on all records
        $totalsSubquery = SalesBudgetsProduct::selectRaw('
                sales_budget_id,
                COALESCE(SUM(base_line), 0) as base_line,
                COALESCE(SUM(tax_line), 0) as tax_line,
                COALESCE(SUM(es_line), 0) as es_line,
                COALESCE(SUM(total_line), 0) as total_line
            ')
            ->groupBy('sales_budget_id');

        $oQuery = static::select('sales_budgets.*')
            ->leftJoinSub($totalsSubquery, 'totals', function($join) {
                $join->on('totals.sales_budget_id', '=', 'sales_budgets.id');
            })
            ->addSelect('totals.base_line', 'totals.tax_line', 'totals.es_line', 'totals.total_line')
            ->when($model_id>0, function($query) use ($model_id) {
                return $query->where('sales_budgets.id', $model_id);
            });

        $oQuery = static::applyFilters($oQuery, $filters);

        foreach ($sort as $key => $value) {
            // Handle sorting on totals columns
            if (in_array($key, ['base_line', 'tax_line', 'total_line'])) {
                $oQuery->orderBy('totals.' . $key, $value);
            } else {
                $oQuery->orderBy('sales_budgets.' . $key, $value);
            }
        }

        return static::getModelData($oQuery, $model_id, $records_in_page, $with, $paginatorName);
    }

    public static function applyFilters(
        $oQuery,
        ?array $filters = []
    ) {

        $oQuery->when(isset($filters['budget_ids']) && !empty($filters['budget_ids']), function($query) use ($filters) {
            return $query->whereIn('sales_budgets.id', $filters['budget_ids']);
        })
        ->when(isset($filters['state_id']) && !empty($filters['state_id']), function($query) use ($filters) {
            return $query->whereInto('sales_budgets.state_id', $filters['state_id']);
        })
        ->when(isset($filters['company_id']) && !empty($filters['company_id']), function($query) use ($filters) {
            return $query->where('sales_budgets.company_id', $filters['company_id']);
        })
        ->when(isset($filters['thirdparty_id']) && !empty($filters['thirdparty_id']), function($query) use ($filters) {
            return $query->whereInto('sales_budgets.thirdparty_id', $filters['thirdparty_id']);
        })
        ->when(isset($filters['mark_sent']) && !empty($filters['mark_sent']), function($query) use ($filters) {
            return $query->whereNotNull('sales_budgets.sent_date');
        })
        ->when(isset($filters['fiscal_year']) && !empty($filters['fiscal_year']), function($query) use ($filters) {
            return $query->where('sales_budgets.fiscal_year', $filters['fiscal_year']);
        })
        ->when(isset($filters['date']) && isset($filters['date'][0]) && !empty($filters['date'][0]), function ($query) use ($filters) {
            if (empty($filters['date'][1]) || empty($filters['date'][2])) {
                return $query;
            }
            $from = (new Carbon($filters['date'][1]))->startOfDay();
            $to = (new Carbon($filters['date'][2]))->endOfDay();
            return $query->whereBetween('sales_budgets.' . $filters['date'][0], [$from, $to]);
        })
        ->when(isset($filters['search']) && !empty($filters['search']), function($query) use ($filters) {
            return $query->where(function ($query) use ($filters){
                $query->where('sales_budgets.number', 'like', '%'.$filters['search'].'%')
                ->orWhere('sales_budgets.recipient', 'like', '%'.$filters['search'].'%')
                ->orWhere('sales_budgets.reference', 'like', '%'.$filters['search'].'%');
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
            COUNT(distinct sales_budgets.id) as documents,
            COALESCE(SUM(sbp.base_line), 0) as base_line,
            COALESCE(SUM(sbp.tax_line), 0) as tax_line,
            COALESCE(SUM(sbp.es_line), 0) as es_line,
            COALESCE(SUM(sbp.total_line), 0) as total_line
        ')
        ->leftJoin('sales_budgets_products as sbp', 'sbp.sales_budget_id', 'sales_budgets.id')
        ->leftJoin('thirdparties', 'thirdparties.id', 'sales_budgets.thirdparty_id');

        $oQuery = static::applyFilters($oQuery, $filters);

        if (!empty($group_by)) {
            foreach ($group_by as $group) {
                if ($group == 'thirdparty_id') {
                    $oQuery->addSelect(DB::raw('sales_budgets.thirdparty_id , thirdparties.legal_form'));
                }
                $oQuery->groupBy($group);
            }
            return static::getModelData($oQuery, 0, $records_in_page);
        } else {
            return $oQuery->get()->first();
        }
    }
    public function emtGetProductsSummary($tax_types = null)
    {
        if (empty($tax_types)) {
            $tax_types = TaxType::emtGet(records_in_page: -1, filters: ['tax_id' => $this->tax_id], with: ['tax'])->keyBy('id');
        }
        $tax_summary = $this->products->where('units', '<>', 0)->groupBy('tax_type')
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
            'base_line' => $this->products->sum('base_line'),
            'tax_line' => $this->products->sum('tax_line'),
            'es_line' => $this->products->sum('es_line'),
            'total_line' => $this->products->sum('total_line'),
            'retention' => $this->products->sum('base_line') * $this->tax_retention,
        ];

        return $summary;
    }

    public function save(array $options = array(), $do_log = true)
    {
        $is_new = empty($this->id) ? true : false;
        $is_dirty_state = $this->isDirty('state_id');
        if ($is_new) {
            $this->company_id = empty($this->company_id) ? session('company')->id ?? 1 : $this->company_id;
            $this->tax_id = empty($this->tax_id) ? session('company')->tax_id ?? 1 : $this->tax_id;
            $this->fiscal_year = empty($this->fiscal_year) ? session('working_year', today()->format('Y')) : $this->fiscal_year;

            $last_budget = static::where('fiscal_year', $this->fiscal_year)->where('company_id', $this->company_id)->orderBy('sequential', 'desc')->take(1)->get()->first();
            if (empty($this->date)) {
                $this->date = today();
            }
            $sequential = ($last_budget) ? $last_budget->sequential + 1 : 1;
            $this->sequential = $sequential;
            $this->number = 'PR' . ($this->fiscal_year % 100) . '-' . sprintf('%04d', $sequential);
        }
        if ($is_dirty_state && $this->state_id === config('constants.states.sent') && $this->sent_date === null) {
            $this->sent_date = now();
        }
        parent::save($options, $do_log);
    }

    public function createOrder($lines_units, $customer_date = null)
    {
        $thirdparty = $this->thirdparty;
        $order = new SalesOrder();
        $order->company_id = $this->company_id;
        $order->thirdparty_id = $this->thirdparty_id;
        $order->sales_budget_id = $this->id;
        $order->date = today();
        $order->customer_date = $customer_date ? $customer_date : today();
        $order->state_id = config('constants.states.open');
        $order->tax_retention = $this->tax_retention;
        $order->vat = ($this->vat) ? $this->vat : $thirdparty->vat;
        $order->legal_form = ($this->legal_form) ? $this->legal_form : $thirdparty->legal_form;
        $order->recipient = ($this->recipient) ? $this->recipient : $thirdparty->contact;
        $order->reference = $this->reference;
        $order->address = ($this->address) ? $this->address : $thirdparty->address;
        $order->zip = ($this->zip) ? $this->zip : $thirdparty->zip;
        $order->town = ($this->town) ? $this->town : $thirdparty->town;
        $order->province = ($this->province) ? $this->province : $thirdparty->province;
        $order->country_id = ($this->country_id) ? $this->country_id : $thirdparty->country_id;
        $order->phone = ($this->phone) ? $this->phone : $thirdparty->phone;
        $order->email = ($this->email) ? $this->email : $thirdparty->email;
        $order->observations = $this->observations;
        $order->internal_note = $this->internal_note;
        $order->save();

        foreach ($this->products as $budget_product) {
            if (isset($lines_units[$budget_product->id])) {
                $units = $lines_units[$budget_product->id];
                $order_product = new SalesOrdersProduct();
                $order_product->sales_order_id = $order->id;
                $order_product->product_variant_id = $budget_product->product_variant_id;
                $order_product->description = $budget_product->description;
                $order_product->units = $lines_units[$budget_product->id] ?? 0;
                $order_product->base_unit = $budget_product->base_unit;
                $order_product->discount_type = $budget_product->discount_type;
                $order_product->discountp = $budget_product->discountp;
                $order_product->discounti = $budget_product->discounti;
                $order_product->base_result = $budget_product->base_result;
                $order_product->tax_type_id = $budget_product->tax_type_id;
                $order_product->tax_type = $budget_product->tax_type;
                $order_product->tax_unit = $budget_product->tax_unit;
                $order_product->tax_line = $budget_product->tax_unit * $units;
                $order_product->es_type = $budget_product->es_type;
                $order_product->es_unit = $budget_product->es_unit;
                $order_product->es_line = $budget_product->es_unit * $units;
                $order_product->base_line = $budget_product->base_result * $units;
                $order_product->total_line = $order_product->base_line + $order_product->tax_line + $order_product->es_line;
                $order_product->save();
            }
        }

        return $order;
    }

    public function delete($do_log = true)
    {
        SalesBudgetsProduct::where('sales_budget_id', $this->id)->delete();
        parent::delete($do_log);
    }

    /**
     * Relationships
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function thirdparty()
    {
        return $this->belongsTo(Thirdparty::class);
    }

    public function tax()
    {
        return $this->belongsTo(Tax::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function products()
    {
        return $this->hasMany(SalesBudgetsProduct::class, 'sales_budget_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
