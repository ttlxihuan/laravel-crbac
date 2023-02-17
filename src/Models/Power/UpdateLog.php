<?php

/*
 * 修改日志记录
 */

namespace Laravel\Crbac\Models\Power;

class UpdateLog extends \Laravel\Crbac\Models\Model {

    /**
     * @var bool 是否记录修改日志
     */
    protected $saveUpdateLog = false;

    /**
     * @var string 时间保存格式
     */
    protected $dateFormat = 'U';

    /**
     * @var string 表名
     */
    protected $table = 'power_update_logs';

    /**
     * @var array 操作类型
     */
    public static $_TYPES = [
        'create' => '创建',
        'update' => '修改',
        'delete' => '删除'
    ];

    /**
     * 获取状态名
     * @return string|false|null
     */
    public function typeName() {
        return $this->type ? array_get(static::$_TYPES, $this->type) : false;
    }

    /**
     * 关联管理员
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function admin() {
        return $this->hasOne(Admin::class, 'id', 'admin_id');
    }

}
