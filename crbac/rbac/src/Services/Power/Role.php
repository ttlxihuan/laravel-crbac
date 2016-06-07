<?php

/*
 * 角色管理
 */

namespace XiHuan\Crbac\Services\Power;

use Input;
use XiHuan\Crbac\Models\Power\Item;
use XiHuan\Crbac\Models\Power\RoleItem;
use XiHuan\Crbac\Models\Power\Role as RoleModel;

class Role extends Service {
    /*
     * 作用：修改权限项
     * 参数：$role XiHuan\Crbac\Models\Power\Role 角色
     * 返回值：void
     */
    public function editItems(RoleModel $role) {
        $items = array_map('intval', (array) Input::get('items'));
        $itemIds = $role->items->modelKeys();
        $removeIds = array_diff($itemIds, $items); //不需要的
        $insertIds = array_diff($items, $itemIds); //未添加的
        if (!isPower('all_power_items')) {//是否允许修改所有权限
            $allowIds = Crbac::items(auth()->user())->modelKeys();
            $removeIds = array_intersect($removeIds, $allowIds);
            $insertIds = array_intersect($insertIds, $allowIds);
        }
        if ($removeIds) {
            RoleItem::where('power_role_id', '=', $role->getKey())
                    ->whereIn('power_item_id', $removeIds)
                    ->delete();
        }
        if ($insertIds) {//插入处理
            $insertIds = array_pluck(Item::whereIn('power_item_id', $insertIds)
                            ->get(['power_item_id']), 'power_item_id');
            $inserts = array_map(function($itemId)use($role) {
                return ['power_role_id' => $role->getKey(), 'power_item_id' => $itemId];
            }, $insertIds);
            RoleItem::insert($inserts);
        }
    }
}
