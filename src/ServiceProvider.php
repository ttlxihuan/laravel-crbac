<?php

namespace XiHuan\Crbac;

use Auth,
    Crbac;
use Illuminate\Http\Response;
use Illuminate\Foundation\AliasLoader;
use XiHuan\Crbac\Console\CrbacTableCommand;
use XiHuan\Crbac\Console\CrbacTableSeederCommand;

class ServiceProvider extends \Illuminate\Support\ServiceProvider {

    //指示是否将提供程序的加载推迟。
    protected $defer = false;

    /*
     * 作用：注册服务供应商
     * 参数：无
     * 返回值：void
     */
    public function register() {
        $this->app['crbac'] = $this->app->share(function ($app) {//取菜单，判断是否有权限
            return new Rbac($app);
        });
        $this->app['events']->listen('auth.login', function($admin) {
            Crbac::setAdmin($admin);
        });
        $this->app['events']->listen('auth.logout', function() {
            Crbac::setAdmin();
        });
        AliasLoader::getInstance(['Crbac' => Facade::class]);
        $this->registerCommands();
    }
    /*
     * 作用：引导应用程序事件
     * 参数：无
     * 返回值：void
     */
    public function boot() {
        $this->app['config']->push('view.paths', __DIR__ . '/Resources/views');
        $router = $this->app['router'];
        $this->addRouteFilter($router);
        $this->addRoutes($router);
    }
    /*
     * 作用：添加路由过滤处理
     * 参数：$router Illuminate\Routing\Router
     * 返回值：void
     */
    protected function addRouteFilter($router) {
        $router->matched(function($route) {
            $action = $route->getAction();
            if (in_array($route->getName(), ['power.static.css', 'power.static.js'])) {
                $action['middleware'] = [];
            } elseif (isset($action['uses']) && is_string($action['uses']) && Auth::check()) {//未作登录的不做权限验证处理
                Crbac::setAdmin($this->app['auth']->user());
                $action['before'][] = 'power_check';
                $action['before'][] = 'power_code';
            }
            $route->setAction($action);
        });
        $router->filter('power_code', function($route, $request) {
            if ($request->ajax() && $request->header('GET-ROUTER-USERS') === 'true') {
                $uses = currentRouteUses();
                $item = $uses ? Models\Power\Item::findCode($uses) : null;
                if ($item) {
                    $data = array_only($item->toArray(), ['code', 'status', 'power_item_group_id']);
                    $data['power_item_group_name'] = array_get($item->group->toArray(), 'name');
                    $data['roles'] = array_pluck($item->roles, 'name', 'power_role_id') ?: [];
                    $item = $data;
                }
                return prompt(compact('uses', 'item'));
            }
        });
        $router->filter('power_check', function() {
            $power = isControllerPower(null, null, true);
            if (!$power) {
                return prompt('没有权限操作', 'error');
            }
        });
    }
    /*
     * 作用：添加路由
     * 参数：$router Illuminate\Routing\Router
     * 返回值：void
     */
    protected function addRoutes($router) {
        $routeConfig = [
            'namespace' => 'XiHuan\Crbac\Controllers\Power',
            'prefix' => 'power/',
            'middleware' => ['auth'],
        ];
        $router->group($routeConfig, function($router) {
            //权限项相关
            $this->addPowerItemRoute($router);
            //角色相关
            $this->addPowerRoleRoute($router);
            //菜单相关
            $this->addPowerMenuRoute($router);
            //菜单组相关
            $this->addPowerMenuGroupRoute($router);
            //权限项组相关
            $this->addPowerItemGroupRoute($router);
            //管理员相关
            $this->addPowerAdminRoute($router);
        });
        //静态文件
        $this->addStaticRoute($router);
        //判断是否存在
        $router->get('usable/{model}/{field}.html', ['uses' => function(\Illuminate\Http\Request $request, $model, $field) {
                $service = new Services\ExistService();
                $val = $request->input($field);
                return $val && $service->check($model, $field, $val, $request->input('id')) ? 'false' : 'true';
            }, 'as' => 'exist_validate'])->where(['model' => '((\\w+)(/\\w+)*)', 'field' => '\\w+']);
    }
    /*
     * 作用：添加权限项路由
     * 参数：$router Illuminate\Routing\Router
     * 返回值：void
     */
    protected function addPowerItemRoute($router) {
        //权限项相关
        $this->addRouteResource('item', 'ItemController', $router, Models\Power\Item::class, ['select']);
        //路由处理
        $router->get('item/routes.html', 'ItemController@routes'); //现有路由列表
        $router->get('item/update/routes.html', 'ItemController@updateRoutes'); //更新路由列表
    }
    /*
     * 作用：添加角色路由
     * 参数：$router Illuminate\Routing\Router
     * 返回值：void
     */
    protected function addPowerRoleRoute($router) {
        $this->addRouteResource('role', 'RoleController', $router, Models\Power\Role::class);
        $router->get('role/admins/{bind_style}/{power_role}.html', 'RoleController@admins')->where('bind_style', 'bind|unbind'); //角色下管理员列表
        $router->match(['get', 'post'], 'role/items/{power_role}.html', 'RoleController@items'); //角色下权限项编辑
        $router->get('role/admin/remove/{power_role}/{admin}.html', 'RoleController@removeAdmin'); //角色下移除管理员
        $router->get('role/admin/add/{power_role}/{admin}.html', 'RoleController@addAdmin'); //角色下添加管理员
    }
    /*
     * 作用：添加菜单路由
     * 参数：$router Illuminate\Routing\Router
     * 返回值：void
     */
    protected function addPowerMenuRoute($router) {
        $this->addRouteResource('menu', 'MenuController', $router, Models\Power\Menu::class);
    }
    /*
     * 作用：添加菜单组路由
     * 参数：$router Illuminate\Routing\Router
     * 返回值：void
     */
    protected function addPowerMenuGroupRoute($router) {
        $this->addRouteResource('group/menu', 'MenuGroupController', $router, Models\Power\MenuGroup::class);
        $router->match(['get', 'post'], 'group/menu/level/{power_group_menu}.html', 'MenuGroupController@menus'); //菜单组下菜单层级编辑
        $router->get('group/menu/select/level/{power_group_menu}.html', 'MenuGroupController@levelOption'); //菜单组下菜单层级列表连动处理
        $router->get('group/menu/copy/{power_group_menu}.html', 'MenuGroupController@copy'); //复制菜单组
        $router->model('copy_power_group_menu', Models\Power\MenuGroup::class);
        $router->match(['get', 'post'], 'group/menu/pasted/{copy_power_group_menu}/{power_group_menu}.html', 'MenuGroupController@pasted'); //粘贴菜单组
    }
    /*
     * 作用：添加权限项组路由
     * 参数：$router Illuminate\Routing\Router
     * 返回值：void
     */
    protected function addPowerItemGroupRoute($router) {
        $this->addRouteResource('group/item', 'ItemGroupController', $router, Models\Power\ItemGroup::class);
    }
    /*
     * 作用：添加管理员路由
     * 参数：$router Illuminate\Routing\Router
     * 返回值：void
     */
    protected function addPowerAdminRoute($router) {
        $authModel = auth_model();
        $authModel::saving(function(Models\Admin $model) {
            $model->savePassword();
        });
        $this->addRouteResource('admin', 'AdminController', $router, $authModel, ['delete', 'select']);
        //修改密码
        $router->match(['get', 'post'], 'admin/update/password.html', ['uses' => 'AdminController@password', 'as' => 'update_admin_password']);
    }
    /*
     * 作用：添加静态文件路由
     * 参数：$router Illuminate\Routing\Router
     * 返回值：void
     */
    protected function addStaticRoute($router) {
        $router->pattern('static_file', '(.*)');
        foreach (['css' => 'text/css', 'js' => 'text/javascript'] as $ext => $type) {
            $router->get('static/' . $ext . '/{static_file}.' . $ext, ['uses' => function($static_file)use($ext, $type) {//获取文件
                    return $this->cacheResponse($ext . '/' . $static_file . '.' . $ext, $type);
                }, 'as' => 'power.static.' . $ext]);
        }
        $router->get('static/img/{static_file}.{file_ext}', ['uses' => function($static_file, $file_ext) {//获取文件
                        return $this->cacheResponse('img/' . $static_file . '.' . $file_ext, 'image/' . $file_ext);
                    }, 'as' => 'power.static.img'])
                ->where('file_ext', 'png|gif');
    }
    /**
     * Cache the response 1 year (31536000 sec)
     */
    /*
     * 作用：静态文件响应
     * 参数：$file 静态文件
     *      $contentType 文件类型
     * 返回值：Illuminate\Http\Response
     */
    protected function cacheResponse($file, $contentType) {
        $filePath = __DIR__ . '/Resources/' . $file;
        $response = new Response(
                file_get_contents($filePath), 200, array('Content-Type' => $contentType,)
        );
        $response->setSharedMaxAge(31536000);
        $response->setMaxAge(31536000);
        $response->setExpires(new \DateTime('+1 year'));
        return $response;
    }
    /*
     * 作用：添加指定路由
     * 参数：$name string 目录名
     *      $controller string 控制器类名
     *      $router Illuminate\Routing\Router
     *      $model string Model类名
     *      $excepts array 排除指定路由
     * 返回值：void
     */
    protected function addRouteResource($name, $controller, $router, $model, $excepts = []) {
        $resource = ['lists', 'add', 'edit', 'delete', 'select'];
        $as = 'power.' . str_replace(['/', '-', '_'], '.', $name);
        $router->model(str_replace('.', '_', $as), $model);
        foreach (array_diff($resource, $excepts) as $action) {
            $function = 'addRoute' . $action;
            $this->$function($router, $name, $as, $controller);
        }
    }
    /*
     * 作用：添加列表路由
     * 参数：$router Illuminate\Routing\Router
     *      $name string  路径
     *      $as string 别名
     *      $controller string 控制器类名
     * 返回值：void
     */
    protected function addRouteLists($router, $name, $as, $controller) {
        $router->get($name . '/lists.html', ['uses' => $controller . '@lists', 'as' => $as . '.lists']);
    }
    /*
     * 作用：添加创建路由
     * 参数：$router Illuminate\Routing\Router
     *      $name string  路径
     *      $as string 别名
     *      $controller string 控制器类名
     * 返回值：void
     */
    protected function addRouteAdd($router, $name, $as, $controller) {
        $router->match(['get', 'post'], $name . '/add.html', ['uses' => $controller . '@add', 'as' => $as . '.add']);
    }
    /*
     * 作用：添加编辑路由
     * 参数：$router Illuminate\Routing\Router
     *      $name string  路径
     *      $as string 别名
     *      $controller string 控制器类名
     * 返回值：void
     */
    protected function addRouteEdit($router, $name, $as, $controller) {
        $router->match(['get', 'post'], $name . '/edit/{' . str_replace('.', '_', $as) . '}.html', ['uses' => $controller . '@edit', 'as' => $as . '.edit']);
    }
    /*
     * 作用：添加删除路由
     * 参数：$router Illuminate\Routing\Router
     *      $name string  路径
     *      $as string 别名
     *      $controller string 控制器类名
     * 返回值：void
     */
    protected function addRouteDelete($router, $name, $as, $controller) {
        $router->get($name . '/delete/{' . str_replace('.', '_', $as) . '}.html', ['uses' => $controller . '@delete', 'as' => $as . '.delete']);
    }
    /*
     * 作用：添加快捷选择路由
     * 参数：$router Illuminate\Routing\Router
     *      $name string  路径
     *      $as string 别名
     *      $controller string 控制器类名
     * 返回值：void
     */
    protected function addRouteSelect($router, $name, $as, $controller) {
        $router->get($name . '/select/{select_relation}.html', ['uses' => $controller . '@select', 'as' => $as . '.select']);
    }
    /*
     * 作用：注册所有的迁移命令
     * 参数：无
     * 返回值：void
     */
    protected function registerCommands() {
        $this->app->singleton('crbac.table', function() {
            return new CrbacTableCommand();
        });
        $this->app->singleton('crbac.seeder', function() {
            return new CrbacTableSeederCommand();
        });
        $this->commands('crbac.table', 'crbac.seeder');
    }
}
