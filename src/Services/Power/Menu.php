<?php

/*
 * 菜单管理
 */

namespace Laravel\Crbac\Services\Power;

use Laravel\Crbac\Models\Model;
use Laravel\Crbac\Models\Power\MenuGroup;
use Laravel\Crbac\Models\Power\MenuLevel;
use Laravel\Crbac\Models\Power\Menu as MenuModel;
use Laravel\Crbac\Models\Power\Item as ItemModel;
use Laravel\Crbac\Services\Service as BaseService;

class Menu extends Service {

    /**
     * 修改数据前处理，菜单权限项处理
     * @param array $data
     * @param BaseService $service
     * @param MenuModel $item
     * @return mixed
     */
    protected function editBefore(&$data, BaseService $service, $item) {
        $code = request('code');
        if (!$code) {
            return;
        }
        $power = $item instanceof MenuModel && $item->item ? $item->item : null;
        if (empty($power)) {//如果没有权限项，尝试从权限码获取权限对象
            $power = ItemModel::findCode($code);
        }
        //添加权限项
        $result = (new Item($this))->edit($power ?: ItemModel::class);
        if ($result) {
            $data['power_item_id'] = $result->getKey();
        }
        return $result;
    }

    /**
     * 修改数据后处理，菜单组合处理
     * @param Model $result
     * @param BaseService $service
     * @return void
     */
    protected function editAfter(Model $result, BaseService $service) {
        /*
         * 菜单层级编辑细节
         * 1、整理验证数据
         * 2、按数据源提取匹配层级菜单
         * 3、匹配可修改的层级，当前菜单上层链路全部修改
         * 4、未匹配的直接创建，创建整个层级数据
         * 5、无法匹配的直接删除，当前菜单及下层链路全部删除
         */
        $groups = [];
        $menuGroupIds = [];
        $levelIds = [];
        // 整理数据
        foreach ((array) request('group') as $items) {
            $level = [];
            foreach ((array) $items as $item) {
                if (!filter_var($item, FILTER_VALIDATE_INT) || $item <= 0) {
                    break;
                }
                $level[] = $item;
            }
            if (count($level)) {
                $menuGroupIds[] = $level[0];
                $levelIds = array_merge($levelIds, $level);
                $groups[] = $level;
            }
        }
        $exist_ids = [];
        $builder = MenuLevel::where('power_menu_id', $id = $result->getKey());
        if (count($groups)) {
            // 验证菜单组是否存在
            if (count(array_diff($menuGroupIds, array_column(MenuGroup::whereIn('id', array_unique($menuGroupIds))->get(['id'])->toArray(), 'id')))) {
                return $this->setError('validator', '菜单组配置的菜单组数不存在');
            }
            // 验证菜单是否存在
            $levels = [];
            foreach (MenuLevel::whereIn('id', array_unique($levelIds))->get(['id', 'parent_id', 'power_menu_group_id'])->toArray() as $item) {
                $levels[$item['id']] = $item;
            }
            if (count(array_diff($levelIds, array_keys($levels)))) {
                return $this->setError('validator', '菜单组配置的菜单层级数据不存在');
            }
            foreach (array_unique($groups, SORT_REGULAR) as $items) {
                $groupId = array_shift($items);
                $parent_id = 0;
                foreach ($items as $item) {
                    if ($levels[$item]['power_menu_group_id'] != $groupId || $levels[$item]['parent_id'] != $parent_id) {
                        return $this->setError('validator', '菜单组配置的菜单层级数据错误');
                    }
                    $parent_id = $item;
                }
                $level_id = MenuLevel::where('power_menu_group_id', $groupId)
                        ->where('power_menu_id', $id)
                        ->where('parent_id', $parent_id)
                        ->value('id');
                if (!$level_id) {
                    // 没有就创建
                    $level_id = MenuLevel::insertGetId([
                                'power_menu_id' => $id,
                                'power_menu_group_id' => $groupId,
                                'parent_id' => $parent_id,
                                'sort' => 0,
                    ]);
                }
                // 记录需要的层级ID集
                $exist_ids[] = $level_id;
            }
            $builder->whereNotIn('id', $exist_ids);
        }
        $groups = [];
        foreach ($builder->get(['power_menu_group_id', 'id']) as $level) {
            $groups[$level['power_menu_group_id']][] = $level['id'];
        }
        foreach ($groups as $group_id => $parent_ids) {
            $this->deleteLevel($group_id, $parent_ids);
            MenuLevel::where('id', $parent_ids)->delete();
        }
    }

    /**
     * 删除指定层级下所有关联数据
     * @param int $group_id
     * @param array $parent_ids
     */
    protected function deleteLevel($group_id, array $parent_ids) {
        // 当前菜单及下级所有菜单层级都删除
        $level = MenuLevel::whereIn('parent_id', $parent_ids)
                ->where('power_menu_group_id', '=', $group_id);
        $lists = $level->get(['id']);
        if ($lists->count()) {//递归删除
            $this->deleteLevel($group_id, array_pluck($lists, 'id'));
        }
        $level->delete();
    }

}
