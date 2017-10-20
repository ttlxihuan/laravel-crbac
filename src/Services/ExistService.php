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

namespace XiHuan\Crbac\Services;

class ExistService extends Service {
    /*
     * 作用：验证数据是否允许使用
     * 参数：$model string 验证Model别名
     *      $field string 字段名
     *      $val string 值
     *      $id int|null 排除ID值
     * 返回值：bool
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
    /*
     * 作用：获取Model别名的Model类名
     * 参数：$model string Model别名
     *      $name string|null Model类名
     * 返回值：bool
     */
    private function getModel($model, $name = false) {
        $models = [
            'crbac/item' => \XiHuan\Crbac\Models\Power\Item::class,
            'crbac/item/group' => \XiHuan\Crbac\Models\Power\ItemGroup::class,
            'crbac/menu' => \XiHuan\Crbac\Models\Power\Menu::class,
            'crbac/menu/group' => \XiHuan\Crbac\Models\Power\MenuGroup::class,
            'crbac/role' => \XiHuan\Crbac\Models\Power\Role::class,
            'crbac/admin' => \XiHuan\Crbac\Models\Admin::class,
        ];
        if ($name) {
            return array_search($model, $models);
        }
        return array_get($models, $model);
    }
    /*
     * 作用：生成验证URL地址
     * 参数：$model string Model类名
     *      $field string 字段名
     * 返回值：url
     */
    public function toUrl($model, $field) {
        if (is_object($model)) {
            $parameters = [get_class($model), $field, 'id' => $model->getKey()];
        } else {
            $parameters = [$model, $field];
        }
        $parameters[0] = $this->getModel($parameters[0], true);
        return route('exist_validate', $parameters);
    }
}
