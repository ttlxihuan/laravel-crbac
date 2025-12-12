<?php

/*
 * 请求类型注解类
 */

namespace Laravel\Crbac\Annotation\Request;

use Attribute;
use Exception;

#[Attribute(Attribute::TARGET_METHOD)]
class Methods {

    /**
     * 可使用的请求类型
     */
    const METHODS = ['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];

    /**
     * 当前可使用的请求类型
     * @var array
     */
    protected $methods;

    /**
     * 初始化处理
     * @param string $args
     */
    public function __construct(string ...$args) {
        $methods = array_map('strtoupper', $args);
        if (count($diff = array_diff($methods, self::METHODS))) {
            throw new Exception('Unknown request method: ' . implode(', ', $diff));
        }
        $this->methods = $methods;
    }

    /**
     * 判断是否匹配
     * @param string $method
     * @return bool
     */
    public function is(string $method) {
        return in_array(strtoupper($method), $this->methods, true);
    }

    /**
     * 获取当前可用的请求类型集合
     * @return array
     */
    public function get() {
        return $this->methods;
    }
}
