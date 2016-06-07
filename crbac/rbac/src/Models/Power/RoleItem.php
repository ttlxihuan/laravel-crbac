<?php

/*
 * 角色与权限项关联
 */

namespace XiHuan\Crbac\Models\Power;

use XiHuan\Crbac\Models\Model;

class RoleItem extends Model {

    protected $table = 'power_role_item'; //表名
    public $timestamps = false; //禁用时间

}
