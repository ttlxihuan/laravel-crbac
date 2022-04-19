<?php

/**
 * 模型数据是否存在处理
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
            return $builder->count() > 0;
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
            $model = get_class($model);
        } else {
            $parameters = [];
        }
        return crbac_route('usable.' . $this->getModel($model, true) . '.' . $field, $parameters);
    }

}
