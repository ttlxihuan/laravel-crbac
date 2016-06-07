<?php

/*
 * 创建表基本处理
 */

namespace XiHuan\Crbac\Database;

use Schema;
use XiHuan\Crbac\Models\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration as BaseMigration;

abstract class Migration extends BaseMigration {

    //是否添加时间字段
    protected $timestamps = true;

    /*
     * 作用：创建表
     * 参数：无
     * 返回值：void
     */
    public function up() {
        Schema::dropIfExists($this->table);
        Schema::create($this->table, function (Blueprint $table) {
            $this->setColumns($table);
            if ($this->timestamps) {
                $table->unsignedInteger(Model::CREATED_AT)->notnull()->comment('创建时间');
                $table->unsignedInteger(Model::UPDATED_AT)->notnull()->comment('删除时间');
            }
            $table->engine = 'MyISAM';
        });
    }
    /*
     * 作用：删除表
     * 参数：无
     * 返回值：void
     */
    public function down() {
        Schema::drop($this->table);
    }
    /*
     * 作用：添加字段列
     * 参数：$table Illuminate\Database\Schema\Blueprint
     * 返回值：void
     */
    protected abstract function setColumns(Blueprint $table);
}
