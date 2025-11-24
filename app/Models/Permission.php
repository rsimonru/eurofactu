<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Permission as SpatiePermission;
use App\Traits\WithExtensions;

class Permission extends SpatiePermission
{
    use HasFactory;
    use WithExtensions;
    use HasTranslations;

    protected $translatable = ['description'];

    /**
     * Get roles
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
        array $filters = []
    ) {

        $oQuery = static::select('permissions.*')
        ->when($model_id>0, function($query) use ($model_id) {
            return $query->where('permissions.id', $model_id);
        })
        ->where('permissions.level', '<=', auth()->user()->level->id)
        ->when(isset($filters['class']) && !empty($filters['class']), function($query) use ($filters) {
            return $query->where('permissions.class', $filters['class']);
        })
        ->when(isset($filters['model']) && !empty($filters['model']), function($query) use ($filters) {
            return $query->where('permissions.model', $filters['model']);
        })
        ->when(isset($filters['model_id']) && !empty($filters['model_id']), function($query) use ($filters) {
            return $query->where('permissions.model_id', $filters['model_id']);
        })
        ;

        foreach ($sort as $key => $value) {
            $oQuery->orderBy($key, $value);
        }
        //dd($oQuery->toSql());
        return static::getModelData($oQuery, $model_id, $records_in_page);
    }

    public function menu()
    {
        return $this->hasOne(Menu::class, 'id', 'model_id');
    }
    public function company()
    {
        return $this->hasOne(Company::class, 'id', 'model_id');
    }
}
