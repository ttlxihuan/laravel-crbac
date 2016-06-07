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
    protected function roleRelateEdit(Model $result, $relateClass, $allowAll = true) {
        $roles = Input::get('roles');
        if ($roles === 'all' && $allowAll) {//关联到所有角色
            $role_ids = array_pluck(Role::get(['power_role_id']), 'power_role_id');
        } elseif ($roles) {//写入部分角色
            $role_ids = array_pluck(Role::whereIn('power_role_id', explode(',', $roles))
                            ->get(['power_role_id']), 'power_role_id');
        }
        if (isset($role_ids) && count($role_ids)) {//保存关联数据
            $power_role_ids = array_pluck($relateClass::whereIn('power_role_id', $role_ids)//获取已有关联数据
                            ->where($result->getKeyName(), '=', $result->getKey())
                            ->get(['power_role_id']), 'power_role_id');
            $relateClass::whereNotIn('power_role_id', $role_ids)//删除无需关联数据
                    ->where($result->getKeyName(), '=', $result->getKey())
                    ->delete();
            $role_ids = array_diff($role_ids, $power_role_ids);
            $inserts = array_map(function($item)use($result) {
                return ['power_role_id' => $item, $result->getKeyName() => $result->getKey()];
            }, $role_ids);
            $relateClass::insert($inserts);
        } else {//没有删除所有关联数据
            $relateClass::where($result->getKeyName(), '=', $result->getKey())
                    ->delete();
        }
    }
}
