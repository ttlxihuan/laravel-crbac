<?php

/*
 * 菜单层与菜单组关联
 */

namespace Laravel\Crbac\Models\Power;

use Laravel\Crbac\Models\Model;

class MenuLevel extends Model {

    protected $table = 'power_menu_level'; //表名
    public $timestamps = false; //禁用时间

}
