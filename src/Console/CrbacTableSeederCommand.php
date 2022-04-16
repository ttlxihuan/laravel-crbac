<?php

/*
 * 数据填充
 */

namespace Laravel\Crbac\Console;

use Illuminate\Console\Command;
use Laravel\Crbac\Models\Power\Admin;
use Laravel\Crbac\Models\Power\Role;
use Laravel\Crbac\Models\Power\Item;
use Laravel\Crbac\Models\Power\Menu;
use Illuminate\Support\Facades\Hash;
use Laravel\Crbac\Models\Power\RoleItem;
use Laravel\Crbac\Models\Power\ItemGroup;
use Laravel\Crbac\Models\Power\MenuGroup;
use Laravel\Crbac\Models\Power\MenuLevel;
use Laravel\Crbac\Models\Power\RoleAdmin;

class CrbacTableSeederCommand extends Command {

    //控制台命令名
    protected $name = 'crbac:seeder';
    //控制台备注说明
    protected $description = '填充Crbac所需数据';

    /**
     * 执行控制台命令
     */
    public function handle() {
        $now = time();
        $this->info('插入数据表：' . (new Role())->getTable());
        Role::insert([
            array('id' => 1, 'name' => '超级管理员', 'status' => 'enable', 'comment' => '使用于核心技术人员，无特殊情况禁止使用到其他非技术人员', 'created_at' => $now, 'updated_at' => $now),
        ]);
        $this->info('插入数据表：' . (new Item())->getTable());
        Item::insert([
            array('id' => 1, 'name' => '允许修改所有权限项', 'code' => 'all_power_items', 'power_item_group_id' => 1, 'status' => 'enable', 'comment' => '允许拥有角色编辑功能人员可以分配所有权限项，否则只能编辑自己拥有的角色和权限功能，在部门管理员人禁止使用该功能，防止部门管理员人可以分配自己未拥有的权限项', 'created_at' => $now, 'updated_at' => $now),
            array('id' => 2, 'name' => '添加菜单', 'code' => 'Laravel\\Crbac\\Controllers\\Power\\MenuController@add', 'power_item_group_id' => 1, 'status' => 'enable', 'comment' => '允许添加菜单，用于添加新的菜单并追加到菜单结构体中，并且可以附加权限项，该功能由核心人员操作', 'created_at' => $now, 'updated_at' => $now),
            array('id' => 3, 'name' => '角色列表', 'code' => 'Laravel\\Crbac\\Controllers\\Power\\RoleController@lists', 'power_item_group_id' => 1, 'status' => 'enable', 'comment' => '系统中定义可用的角色列表，角色与管理员属于一对多关系，同一个管理员可以拥有多个角色。', 'created_at' => $now, 'updated_at' => $now),
            array('id' => 4, 'name' => '添加权限组', 'code' => 'Laravel\\Crbac\\Controllers\\Power\\ItemGroupController@add', 'power_item_group_id' => 1, 'status' => 'enable', 'comment' => '允许添加权限组，用于给新权限项添加分类或分组，PHP开发人员专用', 'created_at' => $now, 'updated_at' => $now),
            array('id' => 5, 'name' => '权限项列表', 'code' => 'Laravel\\Crbac\\Controllers\\Power\\ItemController@lists', 'power_item_group_id' => 1, 'status' => 'enable', 'comment' => '系统定义的每个可用权限项列表', 'created_at' => $now, 'updated_at' => $now),
            array('id' => 6, 'name' => '菜单列表', 'code' => 'Laravel\\Crbac\\Controllers\\Power\\MenuController@lists', 'power_item_group_id' => 1, 'status' => 'enable', 'comment' => '系统中定义的每个可用菜单列表，该数据一般由技术人员添加，然后分配到菜单组的菜单结构中。', 'created_at' => $now, 'updated_at' => $now),
            array('id' => 7, 'name' => '菜单组列表', 'code' => 'Laravel\\Crbac\\Controllers\\Power\\MenuGroupController@lists', 'power_item_group_id' => 1, 'status' => 'enable', 'comment' => '系统定义可用菜单组列表，菜单组用于针对不同使用人员或部门定制不同的菜单结构，再分配对对应人员，用于实现菜单结构可配置化。', 'created_at' => $now, 'updated_at' => $now),
            array('id' => 8, 'name' => '权限项组列表', 'code' => 'Laravel\\Crbac\\Controllers\\Power\\ItemGroupController@lists', 'power_item_group_id' => 1, 'status' => 'enable', 'comment' => '显示现有的权限项分组列表，权限项组属于技术开发专用，用于给添加的新权限项分组，方便后期分类查看权限项', 'created_at' => $now, 'updated_at' => $now),
            array('id' => 9, 'name' => '添加权限项', 'code' => 'Laravel\\Crbac\\Controllers\\Power\\ItemController@add', 'power_item_group_id' => 1, 'status' => 'enable', 'comment' => '允许添加权限项，属于核心权限功能，PHP开发人员专用', 'created_at' => $now, 'updated_at' => $now),
            array('id' => 10, 'name' => '编辑权限项', 'code' => 'Laravel\\Crbac\\Controllers\\Power\\ItemController@edit', 'power_item_group_id' => 1, 'status' => 'enable', 'comment' => '允许编辑权限项，属于修改核心权限功能，PHP开发人员专用', 'created_at' => $now, 'updated_at' => $now),
            array('id' => 11, 'name' => '编辑权限项组', 'code' => 'Laravel\\Crbac\\Controllers\\Power\\ItemGroupController@edit', 'power_item_group_id' => 1, 'status' => 'enable', 'comment' => '允许编辑权限项组，只是用于给权限项分组（分类），用于划分权限项的大体功能类别，以便给好的分配权限', 'created_at' => $now, 'updated_at' => $now),
            array('id' => 12, 'name' => '路由列表', 'code' => 'Laravel\\Crbac\\Controllers\\Power\\ItemController@routes', 'power_item_group_id' => 1, 'status' => 'enable', 'comment' => '技术开发专用，用于显示程序中已经添加成功的路由列表，方便添加对应的权限项或菜单', 'created_at' => $now, 'updated_at' => $now),
            array('id' => 13, 'name' => '编辑菜单', 'code' => 'Laravel\\Crbac\\Controllers\\Power\\MenuController@edit', 'power_item_group_id' => 1, 'status' => 'enable', 'comment' => '允许编辑菜单，可以添加想要的菜单并追加到对应菜单结构体中，还可以附加权限项功能，该功能禁止分配给非核心人员', 'created_at' => $now, 'updated_at' => $now),
            array('id' => 14, 'name' => '编辑角色权限项', 'code' => 'Laravel\\Crbac\\Controllers\\Power\\RoleController@items', 'power_item_group_id' => 1, 'status' => 'enable', 'comment' => '允许编辑角色权限项，属于权限分配功能，可以自由分配角色下的可使用权限功能', 'created_at' => $now, 'updated_at' => $now),
            array('id' => 15, 'name' => '删除菜单', 'code' => 'Laravel\\Crbac\\Controllers\\Power\\MenuController@delete', 'power_item_group_id' => 1, 'status' => 'enable', 'comment' => '允许删除菜单，属于危险操作，当菜单结构体中有使用时，是无法删除', 'created_at' => $now, 'updated_at' => $now),
            array('id' => 16, 'name' => '添加菜单组', 'code' => 'Laravel\\Crbac\\Controllers\\Power\\MenuGroupController@add', 'power_item_group_id' => 1, 'status' => 'enable', 'comment' => '允许添加菜单组，即添加一种菜单结构体', 'created_at' => $now, 'updated_at' => $now),
            array('id' => 17, 'name' => '删除菜单组', 'code' => 'Laravel\\Crbac\\Controllers\\Power\\MenuGroupController@delete', 'power_item_group_id' => 1, 'status' => 'enable', 'comment' => '允许删除菜单组，属于危险操作，当有菜单结构或管理员使用时，是无法删除', 'created_at' => $now, 'updated_at' => $now),
            array('id' => 18, 'name' => '编辑菜单组', 'code' => 'Laravel\\Crbac\\Controllers\\Power\\MenuGroupController@edit', 'power_item_group_id' => 1, 'status' => 'enable', 'comment' => '允许修改菜单组，只能修改名称与备注相关数据。', 'created_at' => $now, 'updated_at' => $now),
            array('id' => 19, 'name' => '编辑菜单组下菜单层级', 'code' => 'Laravel\\Crbac\\Controllers\\Power\\MenuGroupController@menus', 'power_item_group_id' => 1, 'status' => 'enable', 'comment' => '允许编辑菜单组下菜单层级，属于修改菜单结构功能，能随意修改当前菜单组下菜单结构体', 'created_at' => $now, 'updated_at' => $now),
            array('id' => 20, 'name' => '编辑角色中管理员', 'code' => 'Laravel\\Crbac\\Controllers\\Power\\RoleController@admins', 'power_item_group_id' => 1, 'status' => 'enable', 'comment' => '允许编辑角色下管理员列表，属于分配权限功能，可以移除或追加管理员到该角色名下', 'created_at' => $now, 'updated_at' => $now),
            array('id' => 21, 'name' => '删除角色', 'code' => 'Laravel\\Crbac\\Controllers\\Power\\RoleController@delete', 'power_item_group_id' => 1, 'status' => 'enable', 'comment' => '允许删除管理角色，属于危险操作，当角色已经绑定权限项，是无法删除', 'created_at' => $now, 'updated_at' => $now),
            array('id' => 22, 'name' => '编辑角色', 'code' => 'Laravel\\Crbac\\Controllers\\Power\\RoleController@edit', 'power_item_group_id' => 1, 'status' => 'enable', 'comment' => '允许编辑管理角色，属于核心权限功能', 'created_at' => $now, 'updated_at' => $now),
            array('id' => 23, 'name' => '添加角色', 'code' => 'Laravel\\Crbac\\Controllers\\Power\\RoleController@add', 'power_item_group_id' => 1, 'status' => 'enable', 'comment' => '允许添加管理角色，属于核心权限功能', 'created_at' => $now, 'updated_at' => $now),
            array('id' => 24, 'name' => '更新路由数据', 'code' => 'Laravel\\Crbac\\Controllers\\Power\\ItemController@updateRoutes', 'power_item_group_id' => 1, 'status' => 'enable', 'comment' => '允许更新路由列表，该功能属于PHP开发人员专用，其它人员无需分配', 'created_at' => $now, 'updated_at' => $now),
            array('id' => 25, 'name' => '删除权限项', 'code' => 'Laravel\\Crbac\\Controllers\\Power\\ItemController@delete', 'power_item_group_id' => 1, 'status' => 'enable', 'comment' => '允许删除权限项，属于危险操作，当已经有角色使用，是无法删除', 'created_at' => $now, 'updated_at' => $now),
            array('id' => 26, 'name' => '删除权限项组', 'code' => 'Laravel\\Crbac\\Controllers\\Power\\ItemGroupController@delete', 'power_item_group_id' => 1, 'status' => 'enable', 'comment' => '允许删除权限项组，属于危险操作，当组已经有权限项使用，是无法删除', 'created_at' => $now, 'updated_at' => $now),
            array('id' => 27, 'name' => '添加角色下管理员', 'code' => 'Laravel\\Crbac\\Controllers\\Power\\RoleController@addAdmin', 'power_item_group_id' => 1, 'status' => 'enable', 'comment' => '允许添加角色下管理员，属于分配权限，可直接把管理员添加到角色中', 'created_at' => $now, 'updated_at' => $now),
            array('id' => 28, 'name' => '添加角色下管理员', 'code' => 'Laravel\\Crbac\\Controllers\\Power\\RoleController@removeAdmin', 'power_item_group_id' => 1, 'status' => 'enable', 'comment' => '允许添加角色下管理员，属于权限分配，可直接把管理员从角色中移除', 'created_at' => $now, 'updated_at' => $now),
            array('id' => 29, 'name' => '管理员列表', 'code' => 'Laravel\\Crbac\\Controllers\\Power\\AdminController@lists', 'power_item_group_id' => 1, 'status' => 'enable', 'comment' => '显示管理员列表，管理员不能删除，只能禁用', 'created_at' => $now, 'updated_at' => $now),
            array('id' => 30, 'name' => '添加管理员', 'code' => 'Laravel\\Crbac\\Controllers\\Power\\AdminController@add', 'power_item_group_id' => 1, 'status' => 'enable', 'comment' => '允许添加系统管理员，并且可以指定管理员的菜单组，角色，状态等', 'created_at' => $now, 'updated_at' => $now),
            array('id' => 31, 'name' => '编辑管理员', 'code' => 'Laravel\\Crbac\\Controllers\\Power\\AdminController@edit', 'power_item_group_id' => 1, 'status' => 'enable', 'comment' => '允许编辑管理员信息，包括状态，菜单组，角色，密码等', 'created_at' => $now, 'updated_at' => $now),
            array('id' => 32, 'name' => '复制菜单组', 'code' => 'Laravel\Crbac\Controllers\Power\MenuGroupController@copy', 'power_item_group_id' => 1, 'status' => 'enable', 'comment' => '允许复制菜单组，只是进入复制页面，数据最终需要粘贴编辑', 'created_at' => $now, 'updated_at' => $now),
            array('id' => 33, 'name' => '粘贴菜单组', 'code' => 'Laravel\Crbac\Controllers\Power\MenuGroupController@pasted', 'power_item_group_id' => 1, 'status' => 'enable', 'comment' => '允许把一个菜单组的菜单层级数据粘贴到另一个菜单中进行编辑，不影响原菜单数据', 'created_at' => $now, 'updated_at' => $now)
        ]);
        $this->info('插入数据表：' . (new Menu())->getTable());
        Menu::insert([
            array('id' => 1, 'name' => '权限管理', 'url' => '/crbac/power/role.lists', 'power_item_id' => 3, 'comment' => '权限管理用于第一级菜单，一般可显示在导航最上面列表中，方便菜单结构展示', 'created_at' => $now, 'updated_at' => $now),
            array('id' => 2, 'name' => '角色管理', 'url' => '/crbac/power/role.lists', 'power_item_id' => 3, 'comment' => '角色管理用于给角色列表添加上级菜单名，方便菜单结构展示', 'created_at' => $now, 'updated_at' => $now),
            array('id' => 3, 'name' => '权限项管理', 'url' => '/crbac/power/item.lists', 'power_item_id' => 5, 'comment' => '权限项管理用于给权限项列表添加上级菜单名，方便菜单结构展示', 'created_at' => $now, 'updated_at' => $now),
            array('id' => 4, 'name' => '菜单管理', 'url' => '/crbac/power/menu.lists', 'power_item_id' => 6, 'comment' => '菜单管理用于给菜单列表添加上级菜单名，方便菜单结构展示', 'created_at' => $now, 'updated_at' => $now),
            array('id' => 5, 'name' => '菜单列表', 'url' => '/crbac/power/menu.lists', 'power_item_id' => 6, 'comment' => '系统中定义的每个可用菜单列表，该数据一般由技术人员添加，然后分配到菜单组的菜单结构中。', 'created_at' => $now, 'updated_at' => $now),
            array('id' => 6, 'name' => '菜单组列表', 'url' => '/crbac/power/menu-group.lists', 'power_item_id' => 7, 'comment' => '系统定义可用菜单组列表，菜单组用于针对不同使用人员或部门定制不同的菜单结构，再分配对对应人员，用于实现菜单结构可配置化。', 'created_at' => $now, 'updated_at' => $now),
            array('id' => 7, 'name' => '角色列表', 'url' => '/crbac/power/role.lists', 'power_item_id' => 3, 'comment' => '系统中定义可用的角色列表，角色与管理员属于一对多关系，同一个管理员可以拥有多个角色。', 'created_at' => $now, 'updated_at' => $now),
            array('id' => 8, 'name' => '权限项列表', 'url' => '/crbac/power/item.lists', 'power_item_id' => 5, 'comment' => '系统定义的每个可用权限项列表', 'created_at' => $now, 'updated_at' => $now),
            array('id' => 9, 'name' => '权限项组列表', 'url' => '/crbac/power/item-group.lists', 'power_item_id' => 8, 'comment' => '显示现有的权限项分组列表，权限项组属于技术开发专用，用于给添加的新权限项分组，方便后期分类查看权限项', 'created_at' => $now, 'updated_at' => $now),
            array('id' => 10, 'name' => '路由列表', 'url' => '/crbac/power/item.routes', 'power_item_id' => 12, 'comment' => '技术开发专用，用于显示程序中已经添加成功的路由列表，方便添加对应的权限项或菜单', 'created_at' => $now, 'updated_at' => $now),
            array('id' => 11, 'name' => '管理员列表', 'url' => '/crbac/power/admin.lists', 'power_item_id' => 29, 'comment' => '显示管理员列表，管理员不能删除，只能禁用', 'created_at' => $now, 'updated_at' => $now),
        ]);
        $this->info('插入数据表：' . (new RoleItem())->getTable());
        RoleItem::insert([
            array('power_role_id' => 1, 'power_item_id' => 1),
            array('power_role_id' => 1, 'power_item_id' => 2),
            array('power_role_id' => 1, 'power_item_id' => 3),
            array('power_role_id' => 1, 'power_item_id' => 4),
            array('power_role_id' => 1, 'power_item_id' => 5),
            array('power_role_id' => 1, 'power_item_id' => 6),
            array('power_role_id' => 1, 'power_item_id' => 7),
            array('power_role_id' => 1, 'power_item_id' => 8),
            array('power_role_id' => 1, 'power_item_id' => 9),
            array('power_role_id' => 1, 'power_item_id' => 10),
            array('power_role_id' => 1, 'power_item_id' => 11),
            array('power_role_id' => 1, 'power_item_id' => 12),
            array('power_role_id' => 1, 'power_item_id' => 13),
            array('power_role_id' => 1, 'power_item_id' => 14),
            array('power_role_id' => 1, 'power_item_id' => 15),
            array('power_role_id' => 1, 'power_item_id' => 16),
            array('power_role_id' => 1, 'power_item_id' => 17),
            array('power_role_id' => 1, 'power_item_id' => 18),
            array('power_role_id' => 1, 'power_item_id' => 19),
            array('power_role_id' => 1, 'power_item_id' => 20),
            array('power_role_id' => 1, 'power_item_id' => 21),
            array('power_role_id' => 1, 'power_item_id' => 22),
            array('power_role_id' => 1, 'power_item_id' => 23),
            array('power_role_id' => 1, 'power_item_id' => 24),
            array('power_role_id' => 1, 'power_item_id' => 25),
            array('power_role_id' => 1, 'power_item_id' => 26),
            array('power_role_id' => 1, 'power_item_id' => 27),
            array('power_role_id' => 1, 'power_item_id' => 28),
            array('power_role_id' => 1, 'power_item_id' => 29),
            array('power_role_id' => 1, 'power_item_id' => 30),
            array('power_role_id' => 1, 'power_item_id' => 31),
            array('power_role_id' => 1, 'power_item_id' => 32),
            array('power_role_id' => 1, 'power_item_id' => 33),
        ]);
        $this->info('插入数据表：' . (new ItemGroup())->getTable());
        ItemGroup::insert([
            array('id' => 1, 'name' => '权限管理相关', 'comment' => '所以权限操作相关功能定义的权限项，都应该分配到这个组下', 'created_at' => $now, 'updated_at' => $now),
        ]);
        $this->info('插入数据表：' . (new MenuGroup())->getTable());
        MenuGroup::insert([
            array('id' => 1, 'name' => '标准菜单', 'comment' => '系统标准菜单结构，通用菜单', 'created_at' => $now, 'updated_at' => $now),
        ]);
        $this->info('插入数据表：' . (new MenuLevel())->getTable());
        MenuLevel::insert([
            array('id' => 1, 'power_menu_id' => 1, 'power_menu_group_id' => 1, 'parent_id' => 0, 'sort' => 1),
            array('id' => 2, 'power_menu_id' => 2, 'power_menu_group_id' => 1, 'parent_id' => 1, 'sort' => 3),
            array('id' => 3, 'power_menu_id' => 3, 'power_menu_group_id' => 1, 'parent_id' => 1, 'sort' => 2),
            array('id' => 4, 'power_menu_id' => 4, 'power_menu_group_id' => 1, 'parent_id' => 1, 'sort' => 1),
            array('id' => 5, 'power_menu_id' => 5, 'power_menu_group_id' => 1, 'parent_id' => 4, 'sort' => 2),
            array('id' => 6, 'power_menu_id' => 7, 'power_menu_group_id' => 1, 'parent_id' => 2, 'sort' => 2),
            array('id' => 7, 'power_menu_id' => 11, 'power_menu_group_id' => 1, 'parent_id' => 2, 'sort' => 1),
            array('id' => 8, 'power_menu_id' => 8, 'power_menu_group_id' => 1, 'parent_id' => 3, 'sort' => 3),
            array('id' => 9, 'power_menu_id' => 9, 'power_menu_group_id' => 1, 'parent_id' => 3, 'sort' => 2),
            array('id' => 10, 'power_menu_id' => 10, 'power_menu_group_id' => 1, 'parent_id' => 3, 'sort' => 1),
            array('id' => 11, 'power_menu_id' => 6, 'power_menu_group_id' => 1, 'parent_id' => 4, 'sort' => 1)
        ]);
        $this->info('插入数据表：' . (new RoleAdmin())->getTable());
        RoleAdmin::insert([
            array('power_role_id' => 1, 'power_admin_id' => 1),
        ]);
        $this->info('插入数据表：' . (new Admin())->getTable());
        Admin::insert([
            array('id' => 1, 'realname' => '超级管理员', 'username' => 'admin', 'password' => Hash::make('123456'), 'email' => 'admin@admin.com', 'power_menu_group_id' => 1, 'status' => 'enable', 'created_at' => $now, 'updated_at' => $now),
        ]);
    }

}
