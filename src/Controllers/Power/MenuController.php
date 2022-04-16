<?php

/*
 * 菜单管理
 */

namespace Laravel\Crbac\Controllers\Power;

use Laravel\Crbac\Models\Power\Menu;
use Laravel\Crbac\Models\Power\Route;
use Illuminate\Support\Facades\Request;
use Laravel\Crbac\Controllers\Controller;

class MenuController extends Controller {

    //备注说明
    protected $description = '菜单';

    /**
     * 编辑菜单数据
     * @param Menu $item
     * @return mixed
     * @methods(GET,POST)
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

    /**
     * 删除菜单数据
     * @param Menu $item
     * @return mixed
     * @methods(GET)
     */
    public function delete(Menu $item) {
        if ($item->groups()->count()) {
            return prompt($this->description . '已经在使用中，无法删除！', 'error', -1);
        }
        return $this->modelDelete($item);
    }

    /**
     * 菜单列表
     * @return view
     * @methods(GET)
     */
    public function lists() {
        $where = ['name' => 'like', 'status',];
        $order = ['created' => 'created_at'];
        $default = ['order' => 'created', 'by' => 'desc'];
        list($lists, $toOrder) = $this->listsSelect(Menu::class, $where, $order, $default);
        $description = $this->description;
        return view('power.menu.lists', compact('lists', 'description', 'toOrder'));
    }

    /**
     * 菜单快捷选择列表
     * @param string $relation
     * @return view
     * @methods(GET)
     */
    public function select(string $relation) {
        $where = ['name' => 'like',];
        $order = ['created' => 'created_at'];
        $default = ['order' => 'created', 'by' => 'desc'];
        list($lists, $toOrder) = $this->listsSelect(Menu::class, $where, $order, $default);
        return view('power.menu.select', compact('lists', 'toOrder', 'relation'));
    }

}
