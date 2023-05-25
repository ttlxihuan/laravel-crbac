<?php

/*
 * 模块基类
 */

namespace Laravel\Crbac\Models;

use Laravel\Crbac\Models\Power\UpdateLog;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model as BaseModel;

abstract class Model extends BaseModel {

    /**
     * @var string 时间保存格式
     */
    protected $dateFormat = 'U';

    /**
     * @var array 允许验证可用字段
     */
    protected static $validates = [];

    /**
     * @var bool 是否记录修改日志
     */
    protected $saveUpdateLog = true;

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

    /**
     * Save the model to the database.
     *
     * @param  array  $options
     * @return bool
     */
    public function save(array $options = []) {
        if (!$this->saveUpdateLog) {
            return parent::save($options);
        }
        $type = $this->exists ? 'update' : 'create';
        $data = $this->getOriginalAndRelations();
        $result = parent::save($options);
        if ($result) {
            $this->saveUpdateLog($type, $data, $this->getOriginalAndRelations());
        }
        return $result;
    }

    /**
     * Delete the model from the database.
     * 
     * @return bool|null
     * @throws \Exception
     */
    public function delete() {
        if (!$this->saveUpdateLog) {
            return parent::delete();
        }
        $data = $this->getOriginalAndRelations();
        $result = parent::delete();
        if ($result) {
            $this->saveUpdateLog('delete', $data, []);
        }
        return $result;
    }

    /**
     * 获取原数据
     * @return array
     */
    protected function getOriginalAndRelations() {
        $data = [];
        if (count($this->original)) {
            $attributes = $this->attributes;
            $this->attributes = $this->original;
            foreach (array_keys($this->attributes) as $key) {
                $data[$key] = (string) $this->getAttributeValue($key);
            }
            $this->attributes = $attributes;
        }
        return $data;
    }

    /**
     * 保存修改日志
     * @param string $type
     * @param array $old
     * @param array $new
     * @return void
     */
    protected function saveUpdateLog(string $type, array $old, array $new) {
        $except = $this->exceptUpdateLogKey();
        if (count($except)) {
            $old = array_except($old, $except);
            $new = array_except($new, $except);
        }
        if ($old !== $new) {
            $log = new UpdateLog([
                'model' => static::class,
                'type' => $type,
                'primary_id' => (int) $this->getKey(),
                'user_agent' => request()->userAgent(),
                'url' => request()->fullUrl(),
                'ip' => request()->ip(),
                'admin_id' => (int) auth()->id(),
                'old_data' => json_encode(array_diff($old, $new)),
                'new_data' => json_encode(array_diff($new, $old))
            ]);
            $log->save();
        }
    }

    /**
     * 创建数据并写库
     * @param type $data
     * @return \static
     */
    public static function create($data) {
        $model = new static($data);
        $model->save();
        return $model;
    }

    /**
     * 排除指定字段不记录到日志中
     * @return array
     */
    protected function exceptUpdateLogKey(): array {
        return [];
    }

}
