<?php

/*
 * 菜单管理
 */

namespace XiHuan\Crbac\Services\Power;

use Input;
use XiHuan\Crbac\Models\Power\MenuLevel;
use XiHuan\Crbac\Models\Power\Menu as MenuModel;
use XiHuan\Crbac\Models\Power\Item as ItemModel;
use XiHuan\Crbac\Services\Service as BaseService;

class Menu extends Service {
    /*
     * 作用：修改数据前处理
     * 参数：$data array 要修改的数据
     *       $service XiHuan\Crbac\Services\Service 编辑处理service
     *       $item Model|string 要编辑的Model或Model类名
     * 返回值：bool
     */
    protected function editBefore(&$data, BaseService $service, $item) {
        $code = Input::get('code');
        if (!$code) {
            return;
        }
        $power = $item instanceof MenuModel && $item->item ? $item->item : null;
        if (empty($power)) {//如果没有权限项，尝试从权限码获取权限对象
            $power = ItemModel::findCode($code);
        }
        //添加权限项
        $result = (new Item($this))->edit($power ? : ItemModel::class);
        if ($result) {
            $data['power_item_id'] = $result->getKey();
        }
        return $result;
    }
    /*
     * 作用：修改数据后处理
     * 参数：$result null|Model 修改的数据的结果
     *       $service XiHuan\Crbac\Services\Service 编辑处理service
     * 返回值：void
     */
    protected function editAfter($result, BaseService $service) {
        //整理菜单组数据
        $groups = (array) Input::get('group');
        $delete = MenuLevel::where($result->getKeyName(), '=', $result->getKey());
        if (!count($groups)) {//删除所有关联数据
            $delete->delete();
            return;
        }
        $noeLevelIds = [];
        $parentMenu = MenuLevel::select('*'); //上级菜单
        $selfMenu = MenuLevel::where('power_menu_id', '=', $result->getKey()); //自己菜单
        $whereCallback = [];
        foreach ($groups as $item) {
            $item = array_filter(array_values($item), function($v) {//过滤处理
                return $v > 0;
            });
            if (!count($item)) {
                continue;
            }
            if (count($item) == 1) {
                $noeLevelIds[$item[0]] = $item[0]; //自己为第一级菜单
            }
            $parentMenu->orWhere(function($query)use($item, $result) {
                $query->where('power_menu_group_id', $item[0]);
                if (count($item) > 1) {//
                    $query->where('id', $item[count($item) - 1]); //有上级
                } else {//没有上级，自己就是上级
                    $query->where('parent_id', 0)
                            ->where('power_menu_id', '=', $result->getKey()); //无上级
                }
            });
            $whereCallback[] = function($query)use($item) {
                $query->where('power_menu_group_id', $item[0])
                        ->where('parent_id', count($item) > 1 ? $item[count($item) - 1] : 0);
            };
        }
        $parentLists = []; //需要的上级
        $selfLists = [];
        if ($whereCallback) {
            $parentLists = $parentMenu->get(); //需要的上级
            $selfLists = $selfMenu->where(function($query)use($whereCallback) {
                        array_map(function($callback)use($query) {
                            $query->orWhere($callback);
                        }, $whereCallback);
                    })->get(['id', 'power_menu_group_id', 'parent_id']); //已经存在的
        }
        $inserts = []; //要写入的数据
        if ($selfLists) {
            $delete->whereNotIn('id', array_pluck($selfLists, 'id')); //删除无需关联数据
        }
        //删除关联前，需要删除所在当前层级下所有关联数据
        foreach ($delete->get() as $level) {
            $this->deleteLevel($level->power_menu_group_id, [$level->getKey()]);
        }
        $delete->delete();
        $self_parents = array_pluck($selfLists, 'parent_id');
        foreach ($parentLists as $level) {
            if ($level->parent_id == 0 && isset($noeLevelIds[$level->power_menu_group_id]) && $level->power_menu_id == $result->getKey()) {//自己为第一级，并且存在的
                unset($noeLevelIds[$level->power_menu_group_id]);
                continue;
            }
            if (in_array($level->getKey(), $self_parents)) {//存在这个
                continue;
            }
            //新添加
            $inserts[] = [
                'power_menu_id' => $result->getKey(),
                'power_menu_group_id' => $level->power_menu_group_id,
                'parent_id' => $level->getKey(),
            ];
        }
        if ($noeLevelIds) {//未添加的第一级菜单
            $self_groups = array_pluck($selfLists, 'power_menu_group_id', 'parent_id'); //取出已经存在的关联
            foreach ($noeLevelIds as $id) {
                if ((isset($self_groups[$id]) && $self_groups[$id] == 0)) {//存在不创建
                    continue;
                }
                //新添加
                $inserts[] = [
                    'power_menu_id' => $result->getKey(),
                    'power_menu_group_id' => $id,
                    'parent_id' => '0',
                ];
            }
        }
        MenuLevel::insert($inserts);
    }
    /*
     * 作用：删除指定层级下所有关联数据
     * 参数：$group_id int 菜单组ID
     *      $parent_id array 上级ID集
     * 返回值：void
     */
    protected function deleteLevel($group_id, array $parent_id) {
        $level = MenuLevel::whereIn('parent_id', $parent_id)
                ->where('power_menu_group_id', '=', $group_id);
        $lists = $level->get(['id']);
        if ($lists->count()) {//递归删除
            $this->deleteLevel($group_id, array_pluck($lists, 'id'));
        }
        $level->delete();
    }
}
