<?php

/*
 * 权限菜单注解类
 */

namespace Laravel\Crbac\Annotation;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class PowerMenu {

    /**
     * 当前菜单名
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
