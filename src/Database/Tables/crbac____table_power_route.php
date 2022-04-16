<?php

use Illuminate\Database\Schema\Blueprint;
use Laravel\Crbac\Database\Migration;

class TablePowerRoute extends Migration {

    //表名
    protected $table = 'power_route';

    /**
     * 添加字段列
     * @param Blueprint $table
     */
    public function setColumns(Blueprint $table) {
        $table->increments('id')->comment('主键');
        $table->string('uses', 100)->notnull()->comment('控制器@方法');
        $table->string('url', 100)->notnull()->comment('路由地址串');
        $table->string('methods', 110)->notnull()->comment('请求类型集');
        $table->enum('is_usable', ['yes', 'no'])->notnull()->default('yes')->comment('是否有效可用');
    }

}
