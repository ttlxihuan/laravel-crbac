<?php

/*
 * 角色与管理员关联
 */

namespace XiHuan\Crbac\Models\Power;

use XiHuan\Crbac\Models\Model;

class RoleAdmin extends Model {

    protected $table = 'power_role_admin'; //表名
    public $timestamps = false; //禁用时间

}
