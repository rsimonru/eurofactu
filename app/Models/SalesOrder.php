<?php

namespace App\Models;

use App\Models\Scopes\CompanyScope;
use App\Models\Scopes\FiscalYearScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\WithExtensions;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class SalesOrder extends Model
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

        $oQuery = static::select('sales_orders.*')
            ->leftJoinSub($totalsSubquery, 'totals', function($join) {
                $join->on('totals.sales_order_id', '=', 'sales_orders.id');
            })
            ->addSelect('totals.base_line', 'totals.tax_line', 'totals.es_line', 'totals.total_line')
            ->when($model_id>0, function($query) use ($model_id) {
                return $query->where('sales_orders.id', $model_id);
            });

        $oQuery = static::applyFilters($oQuery, $filters);

        foreach ($sort as $key => $value) {
            // Handle sorting on totals columns
            if (in_array($key, ['base_line', 'tax_line', 'total_line'])) {
                $oQuery->orderBy('totals.' . $key, $value);
            } else {
                $oQuery->orderBy('sales_orders.' . $key, $value);
            }
        }

        return static::getModelData($oQuery, $model_id, $records_in_page, $with, $paginatorName);
    }

    public static function applyFilters(
        $oQuery,
        ?array $filters = []
    ) {

        $oQuery->when(isset($filters['sorder_ids']) && !empty($filters['sorder_ids']), function($query) use ($filters) {
            return $query->whereIn('sales_orders.id', $filters['order_ids']);
        })
        ->when(isset($filters['state_id']) && !empty($filters['state_id']), function($query) use ($filters) {
            return $query->whereInto('sales_orders.state_id', $filters['state_id']);
        })
        ->when(isset($filters['company_id']) && !empty($filters['company_id']), function($query) use ($filters) {
            return $query->where('sales_orders.company_id', $filters['company_id']);
        })
        ->when(isset($filters['thirdparty_id']) && !empty($filters['thirdparty_id']), function($query) use ($filters) {
            return $query->whereInto('sales_orders.thirdparty_id', $filters['thirdparty_id']);
        })
        ->when(isset($filters['mark_sent']) && !empty($filters['mark_sent']), function($query) use ($filters) {
            return $query->whereNotNull('sales_orders.sent_date');
        })
        ->when(isset($filters['fiscal_year']) && !empty($filters['fiscal_year']), function($query) use ($filters) {
            return $query->where('sales_orders.fiscal_year', $filters['fiscal_year']);
        })
        ->when(isset($filters['date']) && isset($filters['date'][0]) && !empty($filters['date'][0]), function ($query) use ($filters) {
            if (empty($filters['date'][1]) || empty($filters['date'][2])) {
                return $query;
            }
            $from = (new Carbon($filters['date'][1]))->startOfDay();
            $to = (new Carbon($filters['date'][2]))->endOfDay();
            return $query->whereBetween('sales_orders.' . $filters['date'][0], [$from, $to]);
        })
        ->when(isset($filters['search']) && !empty($filters['search']), function($query) use ($filters) {
            return $query->where(function ($query) use ($filters){
                $query->where('sales_orders.number', 'like', '%'.$filters['search'].'%')
                ->orWhere('sales_orders.recipient', 'like', '%'.$filters['search'].'%')
                ->orWhere('sales_orders.reference', 'like', '%'.$filters['search'].'%');
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
            COUNT(distinct sales_orders.id) as documents,
            COALESCE(SUM(sop.base_line), 0) as base_line,
            COALESCE(SUM(sop.tax_line), 0) as tax_line,
            COALESCE(SUM(sop.es_line), 0) as es_line,
            COALESCE(SUM(sop.total_line), 0) as total_line
        ')
        ->leftJoin('sales_orders_products as sop', 'sop.sales_order_id', 'sales_orders.id')
        ->leftJoin('thirdparties', 'thirdparties.id', 'sales_orders.thirdparty_id');

        $oQuery = static::applyFilters($oQuery, $filters);

        if (!empty($group_by)) {
            foreach ($group_by as $group) {
                if ($group == 'thirdparty_id') {
                    $oQuery->addSelect(DB::raw('sales_orders.thirdparty_id , thirdparties.legal_form'));
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

            $last_order = SalesOrder::where('fiscal_year', $this->fiscal_year)->where('company_id', $this->company_id)->orderBy('sequential', 'desc')->take(1)->get()->first();
            if (empty($this->customer_date)) {
                $this->customer_date = today();
            }
            $sequential = ($last_order) ? $last_order->sequential + 1 : 1;
            $this->sequential = $sequential;
            $this->number = 'PV' . ($this->fiscal_year % 100) . '-' . sprintf('%04d', $sequential);

        }
        if ($is_dirty_state && $this->state_id === config('constants.states.sent') && $this->sent_date === null) {
            $this->sent_date = now();
        }
        parent::save($options, $do_log);
    }

    public function delete($do_log = true)
    {
        SalesOrdersProduct::where('sales_order_id', $this->id)->delete();
        parent::delete($do_log);
    }

    public function createNote($lines_units, $customer_date = null)
    {
        $thirdparty = $this->thirdparty;
        $note = new SalesNote();
        $note->company_id = $this->company_id;
        $note->thirdparty_id = $this->thirdparty_id;
        $note->sales_order_id = $this->id;
        $note->customer_date = $customer_date ? $customer_date : today();
        $note->state_id = config('constants.states.open');
        $note->vat = ($this->vat) ? $this->vat : $thirdparty->vat;
        $note->legal_form = ($this->legal_form) ? $this->legal_form : $thirdparty->legal_form;
        $note->recipient = ($this->recipient) ? $this->recipient : $thirdparty->contact;
        $note->reference = $this->reference;
        $note->address = ($this->address) ? $this->address : $thirdparty->address;
        $note->zip = ($this->zip) ? $this->zip : $thirdparty->zip;
        $note->town = ($this->town) ? $this->town : $thirdparty->town;
        $note->province = ($this->province) ? $this->province : $thirdparty->province;
        $note->country_id = ($this->country_id) ? $this->country_id : $thirdparty->country_id;
        $note->phone = ($this->phone) ? $this->phone : $thirdparty->phone;
        $note->email = ($this->email) ? $this->email : $thirdparty->email;
        $note->observations = $this->observations;
        $note->internal_note = $this->internal_note;
        $note->save();

        foreach ($this->products as $order_product) {
            if (isset($lines_units[$order_product->id])) {
                $units = $lines_units[$order_product->id] ?? 0;
                $note_product = new SalesNotesProduct();
                $note_product->sales_note_id = $note->id;
                $note_product->sales_orders_product_id = $order_product->id;
                $note_product->description = $order_product->description;
                $note_product->units = $units;
                $note_product->confirmed_units = $units;
                $note_product->save();
            }
        }

        return $note;
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
        return $this->hasMany(SalesOrdersProduct::class, 'sales_order_id');
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
