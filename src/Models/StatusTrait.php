<?php

/*
 * 状态处理
 */

namespace Laravel\Crbac\Models;

trait StatusTrait {

    public static $_STATUS = [//状态配置
        'disable' => '禁用',
        'enable' => '启用'
    ];

    /*
     * 作用：获取状态名
     * 参数：无
     * 返回值：string|false|null
     */
    public function statusName() {
        return $this->status ? array_get(static::$_STATUS, $this->status) : false;
    }
}
