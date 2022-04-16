<?php

/*
 * 管理员
 */

namespace Laravel\Crbac\Controllers\Power;

use Laravel\Crbac\Models\Power\Admin;
use Illuminate\Support\Facades\Request;
use Laravel\Crbac\Controllers\Controller;
use Laravel\Crbac\Services\Power\Admin as AdminService;

class AdminController extends Controller {

    //备注说明
    protected $description = '管理员';

    /**
     * 编辑管理员信息
     * @param Admin $item
     * @return mixed
     * @methods(GET,POST)
     */
    public function edit(Admin $item = null) {
        return $this->modelEdit($item, 'power.admin.edit', auth_model(), [], AdminService::class);
    }

    /**
     * 修改密码
     * @return mixed
     * @methods(GET,POST)
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

    /**
     * 管理员列表
     * @return view
     * @methods(GET)
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
