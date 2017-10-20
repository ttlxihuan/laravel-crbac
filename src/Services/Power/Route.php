<?php

/*
 * 路由记录
 */

namespace XiHuan\Crbac\Services\Power;

use Route as Router;
use XiHuan\Crbac\Models\Power\Route as RouteModel;

class Route extends Service {
    /*
     * 作用：更新路由列表
     * 参数：无
     * 返回值：void
     */
    public function update() {
        set_time_limit(3600);
        RouteModel::truncate(); //清空表
        $inserts = [];
        foreach (Router::getRoutes()->getIterator() as $route) {
            $uses = array_get($route->getAction(), 'uses');
            if ($uses && is_string($uses)) {
                $action = explode('@', $uses);
                $inserts[] = [
                    'uses' => str_replace('App\\Http\\Controllers\\', '', $uses),
                    'url' => $route->uri(),
                    'is_usable' => class_exists($action[0]) && method_exists($action[0], $action[1]) ? 'yes' : 'no',
                ];
            }
        }
        RouteModel::insert($inserts);
    }
}
