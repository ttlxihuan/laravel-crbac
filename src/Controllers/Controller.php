<?php

/*
 * 公共处理块
 */

namespace Laravel\Crbac\Controllers;

use URL,
    Closure;
use View;
use Crbac;
use Illuminate\Pagination\Paginator;
use Laravel\Crbac\Services\ModelEdit;
use Illuminate\Database\Eloquent\Model;
use Laravel\Crbac\Services\ModelSelect;
use Illuminate\Support\Facades\Request;
use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController {

    //备注说明
    protected $description = '';

    /**
     * 初始处理
     */
    public function __construct() {
        //分页定义
        if (method_exists(Paginator::class, 'presenter')) {
            Paginator::presenter(function ($paginator) {
                return view('page', compact('paginator'));
            });
        } else {
            Paginator::$defaultView = 'page';
        }
        //菜单处理
        View::composer(['public.menu', 'public.crumbs'], function ($view) {
            $menus = Crbac::menus() ?: [];
            $crumbs = Crbac::crumbs() ?: [];
            $crumbs_ids = array_map(function ($model) {
                return $model->getKey();
            }, $crumbs);
            $view->with(compact('menus', 'crumbs', 'crumbs_ids'));
        });
    }

    /**
     * 删除数据
     * @param Model $item
     * @return mixed
     */
    public function modelDelete(Model $item) {
        if ($item->delete()) {
            return prompt($this->description . '删除成功！', 'success', -1);
        } else {
            return prompt($this->description . '删除失败！', 'error', -1);
        }
    }

    /**
     * 添加数据
     * @return mixed
     * @methods(GET,POST)
     */
    public function add() {
        return $this->edit();
    }

    /**
     * 修改数据
     * @param Model|null $item
     * @param string $view
     * @param string $modelClass
     * @param array $option
     * @param string $serviceClass
     * @return mixed
     */
    protected function modelEdit($item, string $view, string $modelClass, array $option = [], string $serviceClass = null) {
        $title = ($item ? '编辑' : '创建') . $this->description;
        if (!Request::isMethod('post')) {
            return view($view, compact('item', 'title', 'modelClass'));
        }
        $serviceClassName = $serviceClass ?: str_replace('\\Models', '\\Services', $modelClass);
        if (class_exists($serviceClassName)) {
            $service = new $serviceClassName();
            $result = $service->edit($item ?: $modelClass, $option);
        } else {
            $service = new ModelEdit();
            $result = $service->requestEdit($item ?: $modelClass, $option);
        }
        $redirect = null;
        if ($result) {
            $redirect = Request::get('_referrer') ?: URL::previous();
            if (Request::has('_redirect')) {
                $redirect = Request::get('_redirect');
            }
        }
        return $service->prompt($title . ($result ? '成功' : '失败'), null, $redirect);
    }

    /**
     * 查询列表数据
     * @param string $modelClass
     * @param array $where
     * @param array $order
     * @param array $default
     * @param Closure $callback
     * @return array
     */
    protected function listsSelect(string $modelClass, array $where = [], array $order = [], array $default = [], Closure $callback = null) {
        $service = new ModelSelect($modelClass, [], $default);
        $lists = $service->where($where)
                ->order($order)
                ->lists($callback);
        if ($order) {
            return [$lists, $service->orderToString()];
        } else {
            return [$lists];
        }
    }

}
