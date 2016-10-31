<?php

/*
 * 管理员相关
 */

namespace XiHuan\Crbac\Services\Power;

use Input;
use XiHuan\Crbac\Models\Power\RoleAdmin;
use XiHuan\Crbac\Services\Service as BaseService;
use XiHuan\Crbac\Models\Admin as AdminModel;

class Admin extends Service {

    use RoleRelateEditTrait;
    /*
     * 作用：修改数据
     * 参数：$item null|Model 要修改的数据
     *      $option array 要修改的数据项,默认全部
     * 返回值：Model|false
     */
    public function edit($item, array $option = []) {
        $password = Input::Get('password');
        if ($item && empty($password)) {//不修改密码
            $rules = array_except(AdminModel::$_validator_rules, 'password');
            $option = array_keys($rules);
        }
        return parent::edit($item, $option);
    }
    /*
     * 作用：修改数据后处理
     * 参数：$result null|Model 修改的数据的结果
     *       $service XiHuan\Crbac\Services\Service 编辑处理service
     * 返回值：void
     */
    protected function editAfter($result, BaseService $service) {
        $this->roleRelateEdit($result, RoleAdmin::class, 'admin_id', false);
    }
}
