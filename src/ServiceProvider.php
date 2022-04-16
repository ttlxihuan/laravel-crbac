<?php

namespace Laravel\Crbac;

use Crbac;
use Illuminate\Http\Response;
use Illuminate\Foundation\AliasLoader;
use Laravel\Crbac\Console\CrbacTableCommand;
use Laravel\Crbac\Console\CrbacTableSeederCommand;

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
        // 如果没有绑定登录则自动追加
        $this->app->booted(function () {
            // 添加特定路由配置
            $this->app['router']->group([
                'namespace' => 'Laravel\Crbac\Controllers\Power',
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
        // 追加专用目录，如果在原来的目录中存在相关视图文件，则此目录无效
        view()->addLocation(realpath(__DIR__ . '/../views'));
        // 添加特定路由配置
        $router = $this->app['router'];
        //通用公用路由
        $router->any('{type}/{ctr}.{act}/{one?}/{two?}/{three?}/{four?}/{five?}', [
                    'namespace' => 'Laravel\Crbac\Controllers\Power',
                    'prefix' => 'crbac/',
                    'as' => 'mvc-crbac',
                    'uses' => function () {
                        return $this->response();
                    }])
                ->where('type', 'power|static|usable')
                ->where('ctr', '(.*)');
        //路由匹配处理，主要针对其它路由进行权限处理
        $router->matched(function () {
            $route = request()->route();
            $action = $route->getAction();
            if (empty($action['middleware'])) {
                $action['middleware'] = [];
            }
            if (isset($action['as']) && $action['as'] == 'mvc-crbac' && $route->parameter('type') == 'power') {
                $action['middleware'] = $this->getAuthMiddleware();
            }
            // 有授权则追加权限中间件
            foreach ($action['middleware'] as $middleware) {
                if ($middleware == 'auth' || strpos($middleware, 'auth:')) {
                    array_push($action['middleware'], Middleware\PowerAuthenticate::class);
                    $route->setAction($action);
                    break;
                }
            }
        });
    }

    /**
     * 获取中间件
     */
    protected function getAuthMiddleware($auth = 'auth') {
        $middleware = [$auth];
        if (version_compare(app()->version(), '5.2.0', '>=')) {
            array_unshift($middleware, 'web');
        }
        return $middleware;
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
     * 权限处理相关响应
     * @return mixed
     */
    protected function response() {
        $route = request()->route();
        $type = $route->parameter('type');
        $controllerParam = $route->parameter('ctr');
        $actionParam = $route->parameter('act');
        //必需合法
        if (preg_match('#^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*([/\-\.][a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)+$#', $controllerParam . '/' . $actionParam)) {
            //控制器与方法解析匹配
            switch ($type) {
                case 'static':
                    $action = $this->responseStatic($controllerParam, $actionParam);
                    break;
                case 'power':
                    $action = $this->responsePower($controllerParam, $actionParam);
                    break;
                case 'usable':
                    $service = new Services\ExistService();
                    $val = request()->input($actionParam);
                    return $val && $service->check($controllerParam, $actionParam, $val, request()->input('id')) ? 'false' : 'true';
                default:
                    $action = null;
                    break;
            }
            if (is_array($action)) {
                $route->setAction(array_merge($route->getAction(), $action));
                return $route->run();
            }
        }
        return abort(404);
    }

    /**
     * 权限静态文件响应处理
     * @param string $controllerParam
     * @param string $actionParam
     * @return mixed
     */
    protected function responseStatic(string $controllerParam, string $actionParam) {
        $file = __DIR__ . "/../static/$controllerParam.$actionParam";
        $types = ['css' => 'text/css', 'js' => 'text/javascript'];
        if (file_exists($file) && isset($types[$actionParam])) {
            $contentType = $types[$actionParam];
            return [
                'uses' => function ()use ($file, $contentType) {
                    $response = new class($file, 200, ['Content-Type' => $contentType]) extends Response {

                        private $file;

                        public function __construct($file, int $status = 200, array $headers = array()) {
                            parent::__construct('', $status, $headers);
                            $this->file = $file;
                        }

                        public function sendContent() {
                            readfile($this->file);
                        }
                    };
                    $response->setSharedMaxAge(31536000);
                    $response->setMaxAge(31536000);
                    $response->setExpires(new \DateTime('+1 year'));
                    return $response;
                }
            ];
        }
    }

    /**
     * 权限处理响应
     * @param string $controllerParam
     * @param string $actionParam
     * @return mixed
     */
    protected function responsePower(string $controllerParam, string $actionParam) {
        $space = explode('/', $controllerParam);
        $controller = 'Laravel\\Crbac\\Controllers\\Power\\' . implode('\\', array_map('studly_case', array_filter($space))) . 'Controller';
        $action = studly_case($actionParam);
        if (!method_exists($controller, $action)) {
            return;
        }
        $method = new \ReflectionMethod($controller, $action);
        $comment = $method->getDocComment();
        if (!$method->isPublic() || !preg_match('/@methods\(\s*(GET|HEAD|POST|PUT|PATCH|DELETE|OPTIONS)(\s*,\s*(GET|HEAD|POST|PUT|PATCH|DELETE|OPTIONS)+)*\s*\)/i', $comment, $matches)) {
            return;
        }
        $methods = array_map(function ($item) {
            return trim(ltrim($item, ','));
        }, array_slice($matches, 1));
        if (!in_array(request()->method(), $methods)) {
            return;
        }
        //路由参数处理
        $route = request()->route();
        $parameters = array_except($route->parameters, ['type', 'ctr', 'act']);
        $parametersKeys = array_keys($parameters);
        array_push($parameters);
        foreach ($method->getParameters() as $num => $parameter) {
            if (empty($parametersKeys[$num])) {
                break;
            }
            $key = $parametersKeys[$num];
            $class = $parameter->getClass();
            if ($class) {
                $object = $this->app->make($class->name);
                if (is_subclass_of($class->name, \Illuminate\Database\Eloquent\Model::class)) {
                    $object = $object->find($parameters[$key]);
                }
                if (!$object) {
                    return;
                }
                $parameters[$key] = $object;
            }
        }
        $route->parameters = $parameters;
        return [
            'uses' => $controller . '@' . $action,
            'controller' => $controller . '@' . $action
        ];
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
        $this->commands('crbac.table', 'crbac.seeder');
    }

}
