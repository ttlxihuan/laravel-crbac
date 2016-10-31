<?php

/*
 * 编辑与角色关联数据
 */

namespace XiHuan\Crbac\Services\Power;

use Input;
use XiHuan\Crbac\Models\Power\Role;
use Illuminate\Database\Eloquent\Model;

trait RoleRelateEditTrait {
    /*
     * 作用：修改关联
     * 参数：$result Model
     *       $relateClass 关系Model类名
     *       $allowAll 是否允许直接关联全部
     * 返回值：void
     */
    protected function roleRelateEdit(Model $result, $relateClass, $relationField, $allowAll = true) {
        $roles = Input::get('roles');
        if ($roles === 'all' && $allowAll) {//关联到所有角色
            $role_ids = Role::get(['power_role_id'])
                    ->pluck('power_role_id')
                    ->toArray();
        } elseif ($roles) {//写入部分角色
            $role_ids = Role::whereIn('power_role_id', explode(',', $roles))
                    ->get(['power_role_id'])
                    ->pluck('power_role_id')
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
