<?php

/*
 * 基本Service处理
 */

namespace Laravel\Crbac\Services;

abstract class Service {

    private $service; //上级Service对象
    private $messages = []; //异常信息

    /**
     * 初始化
     * @param \Laravel\Crbac\Services\Service $service
     */
    public function __construct(Service $service = null) {
        $this->service = $service;
    }

    /**
     * 写入错误
     * @param array|string $key
     * @param string $value
     * @return false
     */
    public function setError($key, $value) {
        if ($this->service) {
            $this->service->setError($key, $value);
        }
        array_set($this->messages, $key, $value);
        return false;
    }

    /**
     * 获取错误
     * @param null|string $key
     * @param null|mixed $default
     * @return mixed
     */
    public function getError($key = null, $default = null) {
        return array_get($this->messages, $key, $default);
    }

    /**
     * 提示框处理
     * @param null|string $title
     * @param null|string $info
     * @param null|-1|url $redirect
     * @param int $timeout
     * @return mixed
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

    /**
     * 获取异常数据
     * @param array $messages
     * @return array
     */
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
