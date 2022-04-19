<?php

use Illuminate\Database\Schema\Blueprint;
use Laravel\Crbac\Database\Migration;

class TablePowerRole extends Migration {

    //表名
    protected $table = 'power_role';

    /**
     * 添加字段列
     * @param Blueprint $table
     */
    public function setColumns(Blueprint $table) {
        $table->increments('id')->comment('主键');
        $table->string('name', 40)->notnull()->comment('权限项组名称');
        $table->enum('status', ['disable', 'enable'])->notnull()->default('enable')->comment('启用或禁用，enable为启用');
        $table->string('comment', 500)->notnull()->default('')->comment('备注说明');
    }

}
