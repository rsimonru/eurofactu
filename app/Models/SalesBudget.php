<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\WithExtensions;
use Illuminate\Database\Eloquent\SoftDeletes;

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
                COALESCE(SUM(total_line), 0) as total_line
            ')
            ->groupBy('sales_budget_id');

        $oQuery = static::select('sales_budgets.*')
            ->leftJoinSub($totalsSubquery, 'totals', function($join) {
                $join->on('totals.sales_budget_id', '=', 'sales_budgets.id');
            })
            ->addSelect('totals.base_line', 'totals.tax_line', 'totals.total_line')
            ->when($model_id>0, function($query) use ($model_id) {
                return $query->where('sales_budgets.id', $model_id);
            });

        $oQuery = static::emtApplyFilters($oQuery, $filters);

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

    public static function emtApplyFilters(
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
        ->when(isset($filters['date']) && !empty($filters['date']), function ($query) use ($filters) {
            return $query->whereBetween('sales_budgets.' . $filters['date'][0], [$filters['date'][1], $filters['date'][2]]);
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

    /**
     * Get sales summary.
     *
     * @param array $filters
     * @return mixed Collection
     *
     */
    public static function emtGetSummary(
        ?array $filters = []
    ) {
        $oQuery = static::selectRaw('
            count(distinct sales_budgets.id) as documents,
            COALESCE(SUM(sbp.base_line), 0) as base_line,
            COALESCE(SUM(sbp.tax_line), 0) as tax_line,
            COALESCE(SUM(sbp.total_line), 0) as total_line
        ')
        ->leftJoin('sales_budgets_products as sbp', 'sbp.sales_budget_id', 'sales_budgets.id');
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
            sum(sbp.base_line) as base_line,
            sum(sbp.tax_line) as tax_line,
            sum(sbp.total_line) as total_line
        ')
        ->join('thirdparties as t', 't.id', 'sales_budgets.thirdparty_id')
        ->leftJoin('sales_budgets_products as sbp', 'sbp.sales_budget_id', 'sales_budgets.id');
        $oQuery = static::emtApplyFilters($oQuery, $filters);

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }

        $oQuery->groupBy('sales_budgets.thirdparty_id');

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
            sum(sbp.base_line) as base_line,
            sum(sbp.tax_line) as tax_line,
            sum(sbp.total_line) as total_line
        ')
        ->join('companies as c', 'c.id', 'sales_budgets.company_id')
        ->leftJoin('sales_budgets_products as sbp', 'sbp.sales_budget_id', 'sales_budgets.id');
        $oQuery = static::emtApplyFilters($oQuery, $filters);

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }

        $oQuery->groupBy('sales_budgets.company_id');

        return static::getModelData($oQuery, 0, $records_in_page);
    }

    public function save(array $options = array(), $do_log = true)
    {
        $is_new = empty($this->id) ? true : false;
        if ($is_new) {
            $this->company_id = empty($this->company_id) ? session('company')->id ?? 1 : $this->company_id;
            $this->fiscal_year = empty($this->fiscal_year) ? session('working_year', today()->format('Y')) : $this->fiscal_year;

            $last_budget = SalesBudget::where('fiscal_year', $this->fiscal_year)->where('company_id', $this->company_id)->orderBy('sequential', 'desc')->take(1)->get()->first();
            if (empty($this->date)) {
                $this->date = today();
            }
            $sequential = ($last_budget) ? $last_budget->sequential + 1 : 1;
            $this->sequential = $sequential;
            $this->number = 'PR' . $this->fiscal_year.'-'.sprintf('%04d', $sequential);

        }
        parent::save($options, $do_log);
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
