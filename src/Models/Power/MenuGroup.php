<?php

/*
 * 菜单组
 */

namespace Laravel\Crbac\Models\Power;

use Laravel\Crbac\Models\Model;

class MenuGroup extends Model {

    public static $_validator_rules = [//验证规则
        'name' => 'required|between:3,30|unique:power_menu_group', // varchar(35) not null comment '菜单组名',
        'comment' => 'required|between:1,955', //  varchar(1000) not null default '' comment '备注说明',
    ];
    public static $_validator_description = [//验证字段说明
        'name' => '菜单组名', // varchar(35) not null comment '菜单组名',
        'comment' => '备注说明', // varchar(1000) not null default '' comment '备注说明',
    ];
    public static $_validator_messages = []; //验证统一说明
    protected $table = 'power_menu_group'; //表名
    protected static $validates = ['name']; //允许验证可用字段

    /*
     * 作用：关联菜单
     * 参数：无
     * 返回值：Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function menus() {
        return $this->belongsToMany(Menu::class, 'power_menu_level', $this->primaryKey, 'power_menu_id');
    }
    /*
     * 作用：关联管理员
     * 参数：无
     * 返回值：Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function admins() {
        $class = auth_model();
        return $this->hasMany($class, 'menu_group_id');
    }
}
