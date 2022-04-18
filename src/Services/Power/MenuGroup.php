<?php

/*
 * 菜单组管理
 */

namespace Laravel\Crbac\Services\Power;

use Illuminate\Support\Facades\DB;
use Laravel\Crbac\Models\Power\MenuLevel;
use Laravel\Crbac\Models\Power\MenuGroup as MenuGroupModel;

class MenuGroup extends Service {

    /**
     * 编辑菜单关系处理
     * @param MenuGroupModel $item
     */
    public function editMenuLevel(MenuGroupModel $item) {
        $levels = array_values((array) request('level'));
        $delete = MenuLevel::where('power_menu_group_id', '=', $item->getKey()); //删除数据
        $useIds = [];
        try {
            DB::beginTransaction();
            $parent_ids = $this->singleLevelMenu($item, array_map('array_pop', array_shift($levels)), 0); //处理第一级
            foreach ($parent_ids as $menu_id => $level_id) {
                $useIds = array_merge($useIds, $this->levelMenu($item, $levels, $menu_id, $level_id)); //下级处理
            }
            $useIds = array_merge($useIds, $parent_ids);
            if ($useIds) {//去掉已经使用的ID
                $delete->whereNotIn('id', $useIds);
            }
            $delete->delete(); //删除不要的数据
            DB::commit();
        } catch (\Exception $err) {
            DB::rollBack();
            throw $err;
        }
    }

    /**
     * 处理单层下菜单
     * @param MenuGroupModel $item
     * @param array $menus
     * @param int $parent_id
     * @return array
     */
    protected function singleLevelMenu(MenuGroupModel $item, array $menus, $parent_id) {
        if (!count($menus)) {
            return [];
        }
        $exists = MenuLevel::where('power_menu_group_id', '=', $item->getKey())
                ->where('parent_id', '=', $parent_id); //存在的数据
        $ids = [];
        foreach ($menus as $menuId) {
            $ids[] = $menuId;
            $inserts[$menuId] = [
                'power_menu_id' => $menuId,
                'power_menu_group_id' => $item->getKey(),
                'parent_id' => $parent_id,
            ];
        }
        if (!count($ids)) {//没有数据
            return [];
        }
        $exists->whereIn('power_menu_id', $ids);
        $lists = array_pluck($exists->get(['power_menu_id', 'id']), 'id', 'power_menu_id');
        $inserts = array_diff_key($inserts, $lists);
        MenuLevel::insert($inserts); //每层写一次
        //修改排序值
        $max = count($ids);
        foreach ($ids as $id) {
            $exists = MenuLevel::where('power_menu_group_id', '=', $item->getKey())
                    ->where('parent_id', '=', $parent_id)
                    ->where('power_menu_id', '=', $id)
                    ->update(['sort' => $max--]);
        }
        $result = MenuLevel::where('power_menu_group_id', '=', $item->getKey())
                ->where('parent_id', '=', $parent_id)
                ->whereIn('power_menu_id', $ids)
                ->get(['power_menu_id', 'id']); //添加成功数据
        return array_pluck($result, 'id', 'power_menu_id');
    }

    /**
     * 递归层级处理
     * @param MenuGroupModel $item
     * @param array $_levels
     * @param int $menu_id
     * @param int $parent_id
     * @return array
     */
    protected function levelMenu(MenuGroupModel $item, $_levels, $menu_id, $parent_id) {
        $levels = array_shift($_levels);
        $parent_ids = $this->singleLevelMenu($item, array_get($levels, $menu_id, []), $parent_id); //处理第一级
        $child_ids = [];
        foreach ($parent_ids as $menu_id => $level_id) {
            $child_ids = array_merge($child_ids, $this->levelMenu($item, $_levels, $menu_id, $level_id)); //下级处理
        }
        return array_merge($parent_ids, $child_ids);
    }

}
