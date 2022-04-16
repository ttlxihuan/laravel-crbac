<?php

use Illuminate\Database\Schema\Blueprint;
use Laravel\Crbac\Database\Migration;

class TablePowerMenuLevel extends Migration {

    //表名
    protected $table = 'power_menu_level';
    //是否添加时间字段
    protected $timestamps = false;

    /*
     * 作用：添加字段列
     * 参数：$table Illuminate\Database\Schema\Blueprint
     * 返回值：void
     */
    public function setColumns(Blueprint $table) {
        $table->increments('id')->comment('主键');
        $table->unsignedInteger('power_menu_id')->notnull()->comment('菜单ID');
        $table->unsignedInteger('power_menu_group_id')->notnull()->comment('所属菜单组ID');
        $table->unsignedInteger('parent_id')->notnull()->default(0)->comment('上级层级ID');
        $table->smallInteger('sort', false, true)->notnull()->default(0)->comment('排序值，大到小');
        $table->unique(['power_menu_id', 'power_menu_group_id', 'parent_id'], 'menu_level');
    }
}
