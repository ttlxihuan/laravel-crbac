<?php

/*
 * 权限管理对外基本操作
 */

namespace Laravel\Crbac;

use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\URL;
use Illuminate\Container\Container;
use Laravel\Crbac\Models\Power\Menu;
use Laravel\Crbac\Models\Power\Item;
use Illuminate\Support\Facades\Request;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;

class Rbac {

    private $app; //Illuminate\Container\Container
    private $admin; //Illuminate\Contracts\Auth\Authenticatable
    private $menus; //菜单数据
    private $crumbs; //面包屑数据

    /**
     * 初始化
     * @param Container $app
     */
    public function __construct(Container $app) {
        $this->app = $app;
    }

    /**
     * 设置授权人员
     * @param UserContract $admin
     */
    public function setAdmin(UserContract $admin = null) {
        $this->admin = $admin;
    }

    /**
     * 获取授权人员
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function getAdmin() {
        return $this->admin;
    }

    /**
     * 判断用户是否有权限访问
     * @param string $code
     * @param bool $noneDefault
     * @return bool
     */
    public function allow(string $code, bool $noneDefault = false) {
        return Item::allow($this->admin, $code, $noneDefault);
    }

    /**
     * 获取当前用户菜单数据
     * @return array
     */
    public function menus() {
        if (is_array($this->menus)) {
            return $this->menus;
        }
        $menus = Menu::menus($this->admin); //取出用户所有菜单
        list($this->menus, $this->crumbs) = $this->group($menus);
        return $this->menus;
    }

    /**
     * 获取当前用户面包屑数据
     * @return array
     */
    public function crumbs() {
        if (!$this->crumbs) {
            $this->menus();
        }
        return $this->crumbs;
    }

    /**
     * 整理菜单并取出当前菜单
     * @param Illuminate\Database\Eloquent\Collection $lists
     * @return array
     */
    private function group($lists) {
        $menus = [];
        $current_action_menu = []; //当前菜单结构
        $current_controller_menu = []; //当前菜单结构
        $referer_menu = []; //上页的菜单结构
        foreach ($lists as $item) {
            $menus[$item['parent_id']][$item['level_id']] = $item; //追加菜单层级数据
            if ($this->isCurrentAction($item)) {//是这个请求
                $current_action_menu[] = $item;
            }
            if ($this->isCurrentController($item)) {//是这个请求
                $current_controller_menu[] = $item;
            }
            if ($this->isPreviousUrl($item)) {
                $referer_menu[] = $item;
            }
        }
        return [$menus, $this->parseCrumbs($lists, $current_action_menu, $current_controller_menu, $referer_menu)];
    }

    /**
     * 判断是否为当前方法
     * @staticvar boolean $uses
     * @param Menu $menu
     * @return bool
     */
    private function isCurrentAction(Menu $menu) {
        static $uses = false;
        if ($uses === false) {
            $uses = currentRouteUses();
        }
        $url = $menu['url'];
        if ($menu['url'] && strpos('?', $menu['url']) !== false) {
            $url = strstr('?', $url, true);
        }
        return Request::is(trim($url, '/')) && (empty($menu['item']) || strcasecmp($menu['item']['code'], $uses) === 0);
    }

    /**
     * 判断是否为当前控制器
     * @staticvar boolean $controller
     * @param Menu $menu
     * @return bool
     */
    private function isCurrentController(Menu $menu) {
        static $controller = false;
        if ($controller === false) {
            $uses = currentRouteUses();
            $controller = explode('@', $uses)[0] . '@';
        }
        return $menu['item'] && strpos($menu['item']['code'], $controller) === 0;
    }

    /**
     * 判断是否为上一页面地址
     * @staticvar boolean $referer
     * @param Menu $menu
     * @return bool
     */
    private function isPreviousUrl(Menu $menu) {
        static $referer = false;
        if ($referer === false) {
            $referer = urldecode((string) URL::previous());
            $referer = $referer ? parse_url($referer, PHP_URL_PATH) : '/';
        }
        return $referer && Str::is($menu['url'], $referer);
    }

    /**
     * 解析出当前请求面包屑
     * @param Illuminate\Database\Eloquent\Collection $lists
     * @param array $action_menu
     * @param array $controller_menu
     * @param array $referer_menu
     * @return array
     */
    private function parseCrumbs($lists, $action_menu, $controller_menu, $referer_menu) {
        $lists = $lists->keyBy('level_id');
        //优先处理当前action
        $parents_level = [];
        if (count($action_menu)) {//有当前处理action
            do {
                $menu = array_pop($action_menu);
                if (!in_array($menu->level_id, $parents_level)) {//自己不在上级则取自己的上级
                    $parents_level = $this->parentsLevel($lists, $menu);
                }
            } while (count($action_menu));
        } elseif (count($controller_menu)) {//有当前处理controller
            $menus = [];
            do {
                $menu = array_pop($controller_menu);
                if (!in_array($menu->level_id, $parents_level)) {//自己不在上级则取自己的上级
                    $parents_level = $this->parentsLevel($lists, $menu);
                    $menus[explode('@', $menu->item->code)[1]] = $menu;
                }
            } while (count($controller_menu));
            if (isset($menus['lists'])) {//优先取列表
                $menu = $menus['lists'];
            } elseif (isset($menus['index'])) {
                $menu = $menus['index'];
            } elseif (isset($menus['getIndex'])) {
                $menu = $menus['getIndex'];
            } elseif (isset($menus['anyIndex'])) {
                $menu = $menus['anyIndex'];
            } else {
                $menu = array_pop($menus);
            }
        } elseif (count($referer_menu)) {//有上一个页面请求的地址
            do {
                $menu = array_pop($referer_menu);
                if (!in_array($menu->level_id, $parents_level)) {//自己不在上级则取自己的上级
                    $parents_level = $this->parentsLevel($lists, $menu);
                }
            } while (count($referer_menu));
        } else {//真的是找不到了
            $menu = Arr::first($lists, function($item) {
                return $item->parent_id == 0;
            });
        }
        //生成面包屑
        $crumbs = [];
        if ($menu) {
            $crumbs[] = $menu;
        }
        foreach ($parents_level as $level_id) {
            $crumbs[] = $lists[$level_id];
        }
        return array_reverse($crumbs);
    }

    /**
     * 取出菜单上级结构
     * @param Illuminate\Database\Eloquent\Collection $lists
     * @param Menu $menu
     * @return array
     */
    private function parentsLevel($lists, Menu $menu) {
        $levels = [];
        while ($menu->parent_id > 0) {//有上级
            if ($lists->has($menu->parent_id)) {
                $levels[] = $menu->parent_id; //取出上级
                $menu = $lists[$menu->parent_id];
            } else {
                break;
            }
        }
        return $levels;
    }

}
