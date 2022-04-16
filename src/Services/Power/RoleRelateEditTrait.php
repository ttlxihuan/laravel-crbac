<?php

/*
 * 编辑与角色关联数据
 */

namespace Laravel\Crbac\Services\Power;

use Laravel\Crbac\Models\Power\Role;
use Illuminate\Database\Eloquent\Model;

trait RoleRelateEditTrait {

    /**
     * 修改关联数据
     * @param Model $result
     * @param string $relateClass
     * @param string $relationField
     * @param bool $allowAll
     */
    protected function roleRelateEdit(Model $result, $relateClass, $relationField, $allowAll = true) {
        $roles = request('roles');
        if ($roles === 'all' && $allowAll) {//关联到所有角色
            $role_ids = Role::get(['id'])
                    ->pluck('id')
                    ->toArray();
        } elseif ($roles) {//写入部分角色
            $role_ids = Role::whereIn('id', explode(',', $roles))
                    ->get(['id'])
                    ->pluck('id')
                    ->toArray();
        }
        if (isset($role_ids) && count($role_ids)) {//保存关联数据
            $power_role_ids = $relateClass::whereIn('power_role_id', $role_ids)//获取已有关联数据
                    ->where($relationField, '=', $result->getKey())
                    ->get(['power_role_id'])
                    ->pluck('power_role_id')
                    ->toArray();
            $relateClass::whereNotIn('power_role_id', $role_ids)//删除无需关联数据
                    ->where($relationField, '=', $result->getKey())
                    ->delete();
            $role_ids = array_diff($role_ids, $power_role_ids);
            $inserts = array_map(function($item)use($result, $relationField) {
                return ['power_role_id' => $item, $relationField => $result->getKey()];
            }, $role_ids);
            $relateClass::insert($inserts);
        } else {//没有删除所有关联数据
            $relateClass::where($relationField, '=', $result->getKey())
                    ->delete();
        }
    }

}
