<?php

/*
 * 角色管理
 */

namespace Laravel\Crbac\Services\Power;

use Illuminate\Support\Facades\DB;
use Laravel\Crbac\Models\Power\Item;
use Laravel\Crbac\Models\Power\RoleItem;
use Laravel\Crbac\Models\Power\Role as RoleModel;

class Role extends Service {

    /**
     * 修改权限项
     * @param RoleModel $role
     */
    public function editItems(RoleModel $role) {
        $items = array_map('intval', (array) request('items'));
        $itemIds = $role->items->modelKeys();
        $removeIds = array_diff($itemIds, $items); //不需要的
        $insertIds = array_diff($items, $itemIds); //未添加的
        if (!isPower('all_power_items')) {//是否允许修改所有权限
            $allowIds = Item::items(auth()->user())->modelKeys();
            $removeIds = array_intersect($removeIds, $allowIds);
            $insertIds = array_intersect($insertIds, $allowIds);
        }
        try {
            DB::beginTransaction();
            if ($removeIds) {
                RoleItem::where('power_role_id', '=', $role->getKey())
                        ->whereIn('power_item_id', $removeIds)
                        ->delete();
            }
            if ($insertIds) {//插入处理
                $insertIds = Item::whereIn('id', $insertIds)
                        ->get(['id'])
                        ->pluck('id')
                        ->toArray();
                $inserts = array_map(function($itemId)use($role) {
                    return ['power_role_id' => $role->getKey(), 'power_item_id' => $itemId];
                }, $insertIds);
                RoleItem::insert($inserts);
            }
            DB::commit();
        } catch (\Exception $err) {
            DB::rollBack();
            throw $err;
        }
    }

}
