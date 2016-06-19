<?php

/*
 * 基本Service处理
 */

namespace XiHuan\Crbac\Services;

abstract class Service {

    private $service; //上级Service对象
    private $messages = []; //异常信息

    /*
     * 作用：初始化
     * 参数：$service self|null 上级Service对象
     * 返回值：void
     */
    public function __construct(Service $service = null) {
        $this->service = $service;
    }
    /*
     * 作用：写入错误
     * 参数：$key array|string 错误键名或错误集
     *       $value string 错误值
     * 返回值：false
     */
    public function setError($key, $value) {
        if ($this->service) {
            $this->service->setError($key, $value);
        }
        array_set($this->messages, $key, $value);
        return false;
    }
    /*
     * 作用：获取错误
     * 参数：$key null|string 错误键名或错误集
     *       $default null|mixed 不存在时返回默认值
     * 返回值：mixed
     */
    public function getError($key = null, $default = null) {
        return array_get($this->messages, $key, $default);
    }
    /*
     * 作用：提示框处理
     * 参数：$title null|string 标题语
     *       $info null|string 标示详情
     *       $redirect null|-1|url 跳转地址
     *       $timeout int 跳转等待时间
     * 返回值：view|array
     */
    public function prompt($title = null, $info = null, $redirect = null, $timeout = 3) {
        if (count($this->messages)) {
            $status = 'error';
        } else {
            $status = 'success';
        }
        if (is_null($title)) {
            if ($status == 'error') {
                $title = '操作失败';
            } else {
                $title = '操作成功';
            }
        }
        if (is_null($info)) {
            $info = $status == 'success' ? $title : $this->messages($this->messages);
        }
        $message = compact('info', 'title');
        return prompt($message, $status, $redirect, $timeout);
    }
    /*
     * 作用：获取异常数据
     * 参数：$messages array 异常数据
     * 返回值：array
     */
    //异常转为字符串
    private function messages($messages) {
        $map = [];
        foreach ($messages as $message) {
            if (is_array($message)) {
                $map = array_merge($map, $this->messages($message));
            } else {
                array_push($map, $message);
            }
        }
        return $map;
    }
}
