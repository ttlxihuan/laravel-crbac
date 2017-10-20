<?php

/*
 * 创建权限项表
 */

use Illuminate\Database\Schema\Blueprint;
use XiHuan\Crbac\Database\Migration;

class TablePowerAdmin extends Migration {

    //表名
    protected $table = 'admin';

    /*
     * 作用：添加字段列
     * 参数：$table Illuminate\Database\Schema\Blueprint
     * 返回值：void
     */
    public function setColumns(Blueprint $table) {
        $table->increments('admin_id')->comment('主键');
        $table->string('realname', 32)->notnull()->comment('真实姓名');
        $table->string('username', 32)->notnull()->comment('登录用户名');
        $table->string('password', 64)->notnull()->comment('密码');
        $table->string('email', 64)->notnull()->comment('邮箱名');
        $table->string('remember_token', 64)->notnull()->default('')->comment('记住登录');
        $table->unsignedInteger('power_menu_group_id')->notnull()->comment('菜单组ID');
        $table->enum('status', ['disable', 'enable'])->notnull()->default('enable')->comment('启用或禁用，enable为启用');
        $table->unique('username');
    }
}
