<?php

/*
 * 请求中间件注解类
 */

namespace Laravel\Crbac\Annotation\Request;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_CLASS)]
class Middleware {

    /**
     * 当前要使用的中间件
     * @var array
     */
    protected $middlewares;

    /**
     * 初始化处理
     * @param string $args
     */
    public function __construct(string ...$args) {
        $this->middlewares = $args;
    }

    /**
     * 获取当前的中间件
     * @return array
     */
    public function get() {
        return $this->middlewares;
    }
}
