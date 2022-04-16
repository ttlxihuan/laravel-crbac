<?php

/*
 * 角色
 */

namespace Laravel\Crbac\Models\Power;

use Laravel\Crbac\Models\Model;
use Laravel\Crbac\Models\StatusTrait;

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
    protected static $validates = ['name']; //允许验证可用字段

    /*
     * 作用：关联权限项
     * 参数：无
     * 返回值：Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function items() {
        return $this->belongsToMany(Item::class, 'power_role_item', 'power_role_id', 'power_item_id');
    }

    /**
     * Get a new query builder for the model's table.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function newQuery() {
        $builder = parent::newQuery();
        //是否允许操作所有权限
        if (auth()->check() && !isPower('all_power_items')) {
            $builder->whereIn($this->table . '.id', function($query) {
                $query->from('power_role_admin')
                        ->where('power_admin_id', '=', auth()->id())
                        ->select('power_role_id');
            });
        }
        return $builder;
    }

}
