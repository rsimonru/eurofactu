<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\WithExtensions;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory;
    use WithExtensions;
    use SoftDeletes;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'parameters' => 'array',
        'verifactu_data' => 'array',
        'certificate_expiration' => 'date',
    ];

    /**
     * Get companies.
     *
     * @param int $model_id
     * @param int $records_in_page
     * @param array $sort (attribute => 'asc'/'desc')
     * @param array $filters
     * @return mixed Colletion
     *
     */
    public static function emtGet(
        int $model_id = 0,
        int $records_in_page = 0,
        array $sort = [],
        ?array $filters = []
    ) {

        $oQuery = static::select('companies.*')
        ->when($model_id>0, function($query)  use ($model_id) {
            return $query->where('companies.id', $model_id);
        });

        $oQuery = static::emtApplyFilters($oQuery, $filters);

        // Order by
        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }

        $model_id = ($model_id > 0) ? $model_id : ((isset($filters['code']) && !empty($filters['code'])) ? 1:0);
        return static::getModelData($oQuery, $model_id, $records_in_page);
    }
    public static function emtApplyFilters(
        $oQuery,
        ?array $filters = []
    ) {

        $oQuery->when(isset($filters['code']) && !empty($filters['code']), function ($query) use ($filters) {
            return $query->where('companies.code', $filters['code']);
        })
        ->when(isset($filters['legal_form']) && !empty($filters['legal_form']), function($query) use ($filters) {
            return $query->where('companies.legal_form', 'like', '%'.$filters['legal_form'].'%');
        })
        ->when(isset($filters['name']) && !empty($filters['name']), function($query) use ($filters) {
            return $query->where('companies.name', 'like', '%'.$filters['name'].'%');
        })
        ->when(isset($filters['active']) && $filters['active']!==null, function($query) use ($filters) {
            return $query->where('companies.active', $filters['active']);
        });

        return $oQuery;
    }

    public function banks_accounts() {
        return $this->hasMany(BanksAccount::class, 'company_id', 'id');
    }
    public function banks_account() {
        return $this->hasOne(BanksAccount::class, 'company_id', 'id')->where('default', 1);
    }
}
