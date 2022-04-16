<?php

/*
 * 权限项
 */

namespace Laravel\Crbac\Controllers\Power;

use Laravel\Crbac\Models\Power\Item;
use Laravel\Crbac\Models\Power\Route;
use Illuminate\Support\Facades\Request;
use Laravel\Crbac\Controllers\Controller;
use Laravel\Crbac\Services\Power\Route as RouteService;

class ItemController extends Controller {

    //备注说明
    protected $description = '权限项';

    /**
     * 编辑权限项数据
     * @param Item $item
     * @return mixed
     * @methods(GET,POST)
     */
    public function edit(Item $item = null) {
        $result = $this->modelEdit($item, 'power.item.edit', Item::class);
        if (!$item && !Request::isMethod('post')) {
            $route_id = (int) Request::input('route');
            $route = $route_id ? Route::find($route_id) : null;
            $result->with('route', $route);
        }
        return $result;
    }

    /**
     * 删除权限项
     * @param Item $item
     * @return mixed
     * @methods(GET)
     */
    public function delete(Item $item) {
        if ($item->roles()->count() || $item->menus()->count()) {
            return prompt($this->description . '已经在使用中，无法删除！', 'error', -1);
        }
        return $this->modelDelete($item);
    }

    /**
     * 权限项列表
     * @return view
     * @methods(GET)
     */
    public function lists() {
        $where = [
            'name' => 'like',
            'code' => 'like',
            'group_id' => ['power_item_group_id', '='],
            'status',
        ];
        $order = ['created' => 'created_at'];
        $default = ['order' => 'created', 'by' => 'desc'];
        list($lists, $toOrder) = $this->listsSelect(Item::class, $where, $order, $default);
        $description = $this->description;
        return view('power.item.lists', compact('lists', 'description', 'toOrder'));
    }

    /**
     * 路由列表
     * @return view
     * @methods(GET)
     */
    public function routes() {
        $where = [
            'uses' => 'like',
            'url' => 'like',
            'status' => function($builder, $val) {
                if ($val === 'yes') {
                    $builder->whereIn('uses', function($query) {
                                $query->from('power_item')
                                        ->select('code');
                            });
                } elseif ($val === 'no') {
                    $builder->whereNotIn('uses', function($query) {
                                $query->from('power_item')
                                        ->select('code');
                            });
                }
            },
        ];
        $order = [];
        $default = ['status' => 'no'];
        list($lists) = $this->listsSelect(Route::class, $where, $order, $default, function($builder) {
            $builder->with('item')
                    ->orderBy('id', 'desc');
        });
        $description = '路由';
        return view('power.item.routes', compact('lists', 'description'));
    }

    /**
     * 更新路由列表
     * @return mixed
     * @methods(GET)
     */
    public function updateRoutes() {
        $service = new RouteService();
        $service->update();
        return $service->prompt(null, null, -1);
    }

}
