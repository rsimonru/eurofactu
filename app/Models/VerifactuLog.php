<?php

namespace App\Models;

use App\Traits\WithExtensions;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VerifactuLog extends Model
{
    use HasFactory;
    use WithExtensions;

    protected $casts = [
        'records' => 'array',
        'response' => 'array',
    ];

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

        $query = static::select('verifactu_logs.*')
        ->when($model_id > 0, function ($query) use ($model_id) {
            return $query->where('verifactu_logs.id', $model_id);
        })
        ;

        $query = static::applyFilters($query, $filters);

        foreach ($sort as $key => $value) {
            $query->orderBy($key, $value);
        }

        return static::getModelData($query, $model_id, $records_in_page, $with);
    }

    private static function applyFilters(
        $query,
        ?array $filters = []
    ) {

        $query->when(isset($filters['verifactu_log_ids']) && !empty($filters['verifactu_log_ids']), function($query) use ($filters) {
            $query->whereIn('verifactu_logs.id', $filters['verifactu_log_ids']);
        })
        ->when(isset($filters['company_id']) && !empty($filters['company_id']), function($query) use ($filters) {
            return $query->where('verifactu_logs.company_id', $filters['company_id']);
        })
        ;

        return $query;
    }

    public function company() {
        return $this->hasOne(Company::class, 'id', 'company_id');
    }

}
