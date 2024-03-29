<?php

/*
 * 管理员
 */

namespace Laravel\Crbac\Controllers\Power;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Laravel\Crbac\Models\Power\Admin;
use Laravel\Crbac\Services\ModelEdit;
use Illuminate\Support\Facades\Request;
use Laravel\Crbac\Controllers\Controller;
use Laravel\Crbac\Services\Power\Admin as AdminService;

class AdminController extends Controller {

    //备注说明
    protected $description = '管理员';

    /**
     * 登录
     * @return mixed
     */
    public function login() {
        if (Request::isMethod('GET')) {
            return view('power.admin.login');
        }
        $service = new ModelEdit();
        Admin::$_validator_rules['username'] = preg_replace('/unique:power_admin[^\|]*/', '', Admin::$_validator_rules['username']);
        if ($input = $service->validation(new Admin(), Request::all(), ['username', 'password'])) {
            if (Auth::attempt($input)) {
                if (Auth::user()->status !== 'enable') {
                    Auth::logout();
                    return prompt('账户异常', 'error');
                }
                return prompt('登录成功', 'success', -1);
            }
            return prompt('账号或密码错误', 'error');
        }
        return $service->prompt();
    }

    /**
     * 退出登录
     * @return mixed
     */
    public function logout() {
        Auth::logout();
        return prompt('退出成功', 'success', route('login'), 0);
    }

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
        $model = auth()->user();
        if (!Request::isMethod('post')) {
            return view('power.admin.password', [
                'title' => '修改密码',
                'item' => $model,
            ]);
        }
        if (!Hash::check(request('old_password'), $model->getAuthPassword())) {
            return prompt('原密码错误！', 'error');
        }
        $option = ['password'];
        $modelClass = auth_model();
        $modelClass::$_validator_rules['password'] .= '|confirmed';
        $service = new ModelEdit();
        $result = $service->requestEdit($model, $option);
        if ($result) {
            return $service->prompt('密码修改成功', null, -1);
        }
        return $service->prompt('密码修改失败');
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
            'status',
        ];
        $order = ['created' => 'created_at'];
        $default = ['order' => 'created', 'by' => 'desc'];
        list($lists, $toOrder) = $this->listsSelect(auth_model(), $where, $order, $default, function ($builder) {
            $builder->with('menuGroup');
        });
        $description = $this->description;
        return view('power.admin.lists', compact('lists', 'description', 'toOrder'));
    }

}
