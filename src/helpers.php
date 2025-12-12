<?php

if (!function_exists('prompt')) {

    /**
     * 提示处理
     * @param string|array $message
     * @param string $status
     * @param string|-1 $redirect
     * @param int $timeout
     * @return mixed
     */
    function prompt($message, $status = 'success', $redirect = null, $timeout = 3) {
        if (is_string($message)) {
            $message = ['info' => $message, 'title' => $message];
        }
        $data = compact('status', 'message');
        if ($redirect) {
            if (!Request::ajax()) {
                if ($redirect == -1) {
                    $redirect = URL::previous();
                }
                if ($timeout <= 0) {
                    return redirect($redirect);
                }
            }
            $data += compact('redirect', 'timeout');
        }
        if (Request::ajax()) {
            return response()->json($data);
        } else {
            return response()->view('prompt', $data);
        }
    }

}
if (!function_exists('currentRouteUses')) {

    /**
     * 获取当前路由uses，仅控制器@方法结构
     * @return string
     */
    function currentRouteUses() {
        $uses = app('Illuminate\Routing\Route')->getAction()['uses'] ?? '';
        return is_string($uses) ? $uses : '';
    }

}
if (!function_exists('isPower')) {

    /**
     * 判断用户是否拥有这个权限
     * @param string $code
     * @param bool $noneDefault
     * @return bool
     */
    function isPower($code, $noneDefault = false) {
        return auth()->check() ? Crbac::allow($code, $noneDefault) : false;
    }

}
if (!function_exists('isControllerPower')) {

    /**
     * 判断用户是否拥有这个权限
     * @param string|null $action
     * @param string|null $controller
     * @param bool $noneDefault
     * @return bool
     */
    function isControllerPower($action = null, $controller = null, $noneDefault = false) {
        if (is_null($controller)) {
            $controller = explode('@', currentRouteUses())[0];
        }
        if (empty($action)) {
            $action = explode('@', currentRouteUses())[1] ?? '';
        }
        return $controller && $action ? isPower($controller . '@' . $action, $noneDefault) : $noneDefault;
    }

}
if (!function_exists('isAction')) {

    /**
     * 判断当前路由是否为这个方法
     * @param string $action
     * @return boolean
     */
    function isAction($action) {
        $route = Route::current();
        $uses = $route->getAction()['uses'] ?? '';
        if (empty($uses)) {
            return false;
        }
        if (strpos($action, '@') !== false) {
            return strcasecmp($action, $uses) == 0;
        } else {
            return strcasecmp($action, explode('@', $uses)[1]) == 0;
        }
    }

}

if (!function_exists('lang')) {

    /**
     * 获取翻译内容
     * @param string $key
     * @param array $replace
     * @param string $locale
     * @return string
     */
    function lang($key, array $replace = array(), $locale = null) {
        return app('translator')->get($key, $replace, $locale);
    }

}
if (!function_exists('validate_url')) {

    /**
     * 获取验证数据可用URL地址
     * @staticvar type $service
     * @param string $model
     * @param string $field
     * @return string
     */
    function validate_url($model, $field) {
        static $service = null;
        if (!$service) {
            $service = new Laravel\Crbac\Services\ExistService();
        }
        return $service->toUrl($model, $field);
    }

}

if (!function_exists('crbac_route')) {

    /**
     * 生成路由地址，主要针对mvc-crbac路由专用，允许使用相对路径，路径分隔符使用点
     * 示例：
     *      绝对路径  crbac_route('power.admin.edit', [1])
     *      相对路径  crbac_route('.edit', [1])
     * 相对路径必需保证当前地址是mvc-crbac路由，否则将按标准route()处理
     * 相对路径只能是单层，不支持相对多层，比如： ..edit 是不允许的
     * @param string $name
     * @param array $parameters
     * @param bool $absolute
     * @return string
     */
    function crbac_route(string $name = null, array $parameters = [], $absolute = true) {
        if (is_null($name)) {
            return url()->current();
        }
        $request = request();
        $route = $request->route();
        $pathRouter = Laravel\Crbac\PathRouter::instance();
        if ($pathRouter->has($route->getName()) && preg_match_all('#\{(\w+)\}#', $route->uri(), $matches)) {
            $params = $pathRouter->parameters();
            $newParameters = [];
            foreach ($matches[1] as $item) {
                if ($item === 'one') {
                    break;
                }
                $newParameters[$item] = $params[$item];
            }
            $tArray = explode('.', $name);
            $newParameters['action'] = array_pop($tArray);
            $otherKeys = array_diff(array_keys($newParameters), ['action', 'controller']);
            if ($name[0] === '.') { // 相对地址
                $cArray = explode('/', $newParameters['controller']);
                for ($cKey = count($cArray) - 1, $tKey = count($tArray) - 1; $cKey >= 0; $cKey--, $tKey--) {
                    if (isset($tArray[$tKey])) {
                        if ($tArray[$tKey] !== '') {
                            $cArray[$cKey] = $tArray[$tKey];
                        }
                    } else {
                        break;
                    }
                }
                $newParameters['controller'] = implode('/', $cArray);
                if ($tKey >= 0) { // 还有其它参数
                    foreach (array_reverse($otherKeys) as $key) {
                        $newParameters[$key] = $cArray[$tKey--];
                        if ($tKey < 0) {
                            break;
                        }
                    }
                }
            } else { // 绝对地址
                foreach (array_values($otherKeys) as $tKey => $key) {
                    $newParameters[$key] = $tArray[$tKey] ?? '';
                }
                $newParameters['controller'] = implode('/', array_slice($tArray, $tKey + 1));
            }
            $parameters = array_merge($newParameters, $parameters);
            $name = $route->getName();
        }
        return route($name, $parameters, $absolute);
    }

}


if (!function_exists('auth_model')) {

    /**
     * 获取当前授权Model类名
     * @return string
     */
    function auth_model() {
        $user = auth()->user();
        if ($user) {
            return get_class($user);
        }
        return Laravel\Crbac\Models\Power\Admin::class;
    }

}


if (!function_exists('get_annotations')) {

    /**
     * 获取当前授权Model类名
     * @return string
     */
    function get_annotations(Reflector $reflector, string ...$annotationClass) {
        $annotations = [];
        // 处理php8+注解
        if (method_exists($reflector, 'getAttributes')) {
            foreach ($annotationClass as $class) {
                $annotations[$class] = $reflector->getAttributes($class);
            }
        }
        // 解析注释注解
        $comment = $reflector->getDocComment();
        $valueRule = '[a-zA-Z0-9_\\x7f-\\xff]+|"([^"\\\\]*|\\\\.)*"|\'([^\'\\\\]*|\\\\.)*\'';
        if ($comment && preg_match_all('/@([a-zA-Z_\\x7f-\\xff][a-zA-Z0-9_\\x7f-\\xff]*)\s*\(\s*((' . $valueRule . ')(\s*,\s*(' . $valueRule . ')\s*)*)?\s*\)/i', $comment, $matches)) {
            $classes = [];
            foreach ($annotationClass as $class) {
                $classes[$class] = strpos($class, '\\') ? ltrim(strrchr($class, '\\'), '\\') : $class;
            }
            foreach ($matches[1] as $key => $name) {
                foreach ($classes as $class => $className) {
                    if (strcasecmp($className, $name) === 0) {
                        if (!preg_match_all('/' . $valueRule . '/', $matches[2][$key], $params)) {
                            $params = [];
                        }
                        $annotations[$class][] = new $class(...array_map(function ($val) {
                                    return trim($val, '\'"');
                                }, $params[0] ?? []));
                        break;
                    }
                }
            }
        }
        return $annotations;
    }

}

//####################### 以下是兼容不同版本 #######################
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

if (!function_exists('request')) {

    /**
     * 获取请求体，兼容底版本
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function request($key = null, $default = null) {
        if (is_null($key)) {
            return app('request');
        }
        if (is_array($key)) {
            return app('request')->only($key);
        }
        $value = app('request')->__get($key);
        return is_null($value) ? $default : $value;
    }

}
if (!function_exists('array_get')) {

    /**
     * 提取数据
     * @param \ArrayAccess|array $array
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function array_get($array, $key, $default = null) {
        return Arr::get($array, $key, $default);
    }

}
if (!function_exists('array_set')) {


    /**
     * 设置数据
     * @param \ArrayAccess|array $array
     * @param string|null $key
     * @param mixed $value
     * @return array
     */
    function array_set(&$array, $key, $value) {
        return Arr::set($array, $key, $value);
    }

}
if (!function_exists('array_only')) {

    /**
     * 提取指定键名新数组
     * @param array $array
     * @param array|string $keys
     * @return array
     */
    function array_only($array, $keys) {
        return Arr::only($array, $keys);
    }

}


if (!function_exists('array_except')) {

    /**
     * 提取指定除外键名新数组
     * @param array $array
     * @param array|string $keys
     * @return array
     */
    function array_except($array, $keys) {
        return Arr::except($array, $keys);
    }

}

if (!function_exists('array_forget')) {

    /**
     * 删除数组中指定键元素
     * @param array $array
     * @param array|string $keys
     * @return array
     */
    function array_forget(&$array, $keys) {
        return Arr::forget($array, $keys);
    }

}

if (!function_exists('array_pluck')) {

    /**
     * 提取数组中指定键组成新数组
     * @param iterable $array
     * @param string|array $value
     * @param string|array|null $key
     * @return array
     */
    function array_pluck($array, $value, $key = null) {
        return Arr::pluck($array, $value, $key);
    }

}

if (!function_exists('studly_case')) {

    /**
     * 重组字符串分隔符并转换大小写大写再合并
     * @param string $value
     * @return v
     */
    function studly_case($value) {
        return Str::studly($value);
    }

}