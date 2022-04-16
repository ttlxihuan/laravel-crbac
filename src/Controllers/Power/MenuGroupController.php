<?php

/*
 * 菜单组
 */

namespace Laravel\Crbac\Controllers\Power;

use Closure;
use Laravel\Crbac\Models\Power\Menu;
use Illuminate\Support\Facades\Request;
use Laravel\Crbac\Models\Power\MenuGroup;
use Laravel\Crbac\Controllers\Controller;
use Laravel\Crbac\Services\Power\MenuGroup as MenuGroupService;

class MenuGroupController extends Controller {

    //备注说明
    protected $description = '菜单组';

    /**
     * 编辑菜单组数据
     * @param MenuGroup $item
     * @return mixed
     * @methods(GET,POST)
     */
    public function edit(MenuGroup $item = null) {
        return $this->modelEdit($item, 'power.menu.group.edit', MenuGroup::class);
    }

    /**
     * 获取菜单下级列表
     * @param MenuGroup $item
     * @return mixed
     * @methods(GET)
     */
    public function levelOption(MenuGroup $item) {
        $parent_id = (int) request('parent_id');
        return prompt([
            'options' => $item->menus()->where('parent_id', '=', $parent_id)->get(['power_menu_level.id', 'power_menu.name'])
        ]);
    }

    /**
     * 删除菜单组数据
     * @param MenuGroup $item
     * @return mixed
     * @methods(GET)
     */
    public function delete(MenuGroup $item) {
        if ($item->menus()->count() || $item->admins()->count()) {
            return prompt($this->description . '已经在使用中，无法删除！', 'error', -1);
        }
        return $this->modelDelete($item);
    }

    /**
     * 编辑菜单组中菜单层级
     * @param MenuGroup $item
     * @return mixed
     * @methods(GET,POST)
     */
    public function menus(MenuGroup $item) {
        if (!Request::isMethod('post')) {
            $title = '菜单层级编辑';
            $parent_id = '0';
            $level = '1';
            $menu_lists = Menu::group($item->getKey());
            $lists = $menu_lists->groupBy('parent_id');
            $level_lists = $menu_lists->pluck('id', 'level_id');
            return view('power.menu.group.level', compact('lists', 'title', 'parent_id', 'level', 'level_lists'))
                            ->with('menuGroup', $item);
        }
        $service = new MenuGroupService();
        $service->editMenuLevel($item);
        return $service->prompt('菜单层级编辑成功');
    }

    /**
     * 菜单组列表
     * @return view
     * @methods(GET)
     */
    public function lists() {
        return $this->_lists();
    }

    /**
     * 快捷选择列表
     * @param string $relation
     * @return view
     * @methods(GET)
     */
    public function select(string $relation) {
        $where = ['name' => 'like',];
        $order = ['created' => 'created_at'];
        $default = ['order' => 'created', 'by' => 'desc'];
        list($lists, $toOrder) = $this->listsSelect(MenuGroup::class, $where, $order, $default);
        return view('power.menu.group.select', compact('lists', 'toOrder', 'relation'));
    }

    /**
     * 复制菜单组列表
     * @param MenuGroup $item
     * @return view
     * @methods(GET)
     */
    public function copy(MenuGroup $item) {
        return $this->_lists(function($builder)use($item) {
                    $builder->where($item->getKeyName(), '!=', $item->getKey());
                })->with(['copy' => $item, 'title' => '复制菜单']);
    }

    /**
     * 粘贴菜单组列表
     * @param MenuGroup $copy
     * @param MenuGroup $pasted
     * @return mixed
     * @methods(GET,POST)
     */
    public function pasted(MenuGroup $copy, MenuGroup $pasted) {
        if (!Request::isMethod('post')) {
            $result = $this->menus($copy);
            return $result->with('menuGroup', $pasted);
        }
        return $this->menus($pasted);
    }

    /**
     * 获取列表
     * @param Closure $callback
     * @return view
     */
    private function _lists(Closure $callback = null) {
        $where = [
            'name' => 'like',
        ];
        $order = ['created' => 'created_at'];
        $default = ['order' => 'created', 'by' => 'desc'];
        list($lists, $toOrder) = $this->listsSelect(MenuGroup::class, $where, $order, $default, $callback);
        $description = $this->description;
        return view('power.menu.group.lists', compact('lists', 'description', 'toOrder'));
    }

}
