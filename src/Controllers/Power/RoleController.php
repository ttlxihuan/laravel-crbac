<?php

/*
 * 角色管理
 */

namespace XiHuan\Crbac\Controllers\Power;

use Request;
use XiHuan\Crbac\Models\Admin;
use XiHuan\Crbac\Models\Power\Role;
use XiHuan\Crbac\Models\Power\Item;
use XiHuan\Crbac\Models\Power\RoleAdmin;
use XiHuan\Crbac\Models\Power\ItemGroup;
use XiHuan\Crbac\Controllers\Controller;
use XiHuan\Crbac\Services\Power\Role as RoleService;

class RoleController extends Controller {

    //备注说明
    protected $description = '角色';

    /*
     * 作用：编辑角色数据
     * 参数：$item XiHuan\Crbac\Models\Power\Item 需要编辑的数据，默认为添加
     * 返回值：view|array
     */
    public function edit(Role $item = null) {
        return $this->modelEdit($item, 'power.role.edit', Role::class);
    }
    /*
     * 作用：删除角色数据
     * 参数：$item XiHuan\Crbac\Models\Power\Item 角色数据
     * 返回值：view|array
     */
    public function delete($item) {
        if ($item->items()->count()) {
            return prompt($this->description . '已经在使用中，无法删除！', 'error', -1);
        }
        return parent::delete($item);
    }
    /*
     * 作用：角色列表
     * 参数：无
     * 返回值：view
     */
    public function lists() {
        $where = [
            'name' => 'like',
            'status',
        ];
        $order = ['created' => 'created_at'];
        $default = ['order' => 'created', 'by' => 'desc'];
        list($lists, $toOrder) = $this->listsSelect(Role::class, $where, $order, $default);
        $description = $this->description;
        return view('power.role.lists', compact('lists', 'description', 'toOrder'));
    }
    /*
     * 作用：管理员列表
     * 参数：$type string 类型，unbind|bind
     *      $role XiHuan\Crbac\Models\Power\Item 角色数据
     * 返回值：view
     */
    public function admins($type, Role $role) {
        $where = [
            'username' => 'like',
            'realname' => 'like',
            'status',
        ];
        $order = ['created' => 'created_at'];
        $default = ['order' => 'created', 'by' => 'desc'];
        list($lists, $toOrder) = $this->listsSelect(auth_model(), $where, $order, $default, function($builder)use($role) {
            $builder->whereIn($builder->getModel()->getKeyName(), function($query)use($role) {
                $query->from('power_role_admin')
                        ->where('power_role_id', '=', $role->getKey())
                        ->select('admin_id');
            }, 'and', \Route::input('bind_style') !== 'bind');
        });
        $description = ($type == 'unbind' ? '未绑定在' : '已经绑定在') . $this->description . '下管理员';
        $title = ($type == 'unbind' ? '未绑定' : '已经绑定') . '管理员';
        return view('power.role.admins', compact('lists', 'role', 'description', 'toOrder', 'title'));
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
        list($lists, $toOrder) = $this->listsSelect(Role::class, $where, $order, $default, function($builder) {
            $builder->where('status', '=', 'enable');
        });
        return view('power.role.select', compact('lists', 'toOrder'));
    }
    /*
     * 作用：移除指定管理员
     * 参数：$item XiHuan\Crbac\Models\Power\Item 角色数据
     *      $admin XiHuan\Crbac\Models\Admin 管理员数据
     * 返回值：view|array
     */
    public function removeAdmin(Role $item, Admin $admin) {
        $builder = RoleAdmin::where('power_role_id', '=', $item->getKey())
                ->where('admin_id', '=', $admin->getKey());
        return parent::delete($builder);
    }
    /*
     * 作用：添加指定管理员
     * 参数：$item XiHuan\Crbac\Models\Power\Item 角色数据
     *      $admin XiHuan\Crbac\Models\Admin 管理员数据
     * 返回值：view|array
     */
    public function addAdmin(Role $item, Admin $admin) {
        RoleAdmin::firstOrCreate(['power_role_id' => $item->getKey(), 'admin_id' => $admin->getKey()]);
        return prompt('管理员添加成功！', 'success', -1);
    }
    /*
     * 作用：角色对应的权限项编辑
     * 参数：$role XiHuan\Crbac\Models\Power\Item 角色数据
     * 返回值：view|array
     */
    public function items(Role $role) {
        if (!Request::isMethod('post')) {
            if (isPower('all_power_items')) {//是否允许修改所有权限
                $lists = Item::with('menus')->get()->groupBy('power_item_group_id');
            } else {
                $lists = Item::items(auth()->user(), 'menus')->groupBy('power_item_group_id');
            }
            $group_lists = ItemGroup::all();
            $items = $role->items->modelKeys();
            $title = $this->description . '下权限项编辑';
            return view('power.role.items', compact('lists', 'group_lists', 'items', 'title', 'role'));
        }
        $service = new RoleService();
        $service->editItems($role);
        return $service->prompt('角色权限项编辑成功');
    }
}
