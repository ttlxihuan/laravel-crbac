<?php

/*
 * 管理员
 */

namespace XiHuan\Crbac\Controllers\Power;

use Request;
use XiHuan\Crbac\Models\Admin;
use XiHuan\Crbac\Controllers\Controller;
use XiHuan\Crbac\Services\Power\Admin as AdminService;

class AdminController extends Controller {

    //备注说明
    protected $description = '管理员';

    /*
     * 作用：编辑管理员信息
     * 参数：$item XiHuan\Crbac\Models\Admin 需要编辑的数据，默认为添加
     * 返回值：view|array
     */
    public function edit(Admin $item = null) {
        return $this->modelEdit($item, 'power.admin.edit', auth_model(), [], AdminService::class);
    }
    /*
     * 作用：修改密码
     * 参数：无
     * 返回值：view|array
     */
    public function password() {
        $option = ['email', 'password'];
        $modelClass = auth_model();
        $modelClass::$_validator_rules['password'] .= '|confirmed';
        $result = $this->modelEdit(auth()->user(), 'power.admin.password', $modelClass, $option);
        if (!Request::isMethod('post')) {
            $result->with('title', '修改密码');
        }
        return $result;
    }
    /*
     * 作用：管理员列表
     * 参数：无
     * 返回值：view
     */
    public function lists() {
        $where = [
            'realname' => 'like',
            'username' => 'like',
            'status' => [2 => 'intval'],
        ];
        $order = ['created' => 'created_at'];
        $default = ['order' => 'created', 'by' => 'desc'];
        list($lists, $toOrder) = $this->listsSelect(auth_model(), $where, $order, $default, function($builder) {
            $builder->with('menuGroup');
        });
        $description = $this->description;
        return view('power.admin.lists', compact('lists', 'description', 'toOrder'));
    }
}
