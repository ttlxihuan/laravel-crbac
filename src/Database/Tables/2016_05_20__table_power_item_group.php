<?php

use Illuminate\Database\Schema\Blueprint;
use XiHuan\Crbac\Database\Migration;

class TablePowerItemGroup extends Migration {

    //表名
    protected $table = 'power_item_group';

    /*
     * 作用：添加字段列
     * 参数：$table Illuminate\Database\Schema\Blueprint
     * 返回值：void
     */
    public function setColumns(Blueprint $table) {
        $table->increments('power_item_group_id')->comment('主键');
        $table->string('name', 40)->notnull()->comment('权限项组名称');
        $table->string('comment', 500)->notnull()->default('')->comment('备注说明');
    }
}
