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
        $data = [
            'status' => $status,
            'message' => $message
        ];
        if ($redirect == -1) {
            $redirect = URL::previous();
        }
        if ($redirect) {
            $data['redirect'] = $redirect;
            if ($timeout > 0) {
                $data['timeout'] = $timeout;
            } elseif (!Request::ajax()) {
                return redirect($redirect);
            }
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
     * @param bool $default
     * @return bool
     */
    function isPower($code, $default = false) {
        return Illuminate\Support\Facades\Auth::check() ? Crbac::allow($code, $default) : false;
    }

}
if (!function_exists('isControllerPower')) {

    /**
     * 判断用户是否拥有这个权限
     * @param string|null $action
     * @param string|null $controller
     * @param bool $default
     * @return bool
     */
    function isControllerPower($action = null, $controller = null, $default = false) {
        if (is_null($controller)) {
            $controller = explode('@', currentRouteUses())[0];
        }
        if (empty($action)) {
            $action = explode('@', currentRouteUses())['uses'] ?? '';
        }
        return $controller && $action ? isPower($controller . '@' . $action, $default) : $default;
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
        $array = explode('.', $name);
        $type = array_shift($array);
        $act = array_pop($array);
        if ($type === '') {
            // 相对路径，如果当前路径非mvc-crbac路由则无法正常生成
            $request = request();
            $route = $request->route();
            if ($route->getName() !== 'mvc-crbac') {
                return route($name, $parameters, $absolute);
            }
            $path = $request->getPathInfo();
            $prefix = $route->getPrefix();
            if ($prefix) {
                $path = substr($path, strlen(trim($prefix, '/') . '/') + 1);
            }
            $paths = [];
            foreach (explode('/', $path) as $path) {
                $paths[] = $path;
                if (strpos($path, '.') > 0) {
                    break;
                }
            }
            $type = array_shift($paths);
            $ctr_act = explode('.', array_pop($paths));
            if ($act == '') {
                $act = array_pop($ctr_act);
            }
            array_push($paths, array_shift($ctr_act));
            $size = count($array);
            if ($size > 0) {
                $array = array_splice($paths, count($array), count($array), $array);
            } else {
                $array = $paths;
            }
        }
        $ctr = implode('/', $array);
        array_unshift($parameters, $type, $ctr, $act);
        return route('mvc-crbac', $parameters, $absolute);
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