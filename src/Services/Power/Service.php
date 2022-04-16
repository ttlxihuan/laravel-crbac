<?php

/*
 * 数据编辑相关基本处理
 */

namespace Laravel\Crbac\Services\Power;

use Laravel\Crbac\Services\ModelEdit;
use Laravel\Crbac\Services\Service as BaseService;

abstract class Service extends BaseService {
    /*
     * 作用：修改数据
     * 参数：$item null|Model 要修改的数据
     *      $option array 要修改的数据项,默认全部
     * 返回值：Model|false
     */
    public function edit($item, array $option = []) {
        $service = new ModelEdit($this);
        return $service->requestEdit($item, $option, function(&$data, $service, $item) {
                    return $this->editBefore($data, $service, $item);
                }, function($result, $service) {
                    if (!$result) {//操作成功写入角色
                        return $result;
                    }
                    return $this->editAfter($result, $service);
                });
    }
    /*
     * 作用：修改数据前处理
     * 参数：$data array 要修改的数据
     *       $service Laravel\Crbac\Services\Service 编辑处理service
     *       $item Model|string 要编辑的Model或Model类名
     * 返回值：bool
     */
    protected function editBefore(&$data, BaseService $service, $item) {
        
    }
    /*
     * 作用：修改数据后处理
     * 参数：$result null|Model 修改的数据的结果
     *       $service Laravel\Crbac\Services\Service 编辑处理service
     * 返回值：void
     */
    protected function editAfter($result, BaseService $service) {
        
    }
}
