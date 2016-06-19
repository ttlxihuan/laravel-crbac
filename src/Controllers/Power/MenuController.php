<?php

/*
 * 菜单管理
 */

namespace XiHuan\Crbac\Controllers\Power;

use Request;
use XiHuan\Crbac\Models\Power\Menu;
use XiHuan\Crbac\Models\Power\Route;
use XiHuan\Crbac\Controllers\Controller;

class MenuController extends Controller {

    //备注说明
    protected $description = '菜单';

    /*
     * 作用：编辑菜单数据
     * 参数：$item XiHuan\Crbac\Models\Power\Menu 需要编辑的数据，默认为添加
     * 返回值：view|array
     */
    public function edit(Menu $item = null) {
        $result = $this->modelEdit($item, 'power.menu.edit', Menu::class);
        if (!$item && !Request::isMethod('post')) {
            $route_id = (int) Request::input('route');
            $route = $route_id ? Route::find($route_id) : null;
            $result->with('route', $route);
        }
        return $result;
    }
    /*
     * 作用：删除菜单数据
     * 参数：$item XiHuan\Crbac\Models\Power\Menu 需要删除的数据
     * 返回值：view|array
     */
    public function delete($item) {
        if ($item->groups()->count()) {
            return prompt($this->description . '已经在使用中，无法删除！', 'error', -1);
        }
        return parent::delete($item);
    }
    /*
     * 作用：菜单列表
     * 参数：无
     * 返回值：view
     */
    public function lists() {
        $where = ['name' => 'like', 'status',];
        $order = ['created' => 'created_at'];
        $default = ['order' => 'created', 'by' => 'desc'];
        list($lists, $toOrder) = $this->listsSelect(Menu::class, $where, $order, $default);
        $description = $this->description;
        return view('power.menu.lists', compact('lists', 'description', 'toOrder'));
    }
    /*
     * 作用：快捷选择列表
     * 参数：无
     * 返回值：view
     */
    public function select() {
        $where = ['name' => 'like',];
        $order = ['created' => 'created_at'];
        $default = ['order' => 'created', 'by' => 'desc'];
        list($lists, $toOrder) = $this->listsSelect(Menu::class, $where, $order, $default);
        return view('power.menu.select', compact('lists', 'toOrder'));
    }
}
