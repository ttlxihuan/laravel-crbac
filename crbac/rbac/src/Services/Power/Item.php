<?php

/*
 * 权限项处理
 */

namespace XiHuan\Crbac\Services\Power;

use Input;
use XiHuan\Crbac\Models\Power\RoleItem;
use XiHuan\Crbac\Services\Service as BaseService;

class Item extends Service {

    use RoleRelateEditTrait;
    /*
     * 作用：修改数据
     * 参数：$item null|Model 要修改的数据
     *      $option array 要修改的数据项,默认全部
     * 返回值：Model|false
     */
    public function edit($item, array $option = []) {
        $code = Input::Get('code');
        if (strpos('App\\Http\\Controllers\\', $code) === 0) {
            $code = str_replace('App\\Http\\Controllers\\', '', $code);
            Input::merge(compact('code'));
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
        $this->roleRelateEdit($result, RoleItem::class);
    }
}
