<?php

/*
 * 管理员
 */

namespace XiHuan\Crbac\Models;

use Hash;
use XiHuan\Crbac\Models\Power\Menu;
use XiHuan\Crbac\Models\Power\Role;
use XiHuan\Crbac\Models\Power\MenuGroup;
use Illuminate\Contracts\Auth\Authenticatable;

class Admin extends Model implements Authenticatable {

    use \Illuminate\Auth\Authenticatable,
        StatusTrait;

    public static $_validator_rules = [//验证规则
        'realname' => 'required|between:2,30|unique:admin', // varchar(32) NOT NULL COMMENT '真实姓名',
        'username' => 'required|between:3,30|unique:admin', // varchar(32) NOT NULL COMMENT '登录用户名',
        'password' => 'required|between:6,20', // varchar(64) NOT NULL COMMENT '登录密码',
        'email' => 'email|between:6,55', // varchar(64) NOT NULL COMMENT '邮箱名',
        'power_menu_group_id' => 'required|exists:power_menu_group', // int(11) NOT NULL DEFAULT '0' COMMENT '菜单组ID',
        'status' => 'required|in:disable,enable', // enum('disable','enable') NOT NULL DEFAULT 'enable' COMMENT '启用或禁用，enable为启用',
    ];
    public static $_validator_description = [//验证字段说明
        'realname' => '真实姓名', // varchar(32) DEFAULT NULL COMMENT '真实姓名',
        'username' => '用户名', // varchar(32) DEFAULT NULL COMMENT '登录用户名',
        'password' => '密码', // varchar(64) NOT NULL COMMENT '登录密码',
        'email' => '邮箱名', // varchar(64) NOT NULL COMMENT '邮箱名',
        'power_menu_group_id' => '菜单组', // int(11) NOT NULL DEFAULT '0' COMMENT '菜单组ID',
        'status' => '用户状态', // enum('disable','enable') NOT NULL DEFAULT 'enable' COMMENT '启用或禁用，enable为启用',
    ];
    protected $table = 'admin'; //表名
    protected $primaryKey = 'admin_id'; //主键名

    /*
     * 作用：获取管理员的首页地址
     * 参数：无
     * 返回值：string|url
     */
    public function index() {
        $menus = Menu::menus($this)
                ->groupBy('parent_id');
        if ($menus->has('0')) {//有第一页
            return array_first($menus[0], function() {
                        return true;
                    })->url;
        }
        //没有
        return route('logout');
    }
    /*
     * 作用：关联菜单组
     * 参数：无
     * 返回值：Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function menuGroup() {
        return $this->hasOne(MenuGroup::class, 'power_menu_group_id', 'power_menu_group_id');
    }
    /*
     * 作用：关联角色
     * 参数：无
     * 返回值：Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles() {
        return $this->belongsToMany(Role::class, 'power_role_admin', $this->primaryKey, 'power_role_id');
    }
    /*
     * 作用：设置保存密码
     * 参数：无
     * 返回值：void
     */
    public function savePassword() {
        if (isset($this->attributes['password']) && $this->attributes['password'] !== $this->original['password']) {
            $this->attributes['password'] = Hash::make($this->attributes['password']);
        }
    }
}
