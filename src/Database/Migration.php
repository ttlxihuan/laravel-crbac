<?php

/*
 * 创建表基本处理
 */

namespace Laravel\Crbac\Database;

use Schema;
use Laravel\Crbac\Models\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration as BaseMigration;

abstract class Migration extends BaseMigration {

    //是否添加时间字段
    protected $timestamps = true;

    /**
     * 创建表
     */
    public function up() {
        Schema::dropIfExists($this->table);
        Schema::create($this->table, function (Blueprint $table) {
            $this->setColumns($table);
            if ($this->timestamps) {
                $table->unsignedInteger(Model::CREATED_AT)->notnull()->comment('创建时间');
                $table->unsignedInteger(Model::UPDATED_AT)->notnull()->comment('删除时间');
            }
            $table->engine = 'Innodb';
        });
    }

    /**
     * 删除表
     */
    public function down() {
        Schema::drop($this->table);
    }

    /**
     * 添加字段列
     * @param Blueprint $table
     */
    protected abstract function setColumns(Blueprint $table);
}
