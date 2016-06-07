<?php

/*
 * 模块基类
 */

namespace XiHuan\Crbac\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;

abstract class Model extends BaseModel {

    protected $dateFormat = 'U'; //时间保存格式
    protected static $validates = []; //允许验证可用字段

    /*
     * 作用：初始化处理
     * 参数：无
     * 返回值：void
     */
    //公共处理，只有主键不能修改
    public function __construct(array $attributes = []) {
        if ($this->guarded === ['*']) {
            $this->guarded = [$this->primaryKey];
        }
        parent::__construct($attributes);
    }
    /*
     * 作用：获取允许验证可用字段
     * 参数：$field string 字段名
     * 返回值：string|null
     */
    public static function validate($field) {
        if (in_array($field, static::$validates)) {
            return $field;
        }
        if (isset(static::$validates[$field])) {
            return static::$validates[$field];
        }
    }
    /*
     * 作用：获取时间格式
     * 参数：无
     * 返回值：string
     */
    protected function getDateFormat() {
        return $this->dateFormat;
    }
}
