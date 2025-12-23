<?php

/*
 * 创建权限项表
 */

use Illuminate\Database\Schema\Blueprint;
use Laravel\Crbac\Database\Migration;

class TablePowerAdmin extends Migration {

    //表名
    protected $table = 'power_admin';

    /**
     * 添加字段列
     * @param Blueprint $table
     */
    public function setColumns(Blueprint $table) {
        $table->increments('id')->comment('主键');
        $table->string('realname', 32)->notnull()->comment('真实姓名');
        $table->string('username', 32)->notnull()->comment('登录用户名');
        $table->string('password', 64)->notnull()->comment('密码');
        $table->string('email', 64)->notnull()->comment('邮箱名');
        $table->string('remember_token', 64)->notnull()->default('')->comment('记住登录');
        $table->unsignedInteger('power_menu_group_id')->notnull()->comment('菜单组ID');
        $table->unsignedInteger('abnormal', 0)->notnull()->comment('异常登录次数');
        $table->unsignedInteger('locked_at', 0)->notnull()->comment('锁定登录时间');
        $table->enum('status', ['disable', 'enable', 'lock'])->notnull()->default('enable')->comment('状态，enable为启用');
        $table->unique('username');
    }

}
