<?php

namespace Laravel\Crbac;

use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;

class Facade extends \Illuminate\Support\Facades\Facade {

    /**
     * 获取代理码
     * @return string
     */
    protected static function getFacadeAccessor() {
        return 'crbac';
    }

    /**
     * 创建 Rbac
     * @param UserContract $admin
     * @return type
     */
    public static function make(UserContract $admin) {
        return (new Rbac(static::$app))->setAdmin($admin);
    }

    /**
     * 魔法代理调用
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public static function __callStatic($method, $args) {
        $instance = static::getFacadeRoot();
        if (Auth::check()) {
            if (!$instance->getAdmin()) {
                $instance->setAdmin(Auth::user());
            }
        }
        if (in_array($method, ['setAdmin', 'getAdmin']) || $instance->getAdmin()) {
            return parent::__callStatic($method, $args);
        }
    }

}
