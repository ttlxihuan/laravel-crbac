<?php

/*
 * 数据编辑相关基本处理
 */

namespace Laravel\Crbac\Services\Power;

use Laravel\Crbac\Models\Model;
use Laravel\Crbac\Services\ModelEdit;
use Laravel\Crbac\Services\Service as BaseService;

abstract class Service extends BaseService {

    /**
     * 修改数据
     * @param string|Model $item
     * @param array $option
     * @return Model|false
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

    /**
     * 修改数据前处理
     * @param array $data
     * @param BaseService $service
     * @param Model|string $item
     */
    protected function editBefore(array &$data, BaseService $service, $item) {
        
    }

    /**
     * 修改数据后处理
     * @param Model $result
     * @param BaseService $service
     */
    protected function editAfter(Model $result, BaseService $service) {
        
    }

}
