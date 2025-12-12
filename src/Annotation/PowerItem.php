<?php

/*
 * 权限项菜单类
 */

namespace Laravel\Crbac\Annotation;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class PowerItem {

    /**
     * 当前权限项名
     * @var string
     */
    protected $title;

    /**
     * 初始化处理
     * @param string $title
     */
    public function __construct(string $title) {
        $this->title = $title;
    }

    /**
     * 获取
     * @return array
     */
    public function get() {
        return $this->title;
    }
}
