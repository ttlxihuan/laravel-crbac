<?php

/*
 * 路由记录
 */

namespace Laravel\Crbac\Services\Power;

use Route as Router;
use Laravel\Crbac\Models\Power\Route as RouteModel;

class Route extends Service {

    /**
     * 更新路由列表
     */
    public function update() {
        set_time_limit(3600);
        RouteModel::truncate(); //清空表
        $inserts = [];
        $now = time();
        foreach (Router::getRoutes()->getIterator() as $route) {
            $action = $route->getAction();
            $uses = array_get($action, 'uses');
            if ($uses && is_string($uses) && array_get($action, 'as') != 'mvc-crbac') {
                $call = explode('@', $uses);
                $inserts[] = [
                    'uses' => $uses,
                    'url' => $route->uri(),
                    'methods' => implode(',', $route->methods()),
                    'is_usable' => class_exists($call[0]) && method_exists($call[0], $call[1]) ? 'yes' : 'no',
                    RouteModel::CREATED_AT => $now,
                    RouteModel::UPDATED_AT => $now,
                ];
            }
        }
        RouteModel::insert($inserts);
    }

}
