<?php

namespace App\Models;

use App\Models\Scopes\CompanyScope;
use App\Models\Scopes\FiscalYearScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\WithExtensions;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class SalesNote extends Model
{
    use HasFactory;
    use WithExtensions;
    use SoftDeletes;

    protected $casts = [
        'date' => 'date',
        'customer_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $fillable = [
        'thirdparty_id',
        'sales_order_id',
        'company_id',
        'fiscal_year',
        'sequential',
        'number',
        'date',
        'customer_date',
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
     * Get orders
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
        $totalsSubquery = SalesOrdersProduct::selectRaw('
                sales_order_id,
                COALESCE(SUM(base_line), 0) as base_line,
                COALESCE(SUM(tax_line), 0) as tax_line,
                COALESCE(SUM(es_line), 0) as es_line,
                COALESCE(SUM(total_line), 0) as total_line
            ')
            ->groupBy('sales_order_id');

        $oQuery = static::select('sales_notes.*')
            ->leftJoinSub($totalsSubquery, 'totals', function($join) {
                $join->on('totals.sales_order_id', '=', 'sales_notes.id');
            })
            ->addSelect('totals.base_line', 'totals.tax_line', 'totals.es_line', 'totals.total_line')
            ->when($model_id>0, function($query) use ($model_id) {
                return $query->where('sales_notes.id', $model_id);
            });

        $oQuery = static::applyFilters($oQuery, $filters);

        foreach ($sort as $key => $value) {
            // Handle sorting on totals columns
            if (in_array($key, ['base_line', 'tax_line', 'total_line'])) {
                $oQuery->orderBy('totals.' . $key, $value);
            } else {
                $oQuery->orderBy('sales_notes.' . $key, $value);
            }
        }

        return static::getModelData($oQuery, $model_id, $records_in_page, $with, $paginatorName);
    }

    public static function applyFilters(
        $oQuery,
        ?array $filters = []
    ) {

        $oQuery->when(isset($filters['snotes_ids']) && !empty($filters['snotes_ids']), function($query) use ($filters) {
            return $query->whereIn('sales_notes.id', $filters['snotes_ids']);
        })
        ->when(isset($filters['state_id']) && !empty($filters['state_id']), function($query) use ($filters) {
            return $query->whereInto('sales_notes.state_id', $filters['state_id']);
        })
        ->when(isset($filters['company_id']) && !empty($filters['company_id']), function($query) use ($filters) {
            return $query->where('sales_notes.company_id', $filters['company_id']);
        })
        ->when(isset($filters['thirdparty_id']) && !empty($filters['thirdparty_id']), function($query) use ($filters) {
            return $query->whereInto('sales_notes.thirdparty_id', $filters['thirdparty_id']);
        })
        ->when(isset($filters['fiscal_year']) && !empty($filters['fiscal_year']), function($query) use ($filters) {
            return $query->where('sales_notes.fiscal_year', $filters['fiscal_year']);
        })
        ->when(isset($filters['date']) && isset($filters['date'][0]) && !empty($filters['date'][0]), function ($query) use ($filters) {
            if (empty($filters['date'][1]) || empty($filters['date'][2])) {
                return $query;
            }
            $from = (new Carbon($filters['date'][1]))->startOfDay();
            $to = (new Carbon($filters['date'][2]))->endOfDay();
            return $query->whereBetween('sales_notes.' . $filters['date'][0], [$from, $to]);
        })
        ->when(isset($filters['search']) && !empty($filters['search']), function($query) use ($filters) {
            return $query->where(function ($query) use ($filters){
                $query->where('sales_notes.number', 'like', '%'.$filters['search'].'%')
                ->orWhere('sales_notes.recipient', 'like', '%'.$filters['search'].'%')
                ->orWhere('sales_notes.reference', 'like', '%'.$filters['search'].'%');
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
            COUNT(distinct sales_notes.id) as documents,
            COALESCE(SUM(sop.base_line), 0) as base_line,
            COALESCE(SUM(sop.tax_line), 0) as tax_line,
            COALESCE(SUM(sop.es_line), 0) as es_line,
            COALESCE(SUM(sop.total_line), 0) as total_line
        ')
        ->leftJoin('sales_notes_products as snp', 'snp.sales_note_id', 'sales_notes.id')
        ->leftJoin('sales_orders_products as sop', 'snp.sales_orders_product_id', 'sop.id')
        ->leftJoin('thirdparties', 'thirdparties.id', 'sales_notes.thirdparty_id');

        $oQuery = static::applyFilters($oQuery, $filters);

        if (!empty($group_by)) {
            foreach ($group_by as $group) {
                if ($group == 'thirdparty_id') {
                    $oQuery->addSelect(DB::raw('sales_notes.thirdparty_id , thirdparties.legal_form'));
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
        $tax_summary = $this->products->where('units', '<>', 0)->groupBy('sales_orders_product.tax_type')
            ->map(function ($items, $key) use ($tax_types) {
                $base = $items->sum(function ($item) { return $item->units * $item->sales_orders_product->base_result; });
                $tax = $items->sum(function ($item) { return $item->units * $item->sales_orders_product->base_result * $item->sales_orders_product->tax_type; });
                $es = $items->sum(function ($item) { return $item->units * $item->sales_orders_product->base_result * $item->sales_orders_product->es_type; });
                $total = $items->sum(function ($item) { return $item->units * $item->sales_orders_product->base_result * (1 + $item->sales_orders_product->tax_type + $item->sales_orders_product->es_type); });
                return [
                    'tax_name' => ($tax_types[$items->first()->sales_orders_product->tax_type_id]->tax->description . ' ' . $tax_types[$items->first()->sales_orders_product->tax_type_id]->type) ?? '',
                    'tax_rate' => $key,
                    'base_line' => $base,
                    'tax_line' => $tax,
                    'es_rate' => $items->first()->sales_orders_product->es_type,
                    'es_line' => $es,
                    'total_line' => $total,
                    'retention' => $base * $this->tax_retention,
                ];
            })->values()->toArray();

        $summary = [
            'tax_summary' => $tax_summary,
            'base_line' => $this->products->sum(function ($item) { return $item->units * $item->sales_orders_product->base_result; }),
            'tax_line' => $this->products->sum(function ($item) { return $item->units * $item->sales_orders_product->base_result * $item->sales_orders_product->tax_type; }),
            'es_line' => $this->products->sum(function ($item) { return $item->units * $item->sales_orders_product->base_result * $item->sales_orders_product->es_type; }),
            'total_line' => $this->products->sum(function ($item) { return $item->units * $item->sales_orders_product->base_result * (1 + $item->sales_orders_product->tax_type + $item->sales_orders_product->es_type); }),
            'retention' => $this->products->sum(function ($item) { return $item->units * $item->sales_orders_product->base_result; }) * $this->tax_retention,
        ];

        return $summary;
    }

    public function save(array $options = array(), $do_log = true)
    {
        $is_new = empty($this->id) ? true : false;
        $is_dirty_state = $this->isDirty('state_id');
        if ($is_new) {
            $this->company_id = empty($this->company_id) ? session('company')->id ?? 1 : $this->company_id;
            $this->fiscal_year = empty($this->fiscal_year) ? session('working_year', today()->format('Y')) : $this->fiscal_year;

            $last_order = static::where('fiscal_year', $this->fiscal_year)->where('company_id', $this->company_id)->orderBy('sequential', 'desc')->take(1)->get()->first();
            if (empty($this->customer_date)) {
                $this->customer_date = today();
            }
            $sequential = ($last_order) ? $last_order->sequential + 1 : 1;
            $this->sequential = $sequential;
            $this->number = 'AV' . ($this->fiscal_year % 100) . '-' . sprintf('%04d', $sequential);

        }
        if ($is_dirty_state && $this->state_id === config('constants.states.sent') && $this->sent_date === null) {
            $this->sent_date = now();
        }
        parent::save($options, $do_log);
    }

    public function delete($do_log = true)
    {
        SalesNotesProduct::where('sales_note_id', $this->id)->delete();
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

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function products()
    {
        return $this->hasMany(SalesNotesProduct::class, 'sales_note_id');
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
