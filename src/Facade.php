<?php

namespace Laravel\Crbac;

use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;

class Facade extends \Illuminate\Support\Facades\Facade {
    /*
     * 作用：获取工厂代理码
     * 参数：无
     * 返回值：string
     */
    protected static function getFacadeAccessor() {
        return 'crbac';
    }

    /*
     * 作用：创建 Rbac
     * 参数：$admin Illuminate\Contracts\Auth\Authenticatable
     * 返回值：Rbac
     */
    public static function make(UserContract $admin) {
        return (new Rbac(static::$app))->setAdmin($admin);
    }

    /*
     * 作用：代理调用
     * 参数：$method string 方法名
     *       $args array 参数集
     * 返回值：mixed
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
