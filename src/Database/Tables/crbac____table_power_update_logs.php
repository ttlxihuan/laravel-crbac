<?php

/*
 * 创建修改日志记录表
 */

use Illuminate\Database\Schema\Blueprint;
use Laravel\Crbac\Database\Migration;

class TablePowerUpdateLogs extends Migration {

    //表名
    protected $table = 'power_update_logs';

    /**
     * 添加字段列
     * @param Blueprint $table
     */
    public function setColumns(Blueprint $table) {
        $table->increments('id')->comment('主键');
        $table->string('model', 100)->notnull()->comment('模型类名');
        $table->enum('type', ['create', 'update', 'delete'])->notnull()->comment('操作类型');
        $table->integer('primary_id')->notnull()->comment('操作模型主键值');
        $table->string('user_agent', 100)->notnull()->comment('终端标识');
        $table->string('url', 250)->notnull()->comment('操作来源地址');
        $table->string('ip', 64)->notnull()->comment('操作IP地址');
        $table->unsignedInteger('admin_id')->notnull()->comment('管理员ID');
        $table->longText('old_data')->comment('原数据包');
        $table->longText('new_data')->comment('新数据包');
    }

}
