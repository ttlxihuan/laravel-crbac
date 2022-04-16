<?php

/*
 * 模块基类
 */

namespace Laravel\Crbac\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;

abstract class Model extends BaseModel {

    protected $dateFormat = 'U'; //时间保存格式
    protected static $validates = []; //允许验证可用字段

    /**
     * 初始化处理
     * @param array $attributes
     */
    public function __construct(array $attributes = []) {
        if ($this->guarded === ['*']) {
            $this->guarded = [$this->primaryKey];
        }
        parent::__construct($attributes);
    }

    /**
     * 获取允许验证可用字段
     * @param string $field
     * @return string|null
     */
    public static function validate($field) {
        if (in_array($field, static::$validates)) {
            return $field;
        }
        if (isset(static::$validates[$field])) {
            return static::$validates[$field];
        }
    }

}
