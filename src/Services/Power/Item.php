<?php

/*
 * 权限项处理
 */

namespace Laravel\Crbac\Services\Power;

use Laravel\Crbac\Models\Model;
use Laravel\Crbac\Models\Power\RoleItem;
use Laravel\Crbac\Services\Service as BaseService;

class Item extends Service {

    use RoleRelateEditTrait;

    /**
     * 修改数据后处理
     * @param Model $result
     * @param BaseService $service
     */
    protected function editAfter(Model $result, BaseService $service) {
        $this->roleRelateEdit($result, RoleItem::class, 'power_item_id');
    }

}
