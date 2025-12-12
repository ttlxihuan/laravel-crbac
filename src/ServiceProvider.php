<?php

namespace Laravel\Crbac;

use Crbac;
use Illuminate\Routing\Route;
use Illuminate\Http\Response;
use Illuminate\Foundation\AliasLoader;
use Laravel\Crbac\Console\CrbacTableCommand;
use Laravel\Crbac\Console\CrbacTableSeederCommand;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;

class ServiceProvider extends \Illuminate\Support\ServiceProvider {

    //指示是否将提供程序的加载推迟。
    protected $defer = false;

    /**
     * 注册服务
     */
    public function register() {
        $this->app->singleton('crbac', function ($app) {//取菜单，判断是否有权限
            return new Rbac($app);
        });
        $this->app['events']->listen('auth.login', function ($admin) {
            Crbac::setAdmin($admin);
        });
        $this->app['events']->listen('auth.logout', function () {
            Crbac::setAdmin();
        });
        AliasLoader::getInstance(['Crbac' => Facade::class]);
        $this->registerCommands();
    }

    /**
     * 服务初始引导处理
     */
    public function boot() {
        // 没有开启支持授权处理即跳过
        if (!$this->hasPowerAuthGuard()) {
            return;
        }
        $this->addRoute();
    }

    /**
     * 添加rbac路由
     */
    protected function addRoute() {
        //通用公用路由
        PathRouter::instance()->addRoute('mvc-crbac', '/crbac/{type}/{controller}.{action}', 'Laravel\\Crbac\\Controllers\\Power', [$this, 'routeAction'])->where('type', 'power|static|usable');
        // 如果没有绑定登录则自动追加
        $this->app->booted(function () {
            // 添加特定路由配置
            $this->app['router']->group([
                'namespace' => 'Laravel\\Crbac\\Controllers\\Power',
                'prefix' => 'crbac/',
                    ], function ($router) {
                        if (!$router->has('logout')) {
                            $router->get('logout', ['uses' => 'AdminController@logout', 'as' => 'logout', 'middleware' => $this->getAuthMiddleware()]);
                        }
                        if (!$router->has('login')) {
                            $router->match(['GET', 'POST'], 'login', ['uses' => 'AdminController@login', 'as' => 'login', 'middleware' => $this->getAuthMiddleware('guest')]);
                        }
                    });
        });
    }

    /**
     * 消除 \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull 中间件将空请求数据修改为null
     * 此函数主要是兼容目前结构和框架，结构中不允许有null但这个中间件会强制修改为null，函数通过逆向修改为空
     * 为null主要会影响到验证必填，请求默认值重写等
     */
    protected function requestConvertRestore() {
        $this->requestConvertRestore();
        if (class_exists(ConvertEmptyStringsToNull::class)) {
            $restore = new class() extends ConvertEmptyStringsToNull {

                protected function transform($key, $value) {
                    return is_null($value) ? '' : $value;
                }
            };
            $restore->handle($this->app['request'], function () {
                
            });
        }
    }

    /**
     * 获取中间件
     */
    protected function getAuthMiddleware($auth = 'auth') {
        $middlewares = [$auth, Middleware\PowerAuthenticate::class];
        if (version_compare(app()->version(), '5.2.0', '>=')) {
            array_unshift($middlewares, 'web');
        }
        return $middlewares;
    }

    /**
     * 是否存在支持授权处理
     * @return bool
     */
    protected function hasPowerAuthGuard() {
        // 不同版本有差异
        $config = $this->app['config'];
        if (method_exists($this->app['auth'], 'guard')) {
            // 多授权模型配置
            foreach ($config['auth.guards'] ?? [] as $guard) {
                $provider = $guard['provider'] ?? null;
                if ($provider && $config["auth.providers.$provider.driver"] == 'eloquent') {
                    $model = $config["auth.providers.$provider.model"];
                    if ($model && class_exists($model) && is_a(new $model, Models\Power\Admin::class)) {
                        return true;
                    }
                }
            }
        } else {
            // 单授权模型配置
            $model = $config['auth.model'];
            if ($model && class_exists($model) && is_a(new $model, Models\Power\Admin::class)) {
                return true;
            }
        }
        return false;
    }

    /**
     * 生成专用路由处理器
     * @param PathRouter $pathRouter
     * @param Route $route
     * @return type
     */
    public function routeAction(PathRouter $pathRouter, Route $route) {
        //控制器与方法解析匹配
        $middlewares = $route->getAction()['middleware'] ?? [];
        $controllerParam = $route->parameter('controller');
        $actionParam = $route->parameter('action');
        switch (strtolower($route->parameter('type'))) {
            case 'power':
                $action = [
                    'middleware' => array_merge($middlewares, $this->getAuthMiddleware())
                ];
                $route->setAction(array_merge($route->getAction(), $action));
                $pathRouter->updateRouteAction($route);
                break;
            case 'usable':
                $action = [
                    'uses' => function ()use ($controllerParam, $actionParam) {
                        $service = new Services\ExistService();
                        $val = request()->input($actionParam);
                        return $val && $service->check($controllerParam, $actionParam, $val, request()->input('id')) ? 'false' : 'true';
                    },
                    'middleware' => array_unique(array_merge($middlewares, $this->getAuthMiddleware()))
                ];
                break;
            case 'static':
                $file = __DIR__ . "/../static/$controllerParam.$actionParam";
                $types = ['css' => 'text/css', 'js' => 'text/javascript', 'png' => 'image/png', 'gif' => 'image/gif'];
                if (file_exists($file) && isset($types[$actionParam])) {
                    $contentType = $types[$actionParam];
                    $action = [
                        'uses' => function ()use ($file, $contentType) {
                            $response = new Response(file_get_contents($file), 200, ['Content-Type' => $contentType]);
                            $response->setSharedMaxAge(31536000);
                            $response->setMaxAge(31536000);
                            $response->setExpires(new \DateTime('+1 year'));
                            return $response;
                        }
                    ];
                    break;
                }
            default:
                return;
        }
        $route->setAction(array_merge($route->getAction(), $action));
    }

    /**
     * 注册所有命令
     */
    protected function registerCommands() {
        $this->app->singleton('crbac.table', function () {
            return new CrbacTableCommand();
        });
        $this->app->singleton('crbac.seeder', function () {
            return new CrbacTableSeederCommand();
        });
        $this->app->singleton('crbac.lang', function () {
            return new Console\CrbacCopyLangCommand();
        });
        $this->app->singleton('crbac.power', function () {
            return new Console\CrbacUpdatePowerCommand();
        });
        $this->commands('crbac.table', 'crbac.seeder', 'crbac.lang', 'crbac.power');
    }
}
