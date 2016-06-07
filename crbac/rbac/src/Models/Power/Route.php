<?php

/*
 * 路由记录
 */

namespace XiHuan\Crbac\Models\Power;

use XiHuan\Crbac\Models\Model;

class Route extends Model {

    protected $table = 'power_route'; //表名
    protected $primaryKey = 'power_route_id'; //主键名

    /*
     * 作用：关联权限项
     * 参数：无
     * 返回值：Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function item() {
        return $this->hasOne(Item::class, 'code', 'uses');
    }
}
