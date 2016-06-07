<?php

/*
 * 权限管理对外基本操作
 */

namespace XiHuan\Crbac;

use Request,
    URL,
    Illuminate\Support\Str;
use Illuminate\Container\Container;
use XiHuan\Crbac\Models\Power\Menu;
use XiHuan\Crbac\Models\Power\Item;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;

class Rbac {

    private $app; //Illuminate\Container\Container
    private $admin; //Illuminate\Contracts\Auth\Authenticatable
    private $menus; //菜单数据
    private $crumbs; //面包屑数据

    /*
     * 作用：初始化
     * 参数：$app Illuminate\Container\Container
     * 返回值：void
     */
    public function __construct(Container $app) {
        $this->app = $app;
    }
    /*
     * 作用：获取当前用户Model类名
     * 参数：无
     * 返回值：string
     */
    public function authModel() {
        return $this->app['config']['auth.model'];
    }
    /*
     * 作用：设置登录人员
     * 参数：$admin Illuminate\Contracts\Auth\Authenticatable
     * 返回值：void
     */
    public function setAdmin(UserContract $admin = null) {
        $this->admin = $admin;
    }
    /*
     * 作用：获取登录人员
     * 参数：无
     * 返回值：Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function getAdmin() {
        return $this->admin;
    }
    /*
     * 作用：判断用户是否有权限访问
     * 参数：$code string 权限码
     *       $default bool 如果权限不存在或禁用返回默认值
     * 返回值：bool
     */
    public function allow($code, $default = false) {
        return Item::allow($this->admin, $code, $default);
    }
    /*
     * 作用：获取当前用户菜单数据
     * 参数：无
     * 返回值：array
     */
    public function menus() {
        if ($this->menus) {
            return $this->menus;
        }
        $menus = Menu::menus($this->admin); //取出用户所有菜单
        list($this->menus, $this->crumbs) = $this->group($menus);
        return $this->menus;
    }
    /*
     * 作用：获取当前用户面包屑数据
     * 参数：无
     * 返回值：array
     */
    public function crumbs() {
        if (!$this->crumbs) {
            $this->menus();
        }
        return $this->crumbs;
    }
    /*
     * 作用：整理菜单并取出当前菜单
     * 参数：$lists Illuminate\Database\Eloquent\Collection
     * 返回值：array
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
    /*
     * 作用：判断是否为当前方法
     * 参数：$menu XiHuan\Crbac\Models\Power\Menu
     * 返回值：bool
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
        return Request::is(trim($url, '/')) && (empty($menu['item']) || $menu['item']['code'] === $uses);
    }
    /*
     * 作用：判断是否为当前控制器
     * 参数：$menu XiHuan\Crbac\Models\Power\Menu
     * 返回值：bool
     */
    private function isCurrentController(Menu $menu) {
        static $controller = false;
        if ($controller === false) {
            $uses = currentRouteUses();
            $controller = explode('@', $uses)[0] . '@';
        }
        return $menu['item'] && strpos($menu['item']['code'], $controller) === 0;
    }
    /*
     * 作用：判断是否为上一页面地址
     * 参数：$menu XiHuan\Crbac\Models\Power\Menu
     * 返回值：bool
     */
    private function isPreviousUrl(Menu $menu) {
        static $referer = false;
        if ($referer === false) {
            $referer = urldecode((string) URL::previous());
            $referer = $referer ? parse_url($referer, PHP_URL_PATH) : '/';
        }
        return $referer && Str::is($menu['url'], $referer);
    }
    /*
     * 作用：解析出当前请求面包屑
     * 参数：$lists Illuminate\Database\Eloquent\Collection
     *       $action_menu array 方法相同菜单集
     *       $controller_menu array 控制器相同菜单集
     *       $referer_menu array 上页面相同菜单集
     * 返回值：array
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
            $menu = array_first($lists, function($key, $item) {
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
    /*
     * 作用：取出菜单上级结构
     * 参数：$lists Illuminate\Database\Eloquent\Collection
     *       $menu XiHuan\Crbac\Models\Power\Menu
     * 返回值：array
     */
    private function parentsLevel($lists, Menu $menu) {
        $levels = [];
        while ($menu->parent_id > 0) {//有上级
            $levels[] = $menu->parent_id; //取出上级
            if ($lists->has($menu->parent_id)) {
                $menu = $lists[$menu->parent_id];
            }
        }
        return $levels;
    }
}
