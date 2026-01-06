<?php

namespace App\Models;

use App\Traits\WithExtensions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class BanksAccount extends Model
{
    use HasFactory;
    use SoftDeletes;
    use WithExtensions;

    /**
     * Get records.
     *
     * @param int $model_id
     * @param int $records_in_page
     * @param array $sort (attribute => 'asc'/'desc')
     * @param array $filters
     * @return mixed Colletion
     *
     */
    public static function emtGet(
        ?int $model_id = 0,
        int $records_in_page = 0,
        array $sort = [],
        ?array $filters = [],
        array $with = []
    ) {
        $query = static::select('banks_accounts.*')
        ->when($model_id > 0, function ($query) use ($model_id) {
            return $query->where('banks_accounts.id', $model_id);
        })
        ;

        $query = static::applyFilters($query, $filters);

        foreach ($sort as $key => $value) {
            $query->orderBy($key, $value);
        }

        return static::getModelData($query, $model_id, $records_in_page, $with);
    }

    public static function applyFilters(
        $query,
        ?array $filters = []
    ) {

        $query->when(isset($filters['account_ids']) && !empty($filters['account_ids']), function($query) use ($filters) {
            $query->whereIn('banks_accounts.id', $filters['account_ids']);
        })
        ->when(isset($filters['company_id']) && !empty($filters['company_id']), function($query) use ($filters) {
            $query->where('banks_accounts.company_id', $filters['company_id']);
        })
        ->when(isset($filters['default']) && !empty($filters['default']), function($query) use ($filters) {
            $query->where('banks_accounts.default', $filters['default']);
        })
        ->when(isset($filters['name']) && !empty($filters['name']), function($query) use ($filters) {
            $query->where('banks_accounts.name', 'like', '%'.$filters['name'].'%');
        })
        ;

        return $query;
    }

    public function save(array $options = array(), $do_log = true)
    {
        if ($this->isDirty('default') && $this->default){
            $this->active = true;
            static::where('company_id', $this->company_id)->update(['default' => false]);
        }
        parent::save($options, $do_log);
    }

}
