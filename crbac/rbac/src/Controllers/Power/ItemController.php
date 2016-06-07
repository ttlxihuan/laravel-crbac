<?php

/*
 * 权限项
 */

namespace XiHuan\Crbac\Controllers\Power;

use Request;
use XiHuan\Crbac\Models\Power\Item;
use XiHuan\Crbac\Models\Power\Route;
use XiHuan\Crbac\Controllers\Controller;
use XiHuan\Crbac\Services\Power\Route as RouteService;

class ItemController extends Controller {

    //备注说明
    protected $description = '权限项';

    /*
     * 作用：编辑权限项数据
     * 参数：$item XiHuan\Crbac\Models\Power\Item 需要编辑的数据，默认为添加
     * 返回值：view|array
     */
    //修改
    public function edit(Item $item = null) {
        $result = $this->modelEdit($item, 'power.item.edit', Item::class);
        if (!$item && !Request::isMethod('post')) {
            $route_id = (int) Request::input('route');
            $route = $route_id ? Route::find($route_id) : null;
            $result->with('route', $route);
        }
        return $result;
    }
    /*
     * 作用：删除权限项
     * 参数：$item XiHuan\Crbac\Models\Power\Item 需要删除的数据
     * 返回值：view|array
     */
    //删除
    public function delete($item) {
        if ($item->roles()->count() || $item->menus()->count()) {
            return prompt($this->description . '已经在使用中，无法删除！', 'error', -1);
        }
        return parent::delete($item);
    }
    /*
     * 作用：权限项列表
     * 参数：无
     * 返回值：view
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
    /*
     * 作用：路由列表
     * 参数：无
     * 返回值：view
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
                    ->orderBy('power_route_id', 'desc');
        });
        $description = '路由';
        return view('power.item.routes', compact('lists', 'description'));
    }
    /*
     * 作用：更新路由列表
     * 参数：无
     * 返回值：view|array
     */
    public function updateRoutes() {
        $service = new RouteService();
        $service->update();
        return $service->prompt(null, null, -1);
    }
}
