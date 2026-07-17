<?php

/*
 * 创建系统配置表
 */

use Illuminate\Database\Schema\Blueprint;
use Laravel\Crbac\Database\Migration;

class TablePowerConfig extends Migration {

    //表名
    protected $table = 'power_config';

    /**
     * 添加字段列
     * @param Blueprint $table
     */
    public function setColumns(Blueprint $table) {
        $table->increments('id')->comment('主键');
        $table->string('key', 100)->comment('配置键');
        $table->text('value')->nullable()->comment('配置值');
        $table->enum('type', ['string', 'number', 'json', 'boolean'])->default('string')->comment('配置值类型');
        $table->string('comment', 255)->default('')->comment('说明');
        $table->enum('status', ['disable', 'enable'])->notnull()->default('enable')->comment('状态，enable为启用');
        $table->unique('key');
    }

}
