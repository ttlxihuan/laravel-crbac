<?php

/*
 * 状态处理
 */

namespace Laravel\Crbac\Models;

trait StatusTrait {

    public static $_STATUS = [//状态配置
        'enable' => '启用',
        'disable' => '禁用'
    ];

    /**
     * 获取状态名
     * @return string|false|null
     */
    public function statusName() {
        return $this->status ? array_get(static::$_STATUS, $this->status) : false;
    }

}
