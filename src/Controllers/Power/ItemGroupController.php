<?php

/*
 * 权限项组
 */

namespace Laravel\Crbac\Controllers\Power;

use Laravel\Crbac\Models\Power\ItemGroup;
use Laravel\Crbac\Controllers\Controller;

class ItemGroupController extends Controller {

    //备注说明
    protected $description = '权限项组';

    /**
     * 编辑权限项组数据
     * @param ItemGroup $item
     * @return mixed
     * @methods(GET,POST)
     */
    public function edit(ItemGroup $item = null) {
        return $this->modelEdit($item, 'power.item.group.edit', ItemGroup::class);
    }

    /**
     * 删除权限项组数据
     * @param ItemGroup $item
     * @return mixed
     * @methods(GET)
     */
    public function delete(ItemGroup $item) {
        if ($item->items()->count()) {
            return prompt($this->description . '已经关联权限项无法删除！', 'error', -1);
        }
        return $this->modelDelete($item);
    }

    /**
     * 权限项组选择
     * @param string $relation
     * @return view
     * @methods(GET)
     */
    public function select(string $relation) {
        $where = ['name' => 'like'];
        $order = ['created' => 'created_at'];
        $default = ['order' => 'created', 'by' => 'desc'];
        list($lists, $toOrder) = $this->listsSelect(ItemGroup::class, $where, $order, $default);
        return view('power.item.group.select', compact('lists', 'toOrder', 'relation'));
    }

    /**
     * 权限组列表
     * @return view
     * @methods(GET)
     */
    public function lists() {
        $where = [
            'name' => 'like',
        ];
        $order = ['created' => 'created_at'];
        $default = ['order' => 'created', 'by' => 'desc'];
        list($lists, $toOrder) = $this->listsSelect(ItemGroup::class, $where, $order, $default);
        $description = $this->description;
        return view('power.item.group.lists', compact('lists', 'description', 'toOrder'));
    }

}
