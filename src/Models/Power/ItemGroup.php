<?php

/*
 * 权限项组
 */

namespace XiHuan\Crbac\Models\Power;

use XiHuan\Crbac\Models\Model;

class ItemGroup extends Model {

    public static $_validator_rules = [//验证规则
        'name' => 'required|between:3,30|unique:power_item_group', // varchar(35) not null comment '权限项组名称',
        'comment' => 'required|between:1,955', //  varchar(1000) not null default '' comment '备注说明',
    ];
    public static $_validator_description = [//验证字段说明
        'name' => '权限项组名称', // varchar(35) not null comment '权限项组名称',
        'comment' => '备注说明', // varchar(1000) not null default '' comment '备注说明',
    ];
    public static $_validator_messages = []; //验证统一说明
    protected $table = 'power_item_group'; //表名
    protected $primaryKey = 'power_item_group_id'; //主键名
    protected static $validates = ['name']; //允许验证可用字段

    /*
     * 作用：关联权限项
     * 参数：无
     * 返回值：Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items() {
        return $this->hasMany(Item::class, 'power_item_group_id');
    }
}
