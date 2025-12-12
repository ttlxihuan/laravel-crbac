<?php

/*
 * 路径路由处理器
 */

namespace Laravel\Crbac;

use Exception;
use ReflectionClass;
use ReflectionMethod;
use Illuminate\Routing\Route;
use Illuminate\Routing\Events\RouteMatched;
use Laravel\Crbac\Annotation\Request\Methods;
use Laravel\Crbac\Annotation\Request\Middleware;
use Illuminate\Support\Facades\Route as RouteFacade;

class PathRouter {

    /**
     * 当前实例
     * @var self
     */
    private static self $instance;

    /**
     * 添加的路由集合
     * @var array
     */
    protected array $routes = [];

    /**
     * 路由器原始参数
     * @var array
     */
    protected array $parameters = [];

    /**
     * 初始化处理
     */
    private function __construct() {
        RouteFacade::matched(function (RouteMatched $matched) {
            $route = $matched->route;
            $name = $route->getName();
            if (isset($this->routes[$name])) {
                $controllerParam = $route->parameter('controller');
                $actionParam = $route->parameter('action');
                //必需合法
                if (!preg_match('#^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*([/\-\.][a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)+$#', $controllerParam . '/' . $actionParam)) {
                    return;
                }
                $this->parameters = $route->parameters();
                if (is_callable($this->routes[$name])) {
                    call_user_func($this->routes[$name], $this, $route);
                } else {
                    $this->updateRouteAction($route);
                }
            }
        });
    }

    /**
     * 判断是否存在
     * @param string $name
     * @return bool
     */
    public function has(string $name) {
        return isset($this->routes[$name]);
    }

    /**
     * 获取当前匹配的路由参数
     * @return array
     */
    public function parameters() {
        return $this->parameters;
    }

    /**
     * 获取当前实例
     * @return self
     */
    public static function instance(): self {
        if (empty(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * 添加路由
     * @param string $name
     * @param string $path
     * @param string $namespace
     * @param callable $callback
     * @return Route
     */
    public function addRoute(string $name, string $path, string $namespace = 'App\\Http\\Controllers', $callback = null) {
        if (strpos($path, '{controller}') === false) {
            throw new Exception('Route path must specify {controller}');
        }
        if (strpos($path, '{action}') === false) {
            throw new Exception('Route path must specify {action}');
        }
        $this->routes[$name] = is_callable($callback) ? $callback : true;
        //所有其它路由不可在这条之后添加
        return RouteFacade::any($path . '/{one?}/{two?}/{three?}/{four?}/{five?}', [
                            'namespace' => $namespace,
                            'as' => $name,
                            'uses' => function () {
                                return abort(404);
                            }])
                        ->where('controller', '(.*)');
    }

    /**
     * 获取按路径的url地址
     * @param string $controller
     * @param string $action
     * @param array $params
     * @return string|null
     */
    public function getOptions(string $controller, string $action, array $params = []) {
        $method = new ReflectionMethod($controller, $action);
        if (!$method->isPublic()) {
            return;
        }
        $annotations = get_annotations($method, Methods::class, Middleware::class);
        if (empty($annotations[Methods::class])) {
            return;
        }
        $class = new ReflectionClass($controller);
        $namespace = trim($class->getNamespaceName(), '\\');
        $routes = RouteFacade::getRoutes();
        foreach ($this->routes as $name => $_) {
            $route = $routes->getByName($name);
            if ($route && 0 === $pos = stripos($namespace, trim($route->getAction()['namespace'], '\\') . '\\')) {
                $tmp = [];
                foreach (array_merge(explode('\\', substr($namespace, $pos)), [preg_replace('/Controller$/', '', $class->getName()), $action]) as $item) {
                    $tmp[] = trim(preg_replace_callback('#[A-Z]+#', function ($val) {
                                return '-' . strtolower($val[0]);
                            }, $item), '-');
                }
                $act = array_pop($tmp);
                $url = route($name, array_merge([implode('/', $tmp), $act], $params));
                $methods = [];
                foreach ($annotations[Methods::class] as $method) {
                    $methods = array_merge($methods, $method->get());
                }
                $middlewares = $this->getMiddlewares($controller, $annotations[Middleware::class] ?? [], $route->getAction()['middleware'] ?? []);
                return [
                    'name' => $name,
                    'url' => $url,
                    'methods' => array_unique($methods),
                    'middlewares' => $middlewares
                ];
            }
        }
    }

    /**
     * 更新路由动作处理配置
     * @param Route $route
     * @return void
     */
    public function updateRouteAction(Route $route) {
        if (empty($this->routes[$route->getName()])) {
            return;
        }
        //控制器与方法解析匹配
        $space = explode('/', $route->parameter('controller'));
        $namecpase = $route->getAction()['namespace'] ?? 'App\\Http\\Controllers';
        $controller = rtrim($namecpase, '\\') . '\\' . implode('\\', array_map('studly_case', array_filter($space))) . 'Controller';
        $action = studly_case($route->parameter('action'));
        if (!method_exists($controller, $action)) {
            return;
        }
        $method = new ReflectionMethod($controller, $action);
        if (!$method->isPublic()) {
            return;
        }
        $annotations = get_annotations($method, Methods::class, Middleware::class);
        foreach ($annotations[Methods::class] ?? [] as $methods) {
            if ($methods->is(request()->method())) {
                //路由参数处理
                $keys = array_keys($route->parameters());
                $keys = array_slice($keys, array_search('one', $keys));
                $setParameters = [];
                foreach ($method->getParameters() as $num => $parameter) {
                    if (isset($keys[$num], $route->parameters[$keys[$num]])) {
                        $setParameters[$parameter->name] = $route->parameters[$keys[$num]];
                    } elseif ($parameter->isDefaultValueAvailable()) {
                        $setParameters[$parameter->name] = $parameter->getDefaultValue();
                    } else {
                        return;
                    }
                }
                $route->parameters = $setParameters;
                $actions = $route->getAction();
                $actions['uses'] = $controller . '@' . $action;
                $actions['controller'] = $actions['uses'];
                // 获取中间件配置
                $actions['middleware'] = $this->getMiddlewares($controller, $annotations[Middleware::class] ?? [], $actions['middleware'] ?: []);
                $route->setAction($actions);
                return;
            }
        }
    }

    /**
     * 获取对应的中间件信息
     * @param string $controller
     * @param array $actions
     * @param array $middlewares
     * @return array
     */
    protected function getMiddlewares(string $controller, array $actions, array $middlewares = []) {
        // 向上递归
        foreach ($this->eachControllerAnnotations(new ReflectionClass($controller), Middleware::class) as $_middlewares) {
            foreach ($_middlewares[Middleware::class] ?? [] as $middleware) {
                $middlewares = array_merge($middlewares, $middleware->get());
            }
        }
        foreach ($actions as $middleware) {
            $middlewares = array_merge($middlewares, $middleware->get());
        }
        return array_unique($middlewares);
    }

    /**
     * 循环控制器的注解数据
     * @param ReflectionClass $reflector
     * @param string $annotationClass
     */
    protected function eachControllerAnnotations(ReflectionClass $reflector, string ...$annotationClass) {
        yield get_annotations($reflector, ...$annotationClass);
        while ($parent = $reflector->getParentClass()) {
            yield get_annotations($parent, ...$annotationClass);
            $reflector = $parent;
        }
    }
}
