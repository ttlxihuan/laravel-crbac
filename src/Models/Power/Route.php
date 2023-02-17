<?php

/*
 * 路由记录
 */

namespace Laravel\Crbac\Models\Power;

use Laravel\Crbac\Models\Model;

class Route extends Model {

    /**
     * @var bool 是否记录修改日志
     */
    protected $saveUpdateLog = false;

    /**
     * @var string 表名
     */
    protected $table = 'power_route';

    /**
     * 关联权限项
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function item() {
        return $this->hasOne(Item::class, 'code', 'uses');
    }

}
