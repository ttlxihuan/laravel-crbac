<?php

use Illuminate\Database\Schema\Blueprint;
use XiHuan\Crbac\Database\Migration;

class TablePowerRoleItem extends Migration {

    //表名
    protected $table = 'power_role_item';
    //是否添加时间字段
    protected $timestamps = false;

    /*
     * 作用：添加字段列
     * 参数：$table Illuminate\Database\Schema\Blueprint
     * 返回值：void
     */
    public function setColumns(Blueprint $table) {
        $table->increments('id')->comment('主键');
        $table->unsignedInteger('power_role_id')->notnull()->comment('角色ID');
        $table->unsignedInteger('power_item_id')->notnull()->comment('权限项ID');
        $table->unique(['power_role_id', 'power_item_id'], 'role_item');
    }
}
