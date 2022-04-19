<?php

/*
 * 管理员相关
 */

namespace Laravel\Crbac\Services\Power;

use Laravel\Crbac\Models\Model;
use Laravel\Crbac\Models\Power\RoleAdmin;
use Laravel\Crbac\Services\Service as BaseService;
use Laravel\Crbac\Models\Power\Admin as AdminModel;

class Admin extends Service {

    use RoleRelateEditTrait;

    /**
     * 修改数据
     * @param string|Model $item
     * @param array $option
     * @return Model|false
     */
    public function edit($item, array $option = []) {
        $password = request('password');
        if ($item && empty($password)) {//不修改密码
            $rules = array_except(AdminModel::$_validator_rules, 'password');
            $option = array_keys($rules);
        }
        return parent::edit($item, $option);
    }

    /**
     * 修改数据后处理
     * @param Model $result
     * @param BaseService $service
     */
    protected function editAfter(Model $result, BaseService $service) {
        $this->roleRelateEdit($result, RoleAdmin::class, 'power_admin_id', false);
    }

}
