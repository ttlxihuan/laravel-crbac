<?php

/*
 * 权限授权中间件
 */

namespace Laravel\Crbac\Middleware;

use Closure;
use Laravel\Crbac\Models\Power\Item;

class PowerAuthenticate {

    /**
     * 中间处理
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        // 如果不是当前授权处理模型则跳过
        $auth = auth()->user();
        if ($auth && is_a($auth, \Laravel\Crbac\Models\Power\Admin::class)) {
            if ($request->ajax() && $request->header('GET-ROUTER-USERS') === 'true') {
                $uses = currentRouteUses();
                $item = $uses ? Item::findCode($uses) : null;
                if ($item) {
                    $data = array_only($item->toArray(), ['code', 'status', 'power_item_group_id']);
                    $data['power_item_group_name'] = array_get($item->group->toArray(), 'name');
                    $data['roles'] = array_column($item->roles->toArray(), 'name', 'id') ?: [];
                    $item = $data;
                }
                return prompt(compact('uses', 'item'));
            }
            $power = isControllerPower(null, null, true);
            if (!$power) {
                return prompt('没有权限操作', 'error');
            }
        }
        return $next($request);
    }

}
