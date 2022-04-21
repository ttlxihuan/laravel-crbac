<?php

/*
 * 角色管理
 */

namespace Laravel\Crbac\Controllers\Power;

use Laravel\Crbac\Models\Power\Admin;
use Laravel\Crbac\Models\Power\Role;
use Laravel\Crbac\Models\Power\Item;
use Illuminate\Support\Facades\Request;
use Laravel\Crbac\Models\Power\RoleAdmin;
use Laravel\Crbac\Models\Power\ItemGroup;
use Laravel\Crbac\Controllers\Controller;
use Laravel\Crbac\Services\Power\Role as RoleService;

class RoleController extends Controller {

    //备注说明
    protected $description = '角色';

    /**
     * 编辑角色数据
     * @param Role $item
     * @return mixed
     * @methods(GET,POST)
     */
    public function edit(Role $item = null) {
        return $this->modelEdit($item, 'power.role.edit', Role::class);
    }

    /**
     * 删除角色数据
     * @param Role $item
     * @return mixed
     * @methods(GET)
     */
    public function delete(Role $item) {
        if ($item->items()->count()) {
            return prompt($this->description . '已经在使用中，无法删除！', 'error', -1);
        }
        return $this->modelDelete($item);
    }

    /**
     * 角色列表
     * @return view
     * @methods(GET)
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

    /**
     * 管理员列表
     * @param string $type
     * @param Role $role
     * @return view
     * @methods(GET)
     */
    public function admins(string $type, Role $role) {
        $where = [
            'username' => 'like',
            'realname' => 'like',
            'status',
        ];
        $order = ['created' => 'created_at'];
        $default = ['order' => 'created', 'by' => 'desc'];
        list($lists, $toOrder) = $this->listsSelect(auth_model(), $where, $order, $default, function($builder)use($type, $role) {
            $builder->whereIn($builder->getModel()->getKeyName(), function($query)use($role) {
                $query->from('power_role_admin')
                        ->where('power_role_id', '=', $role->getKey())
                        ->select('power_admin_id');
            }, 'and', $type !== 'bind');
        });
        $description = ($type == 'unbind' ? '未绑定在' : '已经绑定在') . $this->description . '下管理员';
        $title = ($type == 'unbind' ? '未绑定' : '已经绑定') . '管理员';
        return view('power.role.admins', compact('lists', 'role', 'description', 'toOrder', 'title', 'type'));
    }

    /**
     * 快捷选择列表
     * @param string $relation
     * @return view
     * @methods(GET)
     */
    public function select(string $relation) {
        $where = ['name' => 'like'];
        $order = ['created' => 'created_at'];
        $default = ['order' => 'created', 'by' => 'desc'];
        list($lists, $toOrder) = $this->listsSelect(Role::class, $where, $order, $default, function($builder) {
            $builder->where('status', '=', 'enable');
        });
        return view('power.role.select', compact('lists', 'toOrder', 'relation'));
    }

    /**
     * 移除指定管理员
     * @param Role $item
     * @param Admin $admin
     * @return mixed
     * @methods(GET)
     */
    public function removeAdmin(Role $item, Admin $admin) {
        $roleAdmin = RoleAdmin::where('power_role_id', '=', $item->getKey())
                ->where('power_admin_id', '=', $admin->getKey())
                ->first();
        return $this->modelDelete($roleAdmin);
    }

    /**
     * 添加指定管理员
     * @param Role $item
     * @param Admin $admin
     * @return mixed
     * @methods(GET)
     */
    public function addAdmin(Role $item, Admin $admin) {
        RoleAdmin::firstOrCreate(['power_role_id' => $item->getKey(), 'power_admin_id' => $admin->getKey()]);
        return prompt('管理员添加成功！', 'success', -1);
    }

    /**
     * 角色对应的权限项编辑
     * @param Role $role
     * @return mixed
     * @methods(GET,POST)
     */
    public function items(Role $role) {
        if (!Request::isMethod('post')) {
            if (isPower('all_power_items')) {//是否允许修改所有权限
                $lists = Item::with('menus')->orderBy('code', 'asc')->get()->groupBy('power_item_group_id');
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
        return $service->prompt('角色权限项编辑成功', null, -1);
    }

}
