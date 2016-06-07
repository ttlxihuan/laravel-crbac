<?php

/*
 * 权限项组
 */

namespace XiHuan\Crbac\Controllers\Power;

use XiHuan\Crbac\Models\Power\ItemGroup;
use XiHuan\Crbac\Controllers\Controller;

class ItemGroupController extends Controller {

    //备注说明
    protected $description = '权限项组';

    /*
     * 作用：编辑权限项组数据
     * 参数：$item XiHuan\Crbac\Models\Power\ItemGroup 需要编辑的数据，默认为添加
     * 返回值：view|array
     */
    public function edit(ItemGroup $item = null) {
        return $this->modelEdit($item, 'power.item.group.edit', ItemGroup::class);
    }
    /*
     * 作用：删除权限项组数据
     * 参数：$item XiHuan\Crbac\Models\Power\ItemGroup 需要删除的数据
     * 返回值：view|array
     */
    public function delete($item) {
        if ($item->items()->count()) {
            return prompt($this->description . '已经关联权限项无法删除！', 'error', -1);
        }
        return parent::delete($item);
    }
    /*
     * 作用：快捷选择列表
     * 参数：无
     * 返回值：view
     */
    public function select() {
        $where = ['name' => 'like'];
        $order = ['created' => 'created_at'];
        $default = ['order' => 'created', 'by' => 'desc'];
        list($lists, $toOrder) = $this->listsSelect(ItemGroup::class, $where, $order, $default);
        return view('power.item.group.select', compact('lists', 'toOrder'));
    }
    /*
     * 作用：权限组列表
     * 参数：无
     * 返回值：view
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
