<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\WithExtensions;
use App\Traits\HasTranslations;
use Illuminate\Support\Facades\DB;

class Menu extends Model
{
    use HasFactory;
    use WithExtensions;
    use HasTranslations;

    public $translatable = ['description'];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Get menus
     *
     * @param int $iUsers_id
     * @param int $records_in_page
     * @param array $aSort (attribute => 'asc'/'desc')
     * @param int $bFavorites
     * @param int $iTownHalls_id
     * @param int $iLevel
     * @return mixed Collection
     *
     */
    public static function emtGet(
        int $iModels_id=0,
        int $records_in_page = 0,
        array $aSort = [],
        array $aFilters = [],
        array $aWith = []
    ) {

        $oQuery = Menu::select('menus.*')
        ->when($iModels_id>0, function($query) use ($iModels_id) {
            return $query->where('menus.id', $iModels_id);
        })
        ->when(isset($aFilters['iLevel']) && $aFilters['iLevel']>0, function($query) use ($aFilters) {
            return $query->where('menus.level', '<=', $aFilters['iLevel']);
        })
        // ->when(isset($aFilters['description']) && !empty($aFilters['description']), function($query) use ($aFilters) {
        //     $query->whereRaw('lower(menus.description->"$.'.app()->getLocale().'") COLLATE utf8mb4_unicode_ci like "%'.strtolower($aFilters['description']).'%"');
        // })
        ->when(isset($aFilters['iUsers_id']) && $aFilters['iUsers_id']>0, function($query) use ($aFilters) {
            $user = User::find($aFilters['iUsers_id']);

            $menus_ids = $user->getAllPermissions()->where('model', static::class)
            ->when($aFilters['bFavorites'] == 1, function($coll) {
                return $coll->where('pivot.favorite', 1);
            })
            ->when(isset($aFilters['iLevel']) && $aFilters['iLevel']>0, function($coll) use ($aFilters) {
                return $coll->where('level', '<=', $aFilters['iLevel']);
            })
            ->keyBy('model_id')->keys()->all();

            $query->whereIn('menus.id', $menus_ids);
            $query2 = Menu::select('menus.*')
            ->whereIn('menus.id', function($query) use ($menus_ids) {
                $query->select('menus.pmenus_id')
                      ->from('menus')
                      ->whereIn('menus.id', $menus_ids);
            })
            ->union($query);
            return $query2;
        })
        ;

        if (!isset($aFilters['iUsers_id'])) {
            foreach ($aSort as $key => $value) {
                $oQuery->orderBy($key, $value);
            }
        }
        //$oQuery->dd();
        $records = static::getModelData($oQuery, $iModels_id, $records_in_page, $aWith);
        if (isset($aFilters['iUsers_id']) && $aFilters['iUsers_id']>0) {
            $records->sortBy($aSort);
        }
        return $records;
    }

    /**
     * Get user menus
     *
     * @param int $iUsers_id
     * @param int $bFavorites
     * @param int $iTownHalls_id
     * @param int $iLevel
     * @return mixed Collection
     *
     */
    public static function emtGetUser(
        int $iUsers_id=0,
        int $bFavorites = 0,
        int $iLevel = 0
    ) {

        $oMenus = static::emtGet(0,-1,
            [
                'menus.order' => 'asc',
                'menus.deep' => 'asc',
                'menus.description' => 'asc',
            ],
            [
                'iUsers_id' => $iUsers_id,
                'bFavorites' => $bFavorites,
                'iLevel' => $iLevel,
            ],
            ['permission']
        );

        $aMenus = array();
        if ($oMenus->count()>0) {
            foreach ($oMenus as $oMenu) {
                if($oMenu->deep==1){
                    if (isset($aMenus[$oMenu->id]['submenu'])) {
                        $aMenus[$oMenu->id] = array_merge($oMenu->attributes,$aMenus[$oMenu->id]);
                    } else {
                        $aMenus[$oMenu->id] = $oMenu->attributes;
                        $aMenus[$oMenu->id]['submenu'] = array();
                        $aMenus[$oMenu->id]['permission'] = $oMenu->permission;
                    }
                }
                if($oMenu->deep==2){
                    if (isset($aMenus[$oMenu->pmenus_id]['submenu'][$oMenu->id])) {
                        $aMenus[$oMenu->pmenus_id]['submenu'][$oMenu->id] = array_merge($oMenu->attributes,$aMenus[$oMenu->pmenus_id]);
                    } else {
                        $aMenus[$oMenu->pmenus_id]['submenu'][$oMenu->id] = $oMenu->attributes;
                        $aMenus[$oMenu->pmenus_id]['submenu'][$oMenu->id]['submenu'] = array();
                        $aMenus[$oMenu->pmenus_id]['submenu'][$oMenu->id]['permission'] =  $oMenu->permission;
                    }
                }
                // if($oMenu->deep==3){
                //     $aMenus[$oMenu->pmenus_id]['submenu'][$oMenu->pmenus_id]['submenu'][$oMenu->id] = $oMenu->attributes;
                //     $aMenus[$oMenu->pmenus_id]['submenu'][$oMenu->pmenus_id]['submenu'][$oMenu->id]['submenu'] = array();
                // }
            }
        }
        $aMenusResult = [];
        foreach ($aMenus as $index => $menu) {
            $aMenusResult[$index] = $menu;
            $aMenusResult[$index]['submenu'] = collect($menu['submenu'])->sortBy('order');
        }
        return collect($aMenusResult)->sortBy('order');
    }

    public function submenus()
    {
        return $this->hasMany(Menu::class, 'pmenus_id', 'id')->whereColumn('menus.pmenus_id', '<>', 'menus.id');
    }
    public function permission()
    {
        return $this->hasOne(Permission::class, 'model_id', 'id')
            ->where('permissions.model', Menu::class);
    }

    public static function getUsers($ids, $favorites=false)
    {
        $users = User::select('users.*', 'menus.id as menu_id')
        ->join('model_has_permissions', 'model_has_permissions.model_id', 'users.id')
        ->join('permissions', 'permissions.id', 'model_has_permissions.permission_id')
        ->join('menus', 'menus.id', 'permissions.model_id')
        ->where('permissions.model', Menu::class)
        ->when($favorites, function ($query) {
            $query->where('model_has_permissions.favorite', 1);
        })
        ->whereIn('menus.id', $ids)
        ->get();

        $grouped = $users->groupBy('menu_id');

        return $grouped;
    }
    public static function getGroups($ids, $favorites=false)
    {
        $groups = Role::select('roles.*', 'menus.id as menu_id')
        ->join('role_has_permissions', 'role_has_permissions.role_id', 'roles.id')
        ->join('permissions', 'permissions.id', 'role_has_permissions.permission_id')
        ->join('menus', 'menus.id', 'permissions.model_id')
        ->where('permissions.model', Menu::class)
        ->when($favorites, function ($query) {
            $query->where('role_has_permissions.favorite', 1);
        })
        ->whereIn('menus.id', $ids)
        ->get();

        $grouped = $groups->groupBy('menu_id');

        return $grouped;
    }
}
