<?php

/*
 * 管理员
 */

namespace Laravel\Crbac\Controllers\Power;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\{
    DB,
    Hash,
    Auth,
    Request
};
use Laravel\Crbac\Models\Power\Admin;
use Laravel\Crbac\Services\ModelEdit;
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
            $res = Auth::validate($input);
            $admin = Auth::getLastAttempted();
            if ($admin) {
                if ($admin->status === 'lock' && $admin->locked_at < time()) {
                    $admin->status = 'enable';
                }
                if ($res && $admin->status === 'enable') {
                    $admin->abnormal = 0;
                    $admin->locked_at = 0;
                    $remember = (bool) request('remember', false);
                    Auth::login($admin, $remember);
                    if (!$remember) {
                        $admin->save();
                    }
                    return prompt('登录成功', 'success', request('redirect', -1));
                }
                $limit = max(min(120, (int) env('ADMIN_LOGIN_ATTEMPT_MAX', 12)), 0);
                if ($admin->abnormal > $limit || $admin->status === 'disable') { // 超出最大限制就不能再登录了
                    $admin->status = 'disable';
                    $admin->save();
                    return prompt('账号已禁用', 'error');
                }
                if ($admin->status === 'enable') {
                    $limit = max(min(50, (int) env('ADMIN_LOGIN_ATTEMPT_LIMIT', 3)), 0);
                    if ($limit > 0) {
                        $admin->newQuery()
                                ->where('id', $admin->getKey())
                                ->whereIn('status', ['enable', 'lock'])
                                ->update([
                                    'abnormal' => DB::raw('abnormal + 1'),
                                    'locked_at' => DB::raw("IF(`abnormal` % {$limit} = 0, unix_timestamp() + 300 * POW(2, `abnormal` % {$limit}), `locked_at`)"),
                                    'status' => DB::raw("IF(`locked_at` > unix_timestamp(), 'lock', `status`)"),
                        ]);
                    }
                } elseif ($admin->status === 'lock') {
                    return prompt('账号被限制登录', 'error');
                }
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

    /**
     * 分片上传文件合并处理
     * @return string
     * @methods(POST)
     */
    public function uploadSplit() {
        $file = request()->file('upload-file');
        if (!($file instanceof UploadedFile) || !$file->isValid()) {
            return prompt('没有上传文件！', 'error');
        }
        $ext = strrchr(basename($file->getClientOriginalName()), '.');
        if (!$ext) {
            return prompt('上传文件没有扩展名', 'error');
        }
        $index = request('upload-index');
        if (!is_numeric($index)) {
            return prompt('缺少有效参数：upload-index', 'error');
        }
        $no = request('upload-no'); // 编号
        if (!is_string($no)) {
            return prompt('缺少有效参数：upload-no', 'error');
        }
        $basepath = storage_path('/upload-tmp');
        is_dir($basepath) || mkdir($basepath, 0666, true);
        $filename = md5($file->getClientOriginalName() . $no . auth()->id()) . $ext;
        $fp = fopen($basepath . '/' . $filename, 'cb');
        flock($fp, LOCK_EX | LOCK_NB);
        fseek($fp, $index, SEEK_SET);
        fwrite($fp, file_get_contents($file->getPathname()));
        fclose($fp);
        return prompt(['name' => $filename]);
    }
}
