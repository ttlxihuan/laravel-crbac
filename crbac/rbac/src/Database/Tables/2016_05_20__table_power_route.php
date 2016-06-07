<?php

use Illuminate\Database\Schema\Blueprint;
use XiHuan\Crbac\Database\Migration;

class TablePowerRoute extends Migration {

    //表名
    protected $table = 'power_route';

    /*
     * 作用：添加字段列
     * 参数：$table Illuminate\Database\Schema\Blueprint
     * 返回值：void
     */
    public function setColumns(Blueprint $table) {
        $table->increments('power_route_id')->comment('主键');
        $table->string('uses', 100)->notnull()->comment('控制器@方法');
        $table->string('url', 100)->notnull()->comment('路由地址串');
    }
}
