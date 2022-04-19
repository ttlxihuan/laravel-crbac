<?php

/*
 * 路由记录
 */

namespace Laravel\Crbac\Models\Power;

use Laravel\Crbac\Models\Model;

class Route extends Model {

    protected $table = 'power_route'; //表名

    /**
     * 关联权限项
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function item() {
        return $this->hasOne(Item::class, 'code', 'uses');
    }

}
