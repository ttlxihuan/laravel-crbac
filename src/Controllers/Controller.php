<?php

/*
 * 公共处理块
 */

namespace XiHuan\Crbac\Controllers;

use URL,
    Closure;
use View;
use Crbac;
use Illuminate\Pagination\Paginator;
use XiHuan\Crbac\Services\ModelEdit;
use XiHuan\Crbac\Services\ModelSelect;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController {

    //备注说明
    protected $description = '';

    /*
     * 作用：初始处理
     * 参数：无
     * 返回值：void
     */
    public function __construct() {
        //分页定义
        Paginator::presenter(function($paginator) {
            return view('page', compact('paginator'));
        });
        //菜单处理
        View::composer(['public.menu', 'public.crumbs'], function($view) {
            $menus = Crbac::menus();
            $crumbs = Crbac::crumbs();
            $crumbs_ids = array_map(function($model) {
                return $model->getKey();
            }, $crumbs);
//            dd($crumbs_ids);
            $view->with(compact('menus', 'crumbs', 'crumbs_ids'));
        });
    }
    /*
     * 作用：删除数据
     * 参数：$item Model 需要删除的数据
     * 返回值：view|array
     */
    public function delete($item) {
        if ($item->delete()) {
            return prompt($this->description . '删除成功！', 'success', -1);
        } else {
            return prompt($this->description . '删除失败！', 'error', -1);
        }
    }
    /*
     * 作用：添加数据
     * 参数：无
     * 返回值：view|array
     */
    public function add() {
        return $this->edit();
    }
    /*
     * 作用：修改数据
     * 参数：$item Model|null 要修改的数据
     *      $view string 显示的视图名
     *      $modelClass string 要修改的Model类名
     *      $option array 要修改的数据项,默认全部
     *      $serviceClass string 处理修改的Service类名
     * 返回值：view|array
     */
    protected function modelEdit($item, $view, $modelClass, array $option = [], $serviceClass = null) {
        $title = ($item ? '编辑' : '创建') . $this->description;
        if (!Request::isMethod('post')) {
            return view($view, compact('item', 'title', 'modelClass'));
        }
        $serviceClassName = $serviceClass? : str_replace('\\Models', '\\Services', $modelClass);
        if (class_exists($serviceClassName)) {
            $service = new $serviceClassName();
            $result = $service->edit($item? : $modelClass, $option);
        } else {
            $service = new ModelEdit();
            $result = $service->requestEdit($item? : $modelClass, $option);
        }
        $redirect = null;
        if ($result) {
//            $redirect = Request::get('_referrer') ?: URL::previous();
//            if (Request::has('_redirect')) {
//                $redirect = Request::get('_redirect');
//            }
        }
        return $service->prompt($title . ($result ? '成功' : '失败'), null, $redirect);
    }
    /*
     * 作用：查询列表数据
     * 参数：$className string 要查询的Model类名
     *      $where array 条件规则
     *      $order array 排序规则
     *      $default array 默认请求参数
     *      $callback Closure 回调处理查询
     * 返回值：array
     */
    protected function listsSelect($className, $where = [], $order = [], $default = [], Closure $callback = null) {
        $service = new ModelSelect($className, [], $default);
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
