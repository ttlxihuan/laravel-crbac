<?php

/*
 * 选项映射
 */

namespace Laravel\Crbac\Models;

trait GetMappingTrait {

    /**
     * 魔法调用
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call($method, $parameters) {
        if (stripos($method, 'get') === 0 && strlen($method) > 3) {
            $name = explode('get', $method, 2)[1];
            $config = '_' . strtoupper($name);
            $key = strtolower($name[0]) . substr($name, 1);
            if (isset(static::$$config)) {
                return $this->$key ? array_get(static::$$config, $this->$key) : false;
            }
        }
        return parent::__call($method, $parameters);
    }
}
