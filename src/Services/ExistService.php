<?php

/**
 * @文件名 ExistService
 * @字符集 UTF-8
 * @创建人 奚欢
 * @版权所有 版权属创建人所有，任何人要复制、翻译、改编或演出等均需要得到版权所有人的许可
 * @创建时间 2016-6-2 22:30:44
 * @版本 1.0
 * @说明 是否存在处理
 */

namespace Laravel\Crbac\Services;

class ExistService extends Service {

    /**
     * 验证数据是否允许使用
     * @param string $model
     * @param string $field
     * @param mixed $val
     * @param mixed $id
     * @return mixed
     */
    public function check($model, $field, $val, $id = null) {
        $modelClass = $this->getModel($model);
        if ($modelClass && $field = $modelClass::validate($field)) {
            $model = new $modelClass;
            $builder = $model->where($field, '=', $val);
            if ($id) {
                $builder->where($model->getKeyName(), '!=', $id);
            }
            return (bool) $builder->count();
        }
    }

    /**
     * 获取Model别名的Model类名
     * @param string $model
     * @param string $name
     * @return mixed
     */
    private function getModel($model, $name = false) {
        $models = [
            'crbac/item' => \Laravel\Crbac\Models\Power\Item::class,
            'crbac/item/group' => \Laravel\Crbac\Models\Power\ItemGroup::class,
            'crbac/menu' => \Laravel\Crbac\Models\Power\Menu::class,
            'crbac/menu/group' => \Laravel\Crbac\Models\Power\MenuGroup::class,
            'crbac/role' => \Laravel\Crbac\Models\Power\Role::class,
            'crbac/admin' => \Laravel\Crbac\Models\Power\Admin::class,
        ];
        if ($name) {
            return array_search($model, $models);
        }
        return array_get($models, $model);
    }

    /**
     * 生成验证URL地址
     * @param string|Model $model
     * @param string $field
     * @return url
     */
    public function toUrl($model, string $field) {
        if (is_object($model)) {
            $parameters = ['id' => $model->getKey()];
        } else {
            $parameters = [];
        }
        return crbac_route('usable.' . $this->getModel($model, true) . '.' . $field, $parameters);
    }

}
