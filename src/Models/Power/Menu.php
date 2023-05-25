<?php

/*
 * 菜单项
 */

namespace Laravel\Crbac\Models\Power;

use Laravel\Crbac\Models\Model;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;

class Menu extends Model {

    public static $_validator_rules = [//验证规则
        'name' => 'required|between:3,30|unique:power_menu', // varchar(35) not null comment '菜单名',
        'url' => 'required|between:1,55', // varchar(60) not null comment '链接地址',
        'power_item_id' => 'exists:power_item,id', // int unsigned not null default 0 comment '关联权限项ID',
        'comment' => 'required|between:1,955', //  varchar(1000) not null default '' comment '备注说明',
    ];
    public static $_validator_description = [//验证字段说明
        'name' => '菜单名', // varchar(35) not null comment '菜单名',
        'url' => '链接地址', // varchar(60) not null comment '链接地址',
        'power_item_id' => '权限项', // int unsigned not null default 0 comment '关联权限项ID',
        'comment' => '备注说明', // varchar(1000) not null default '' comment '备注说明',
    ];
    public static $_validator_messages = []; //验证统一说明
    protected $table = 'power_menu'; //表名
    protected static $validates = ['name']; //允许验证可用字段

    /**
     * 关联权限项
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function item() {
        return $this->hasOne(Item::class, 'id', 'power_item_id');
    }

    /**
     * 关联菜单组
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function groups() {
        return $this->belongsToMany(MenuGroup::class, 'power_menu_level', 'power_menu_id', 'power_menu_group_id')
                        ->select('power_menu_group.*', 'power_menu_level.parent_id', 'power_menu_level.id as level_id');
    }

    /**
     * 获取指定层级菜单
     * @param int $group_id
     * @param int $parent_id
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function level($group_id, $parent_id = 0) {
        return self::leftJoin('power_menu_level', 'power_menu_level.power_menu_id', '=', 'power_menu.id')
                        ->where('power_menu_level.power_menu_group_id', '=', $group_id)
                        ->where('power_menu_level.parent_id', '=', $parent_id)
                        ->get(['power_menu.*', 'power_menu_level.id as level_id', 'power_menu_level.parent_id']);
    }

    /**
     * 获取指定人员可用菜单
     * @param UserContract $admin
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function menus(UserContract $admin) {
        return self::leftJoin('power_menu_level', 'power_menu_level.power_menu_id', '=', 'power_menu.id')
                        ->where('power_menu_level.power_menu_group_id', '=', $admin->power_menu_group_id)
                        ->where(function($query)use($admin) {
                            $query->orWhere('power_menu.power_item_id', '=', '0')
                            ->orWhereIn('power_menu.power_item_id', function($query)use($admin) {
                                Item::addItemWhere($query, $admin);
                            })->orWhereIn('power_menu.power_item_id', function($query) {
                                $query->from('power_item')
                                ->where('status', '!=', 'enable')
                                ->select('id');
                            });
                        })
                        ->with('item')
                        ->orderBy('power_menu_level.sort', 'desc')
                        ->get(['power_menu.*', 'power_menu_level.id as level_id', 'power_menu_level.parent_id']);
    }

    /**
     * 获取指定菜单组中的菜单列表
     * @param int $group_id
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function group($group_id) {
        return self::leftJoin('power_menu_level', 'power_menu_level.power_menu_id', '=', 'power_menu.id')
                        ->where('power_menu_level.power_menu_group_id', '=', $group_id)
                        ->with('item')
                        ->orderBy('power_menu_level.sort', 'desc')
                        ->get(['power_menu.*', 'power_menu_level.id as level_id', 'power_menu_level.parent_id']);
    }

}
