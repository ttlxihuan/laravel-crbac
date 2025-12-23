<?php

/*
 * 管理员
 */

namespace Laravel\Crbac\Models\Power;

use Hash;
use Laravel\Crbac\Models\Model;
use Illuminate\Contracts\Auth\Authenticatable;

class Admin extends Model implements Authenticatable {

    use \Illuminate\Auth\Authenticatable,
        \Laravel\Crbac\Models\GetMappingTrait;

    public static $_STATUS = [//状态配置
        'enable' => '启用',
        'disable' => '禁用',
        'lock' => '锁定',
    ];
    public static $_validator_rules = [//验证规则
        'realname' => 'required|between:2,30', // varchar(32) NOT NULL COMMENT '真实姓名',
        'username' => 'required|between:3,30|unique:power_admin', // varchar(32) NOT NULL COMMENT '登录用户名',
        'password' => 'required|between:6,20', // varchar(64) NOT NULL COMMENT '登录密码',
        'email' => 'email|between:6,55', // varchar(64) NOT NULL COMMENT '邮箱名',
        'power_menu_group_id' => 'required|exists:power_menu_group,id', // int(11) NOT NULL DEFAULT '0' COMMENT '菜单组ID',
        'status' => 'required|in:disable,lock,enable', // enum('disable','lock','enable') NOT NULL DEFAULT 'enable' COMMENT '状态，enable为启用',
    ];
    public static $_validator_description = [//验证字段说明
        'realname' => '真实姓名', // varchar(32) DEFAULT NULL COMMENT '真实姓名',
        'username' => '用户名', // varchar(32) DEFAULT NULL COMMENT '登录用户名',
        'password' => '密码', // varchar(64) NOT NULL COMMENT '登录密码',
        'email' => '邮箱名', // varchar(64) NOT NULL COMMENT '邮箱名',
        'power_menu_group_id' => '菜单组', // int(11) NOT NULL DEFAULT '0' COMMENT '菜单组ID',
        'status' => '用户状态', // enum('disable','lock','enable') NOT NULL DEFAULT 'enable' COMMENT '启用或禁用，enable为启用',
    ];
    protected static $validates = ['username']; //允许验证可用字段
    protected $table = 'power_admin'; //表名

    /**
     * 获取管理员的首页地址
     * @return string|url
     */
    public function index() {
        $menus = Menu::menus($this)
                ->groupBy('parent_id');
        if ($menus->has('0')) {//有第一页
            return array_first($menus[0], function () {
                        return true;
                    })->url;
        }
        //没有
        return route('logout');
    }

    /**
     * 关联菜单组
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function menuGroup() {
        return $this->hasOne(MenuGroup::class, 'id', 'power_menu_group_id');
    }

    /**
     * 关联角色
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles() {
        return $this->belongsToMany(Role::class, 'power_role_admin', 'power_admin_id', 'power_role_id');
    }

    /**
     * 设置保存密码
     */
    public function setPasswordAttribute($password) {
        $this->attributes['password'] = Hash::make($password);
    }
}
