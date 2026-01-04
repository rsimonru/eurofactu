<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Notifications\ResetPasswordMail;
use App\Traits\HasTranslations;
use App\Traits\WithExtensions;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;
    use WithExtensions;
    use HasRoles;
    use HasTranslations;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login' => 'datetime',
            'password' => 'hashed',
            'active' => 'boolean',
            'filters' => 'array',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

        /**
     * Get users
     *
     * @param int $iModels_id
     * @param int $records_in_page
     * @param array $aSort (attribute => 'asc'/'desc')
     * @param array $filters
     * @return mixed Collection
     *
     */
    public static function emtGet(
        int $model_id=0,
        int $records_in_page = 0,
        array $sort = [],
        array $filters = [],
        array $with = []
    ) {

        $query = static::select('users.*', 'l.name as level_name', 'l.level as level_number')
        ->join('levels as l', 'l.id', 'users.level_id')
        ->when($model_id > 0 , function($query) use ($model_id) {
            return $query->where('users.id', $model_id);
        })
        ;

        $query = static::emtApplyFilters($query, $filters);

        foreach ($sort as $key => $value) {
            $query->orderBy($key, $value);
        }

        // $query->dd();
        return static::getModelData($query, $model_id, $records_in_page, $with);
    }

    /**
     * Apply filters.
     *
     * @param $oQuery
     * @param array $filters
     * @return mixed Query
     *
     */
    public static function emtApplyFilters(
        $oQuery,
        ?array $filters = []
    ) {

        $oQuery
        ->when(isset($filters['active']) && ($filters['active'] == 1 || $filters['active'] == 0), function($query) use ($filters) {
            return $query->where('users.active', $filters['active']);
        })
        ->when(isset($filters['level']) && !empty($filters['level']), function($query) use ($filters) {
            return $query->where('l.level', '<=', $filters['level']);
        })
        ->when(isset($filters['under_level']) && !empty($filters['under_level']), function($query) use ($filters) {
            return $query->where('l.level', '<', $filters['under_level']);
        })
        ->when(isset($filters['min_level']) && !empty($filters['min_level']), function($query) use ($filters) {
            return $query->where('l.level', '>=', $filters['min_level']);
        })
        ->when(isset($filters['max_level']) && !empty($filters['max_level']), function($query) use ($filters) {
            return $query->where('l.level', '<=', $filters['max_level']);
        })
        ->when(isset($filters['level_id']) && !empty($filters['level_id']), function($query) use ($filters) {
            return $query->where('users.level_id',$filters['level_id']);
        })
        ->when(isset($filters['commission_on_invoices']) && $filters['commission_on_invoices'] !== null, function($query) use ($filters) {
            return $query->where('users.commission_on_invoices',$filters['commission_on_invoices']);
        })
        ->when(isset($filters['levels']) && !empty($filters['levels']), function($query) use ($filters) {
            return $query->whereIn('users.level_id',$filters['levels']);
        })
        ->when(isset($filters['search']) && !empty($filters['search']), function($query) use ($filters) {
            return $query->where(function ($query) use ($filters) {
                $query->where('users.email', 'like', '%'.$filters['search'].'%')
                ->orWhere('users.name', 'like', '%'.$filters['search'].'%');
            });
        });

        return $oQuery;
    }

    /**
     * Get user filters.
     *
     * @return mixed aFilters array()
     *
     */
    public static function getFilters($key = '')
    {
        $oUser = User::find(Auth::user()->id);
        $aUFilters = $oUser->filters;
        $templates = config('filters');
        $flist = [
            'FilterOn' => 0,
            //'ModalName' => 'modal_Filter',
            'aValues' => [],
        ];
        $filters = [];
        $filters_defaults = [];

        if (!empty($key)) { // Si la clave del filtro no está vacía
            if (!isset($aUFilters[$key]) || empty($aUFilters[$key])) { // Si el usuario no tiene filtros para esa clave
                if (isset($templates[$key][0])) { // Si existe plantilla para esa clave
                    $aUFilters[$key] = $templates[$key][0];
                    $oUser->filters = $aUFilters;
                    $oUser->save();
                } else {
                    return []; // No hay plantilla para esa clave
                }
            }

            $filters = $aUFilters;
            foreach ($templates[$key][1] as $key1 => $value) {
                $aValues = [];
                $label = '';
                if ($aUFilters[$key][$key1]) {
                    switch ($value[0]) {
                        case 'text':
                            $aValues = [$aUFilters[$key][$key1]];
                            $label = $value[2];
                            break;
                        case 'boolean':
                            $aValues = [$aUFilters[$key][$key1] == 1 ? true : false];
                            $label = $value[2];
                            break;
                        case 'array':
                            $aValuesF = (is_array($aUFilters[$key][$key1])) ? $aUFilters[$key][$key1] : explode(',', $aUFilters[$key][$key1]);
                            if (is_array($value[1])) {
                                $aValues = array_intersect_key($value[1], array_combine(array_values($aValuesF), array_values($aValuesF)));
                            } else {
                                $aTable = explode(":", $value[1]);
                                $vcModel = $aTable[0];
                                $vcAttrib = $aTable[1];
                                if ($vcModel == 'Select') {
                                    $aTmpValues = Select::emtGet($vcAttrib);
                                    $aValues = [];
                                    foreach ($aTmpValues as $key2 => $value2) {
                                        $aValues[$value2['value']] = $value2['option'];
                                    }
                                    $aValues = array_intersect_key($aValues, array_combine(array_values($aValuesF), array_values($aValuesF)));
                                } else {
                                    $vcModel = "App\\Models\\" . $vcModel;
                                    $aTmpValues = $vcModel::selectRaw('id,`' . $vcAttrib . '` `value`')->get()->toArray();
                                    $aValues = [];
                                    foreach ($aTmpValues as $key2 => $value2) {
                                        $aValues[$value2['id']] = $value2['value'];
                                    }
                                    $aValues = array_intersect_key($aValues, array_combine(array_values($aValuesF), array_values($aValuesF)));
                                }
                            }
                            $label = $value[2];
                            break;
                        case 'date':
                            if (!empty($aUFilters[$key][$key1][0])) {
                                $date_from = new Carbon($aUFilters[$key][$key1][1]);
                                $date_to = new Carbon($aUFilters[$key][$key1][2]);
                                $label = config('constants.date_type.' . $aUFilters[$key][$key1][0]);
                                $aValues = [$date_from->format('d-m-Y') . ' a ' . $date_to->format('d-m-Y')];
                            }
                            break;
                    }
                    if (!empty($aValues)) {
                        $flist['FilterOn'] = 1;
                        $flist['aValues'][$key1] = array(
                            'aValues' => $aValues,
                            'label' => $label
                        );
                    }
                }
            }
            // Valor por defecto para los filtros
            if (isset($templates[$key][2])) {
                foreach ($templates[$key][2] as $key1 => $value) {
                    if (isset($templates[$key][1][$key1])) {
                        if ($templates[$key][1][$key1][0] == 'date') {
                            $filters_defaults[$key][$key1]['aValues'] = $value;

                            $date_from = new Carbon($value[1]);
                            $date_to = new Carbon($value[2]);
                            $label = config('constants.date_type.' . $value[0]);
                            $filters_defaults[$key][$key1]['label'] = [$label . ' de ' . $date_from->format('d-m-Y') . ' a ' . $date_to->format('d-m-Y')];
                        }
                    }
                }
            }

            $result = [
                'flist' => $flist,
                'ufilters' => $aUFilters[$key],
                'filters' => $filters,
                'filters_defaults' => $filters_defaults,
            ];
            return $result;
        } else {
            return []; // Se ha pasado una clave de filtro vacía
        }
    }
    /**
     * Save user filters.
     *
     * @return mixed aResult(iResult, vcMessage)
     *
     */
    public static function saveFilters($key, $filters)
    {
        $oUser = User::find(Auth::user()->id);
        $aUFilters = $oUser->filters;
        $templates = config('filters');
        $aClean = array();

        if (!empty($filters) && !empty($key)) {
            foreach ($filters as $key1 => $value) {
                if ($key1 != '_token' && $key1 != 'page' && $key1 != 'signature') {
                    //$key = substr($key,strpos($key,'_')+1);
                    $aClean[$key1] = $value;
                }
            }
            $aDif = array_diff_key($templates[$key][0], $aClean);
            $aMerge = array_merge($aClean, $aDif);
            $aUFilters[$key] = $aMerge;
            $oUser->filters = $aUFilters;
            $oUser->save();
            return ['iResult' => $oUser->id];
        } else {
            return ['iResult' => -1, 'vcMessage' => 'Clave o valores de filtro vacíos'];
        }
    }
    /**
     * Reset user filters.
     *
     * @param int $iUsers_id
     * @param array $aAttributes array (attribute => value)
     * @return mixed aResult(iResult, vcMessage)
     *
     */
    public static function resetFilters($ids = [], $prefix = '')
    {
        $aResult = ['iResult' => 0, 'vcMessage' => ''];
        if (empty($prefix)) {
            User::where('id', '>', 0)
                ->when(!empty($ids), function ($query) use ($ids) {
                    return $query->whereIn('id', $ids);
                })
                ->update(['filters' => null]);
        } else {
            if (empty($ids)) {
                $ids = User::all()->keyBy('id')->keys()->all();
            }
            foreach ($ids as $id) {
                $user = User::find($id);
                $filters = $user->filters;
                if (!empty($filters)) {
                    $filters = array_filter($filters, function ($key) use ($prefix) {
                        return !str_starts_with($key, $prefix);
                    }, ARRAY_FILTER_USE_KEY);
                    User::where('id', $id)->update(['filters' => $filters]);
                }
            }
        }
        return $aResult;
    }
    public function roles() {
        return $this->belongsToMany(Role::class, ModelHasRole::class, 'model_id', 'role_id')
            ->where('model_has_roles.model_type', User::class);
    }
    public function level() {
        return $this->hasOne(Level::class, 'id', 'level_id');
    }
    public function menus() {
        return $this->hasManyThrough(Permission::class, ModelHasPermission::class, 'model_id', 'id', 'id' , 'permission_id')
            ->join('menus', 'menus.id', 'permissions.model_id')
            ->where('model_has_permissions.model_type', User::class)
            ->where('permissions.model', Menu::class)
            ->with('menu');
    }
    public function submenus() {
        return $this->hasManyThrough(Permission::class, ModelHasPermission::class, 'model_id', 'id', 'id' , 'permission_id')
            ->join('menus', 'menus.id', 'permissions.model_id')
            ->where('model_has_permissions.model_type', User::class)
            ->where('permissions.model', Menu::class)
            ->whereColumn('menus.id', '<>', 'menus.pmenus_id')
            ->with('menu');
    }
    public function fsubmenus() {
        return $this->hasManyThrough(Permission::class, ModelHasPermission::class, 'model_id', 'id', 'id' , 'permission_id')
            ->join('menus', 'menus.id', 'permissions.model_id')
            ->where('model_has_permissions.model_type', User::class)
            ->where('model_has_permissions.favorite', 1)
            ->where('permissions.model', Menu::class)
            ->whereColumn('menus.id', '<>', 'menus.pmenus_id')
            ->with('menu');
    }

    /**
	 * Overload model save.
	 */
    public function save (array $options = array(), $do_log = true)
    {

        parent::save($options); // Calls Default Save
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordMail($token));
    }

    /**
     * A model may have multiple direct permissions.
     */
    public function permissions(): BelongsToMany
    {
        $relation = $this->morphToMany(
            config('permission.models.permission'),
            'model',
            config('permission.table_names.model_has_permissions'),
            config('permission.column_names.model_morph_key'),
            app(PermissionRegistrar::class)->pivotPermission
        )->withPivot('favorite');

        if (! app(PermissionRegistrar::class)->teams) {
            return $relation;
        }

        return $relation->wherePivot(app(PermissionRegistrar::class)->teamsKey, getPermissionsTeamId());
    }

    public function companies()
    {
        return $this->belongsToMany(Company::class);
    }
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
