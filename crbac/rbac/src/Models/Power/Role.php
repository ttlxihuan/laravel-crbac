<?php

/*
 * 角色
 */

namespace XiHuan\Crbac\Models\Power;

use XiHuan\Crbac\Models\Model;
use XiHuan\Crbac\Models\StatusTrait;

class Role extends Model {

    use StatusTrait;

    public static $_validator_rules = [//验证规则
        'name' => 'required|between:3,30|unique:power_role', // varchar(35) not null comment '角色名',
        'status' => 'required|in:disable,enable', // enum('disable','enable') NOT NULL DEFAULT 'enable' COMMENT '启用或禁用，enable为启用',
        'comment' => 'required|between:1,955', //  varchar(1000) not null default '' comment '备注说明',
    ];
    public static $_validator_description = [//验证字段说明
        'name' => '角色名', // varchar(35) not null comment '角色名',
        'status' => '角色状态', // enum('disable','enable') NOT NULL DEFAULT 'enable' COMMENT '启用或禁用，enable为启用',
        'comment' => '备注说明', // varchar(1000) not null default '' comment '备注说明',
    ];
    public static $_validator_messages = []; //验证统一说明
    protected $table = 'power_role'; //表名
    protected $primaryKey = 'power_role_id'; //主键名
    protected static $validates = ['name']; //允许验证可用字段

    /*
     * 作用：关联权限项
     * 参数：无
     * 返回值：Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function items() {
        return $this->belongsToMany(Item::class, 'power_role_item', $this->primaryKey, 'power_item_id');
    }
}
