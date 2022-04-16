<?php

/*
 * 管理员相关
 */

namespace Laravel\Crbac\Services\Power;

use Laravel\Crbac\Models\Power\RoleAdmin;
use Laravel\Crbac\Services\Service as BaseService;
use Laravel\Crbac\Models\Power\Admin as AdminModel;

class Admin extends Service {

    use RoleRelateEditTrait;
    /*
     * 作用：修改数据
     * 参数：$item null|Model 要修改的数据
     *      $option array 要修改的数据项,默认全部
     * 返回值：Model|false
     */
    public function edit($item, array $option = []) {
        $password = request('password');
        if ($item && empty($password)) {//不修改密码
            $rules = array_except(AdminModel::$_validator_rules, 'password');
            $option = array_keys($rules);
        }
        return parent::edit($item, $option);
    }
    /*
     * 作用：修改数据后处理
     * 参数：$result null|Model 修改的数据的结果
     *       $service Laravel\Crbac\Services\Service 编辑处理service
     * 返回值：void
     */
    protected function editAfter($result, BaseService $service) {
        $this->roleRelateEdit($result, RoleAdmin::class, 'power_admin_id', false);
    }
}
