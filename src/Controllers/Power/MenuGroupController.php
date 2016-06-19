<?php

/*
 * 菜单组
 */

namespace XiHuan\Crbac\Controllers\Power;

use Input,
    Request,
    Closure;
use XiHuan\Crbac\Models\Power\Menu;
use XiHuan\Crbac\Models\Power\MenuGroup;
use XiHuan\Crbac\Controllers\Controller;
use XiHuan\Crbac\Services\Power\MenuGroup as MenuGroupService;

class MenuGroupController extends Controller {

    //备注说明
    protected $description = '菜单组';

    /*
     * 作用：编辑菜单组数据
     * 参数：$item XiHuan\Crbac\Models\Power\MenuGroup 需要编辑的数据，默认为添加
     * 返回值：view|array
     */
    public function edit(MenuGroup $item = null) {
        return $this->modelEdit($item, 'power.menu.group.edit', MenuGroup::class);
    }
    /*
     * 作用：获取菜单下级列表
     * 参数：$item XiHuan\Crbac\Models\Power\MenuGroup 菜单组
     * 返回值：array
     */
    //获取菜单级
    public function levelOption(MenuGroup $item) {
        $parent_id = (int) Input::Get('parent_id');
        return prompt([
            'options' => $item->menus()->where('parent_id', '=', $parent_id)->get(['power_menu_level.id', 'power_menu.name'])
        ]);
    }
    /*
     * 作用：删除菜单组数据
     * 参数：$item XiHuan\Crbac\Models\Power\MenuGroup 需要删除的菜单组
     * 返回值：view|array
     */
    public function delete($item) {
        if ($item->menus()->count() || $item->admins()->count()) {
            return prompt($this->description . '已经在使用中，无法删除！', 'error', -1);
        }
        return parent::delete($item);
    }
    /*
     * 作用：编辑菜单组中菜单层级
     * 参数：$item XiHuan\Crbac\Models\Power\MenuGroup 菜单组
     * 返回值：view|array
     */
    public function menus(MenuGroup $item) {
        if (!Request::isMethod('post')) {
            $title = '菜单层级编辑';
            $parent_id = '0';
            $level = '1';
            $menu_lists = Menu::group($item->getKey());
            $lists = $menu_lists->groupBy('parent_id');
            $level_lists = $menu_lists->lists('power_menu_id', 'level_id');
            return view('power.menu.group.level', compact('lists', 'title', 'parent_id', 'level', 'level_lists'))
                            ->with('menuGroup', $item);
        }
        $service = new MenuGroupService();
        $service->editMenuLevel($item);
        return $service->prompt('菜单层级编辑成功');
    }
    /*
     * 作用：菜单组列表
     * 参数：无
     * 返回值：view
     */
    public function lists() {
        return $this->_lists();
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
        list($lists, $toOrder) = $this->listsSelect(MenuGroup::class, $where, $order, $default);
        return view('power.menu.group.select', compact('lists', 'toOrder'));
    }
    /*
     * 作用：复制菜单组列表
     * 参数：$item XiHuan\Crbac\Models\Power\MenuGroup 菜单组
     * 返回值：view
     */
    public function copy(MenuGroup $item) {
        return $this->_lists(function($builder)use($item) {
                    $builder->where($item->getKeyName(), '!=', $item->getKey());
                })->with(['copy' => $item, 'title' => '复制菜单']);
    }
    /*
     * 作用：粘贴菜单组列表
     * 参数：$copy XiHuan\Crbac\Models\Power\MenuGroup 复制菜单组
     *      $pasted XiHuan\Crbac\Models\Power\MenuGroup 粘贴菜单组
     * 返回值：view|array
     */
    public function pasted(MenuGroup $copy, MenuGroup $pasted) {
        if (!Request::isMethod('post')) {
            $result = $this->menus($copy);
            return $result->with('menuGroup', $pasted);
        }
        return $this->menus($pasted);
    }
    /*
     * 作用：获取列表
     * 参数：$callback Closure 查询回调处理
     * 返回值：view
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
