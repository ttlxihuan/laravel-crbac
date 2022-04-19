<?php

use Illuminate\Database\Schema\Blueprint;
use Laravel\Crbac\Database\Migration;

class TablePowerMenu extends Migration {

    //表名
    protected $table = 'power_menu';

    /**
     * 添加字段列
     * @param Blueprint $table
     */
    public function setColumns(Blueprint $table) {
        $table->increments('id')->comment('主键');
        $table->string('name', 40)->notnull()->comment('菜单名');
        $table->string('url', 100)->notnull()->comment('路由地址串');
        $table->unsignedInteger('power_item_id')->notnull()->default(0)->comment('权限项ID');
        $table->string('comment', 500)->notnull()->default('')->comment('备注说明');
    }

}
