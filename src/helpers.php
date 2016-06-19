<?php

if (!function_exists('prompt')) {
    /*
     * 作用：提示处理
     * 参数：$message string|array 信息
     *      $status string  信息状态类型
     *      $redirect string|-1 跳转地址，-1时跳转上个地址
     *      $timeout int 跳转等待时间
     * 返回值：array|view  json或视图
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
            return $data;
        } else {
            return view('prompt', $data);
        }
    }
}
if (!function_exists('currentRouteUses')) {
    /*
     * 作用：获取当前路由uses，仅控制器@方法结构
     * 参数：无
     * 返回值：string 
     */
    function currentRouteUses() {
        $uses = array_get(app('Illuminate\Routing\Route')->getAction(), 'uses', '');
        return is_string($uses) ? $uses : '';
    }
}
if (!function_exists('isPower')) {
    /*
     * 作用：判断用户是否拥有这个权限
     * 参数：$code string 权限码
     *      $default bool 如果权限不存在或禁用返回默认值
     * 返回值：bool
     */
    //
    function isPower($code, $default = false) {
        return Auth::check() ? Crbac::allow($code, $default) : false;
    }
}
if (!function_exists('isControllerPower')) {
    /*
     * 作用：判断用户是否拥有这个权限
     * 参数：$action string|null 指定方法名，默认为当前方法名
     *      $controller string|null 指定控制器类名，默认为当前控制器类名
     *      $default bool 如果权限不存在或禁用返回默认值
     * 返回值：bool
     */
    //
    function isControllerPower($action = null, $controller = null, $default = false) {
        if (is_null($controller)) {
            $controller = explode('@', currentRouteUses())[0];
        }
        if (empty($action)) {
            $action = array_get(explode('@', currentRouteUses()), 1);
        }
        return $controller && $action ? isPower($controller . '@' . $action, $default) : $default;
    }
}
if (!function_exists('isAction')) {
    /*
     * 作用：判断当前路由是否为这个方法
     * 参数：$action string 控制器@方法 或 方法名
     *      $method string 请求类型如GET或POST，默认为当前请求类型
     * 返回值：bool
     */
    //
    function isAction($action, $method = null) {
        $route = Route::current();
        $uses = array_get($route->getAction(), 'uses', '');
        if (empty($uses)) {
            return false;
        }
        if (strpos($action, '@') !== false) {
            $isAction = strcasecmp($action, $uses) == 0;
        } else {
            $isAction = strcasecmp($action, explode('@', $uses)[1]) == 0;
        }
        return $isAction && (empty($method) || array_intersect($route->methods(), (array) $method));
    }
}
if (!function_exists('auth_model')) {
    /*
     * 作用：获取当前授权Model类名
     * 参数：无
     * 返回值：string
     */
    function auth_model() {
        return Crbac::authModel();
    }
}
if (!function_exists('lang')) {
    /*
     * 作用：获取翻译内容
     * 参数：$key string 待翻译串
     *      $replace  替换内容
     *      $locale  语言环境，默认为配置
     * 返回值：string
     */
    function lang($key, array $replace = array(), $locale = null) {
        return app('translator')->get($key, $replace, $locale);
    }
}
if (!function_exists('validate_url')) {
    /*
     * 作用：获取验证数据可用URL地址
     * 参数：$model 需要验证的Model类名
     *      $field 需要验证的字段名
     * 返回值：string
     */
    function validate_url($model, $field) {
        static $service = null;
        if (!$service) {
            $service = new XiHuan\Crbac\Services\ExistService();
        }
        return $service->toUrl($model, $field);
    }
}