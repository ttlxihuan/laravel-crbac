<?php

/*
 * 权限项
 */

namespace Laravel\Crbac\Models\Power;

use Laravel\Crbac\Models\Model;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;

class Item extends Model {

    use \Laravel\Crbac\Models\GetMappingTrait;

    public static $_STATUS = [//状态配置
        'enable' => '启用',
        'disable' => '禁用'
    ];
    public static $_validator_rules = [//验证规则
        'name' => 'required|between:3,30', // varchar(35) not null comment '权限项名称',
        'code' => 'required|between:3,75|unique:power_item|string', // varchar(80) unique not null comment '权限项代码',
        'power_item_group_id' => 'required|exists:power_item_group,id', //int unsigned not null comment '权限项组ID',
        'status' => 'required|in:disable,enable', // enum('disable','enable') NOT NULL DEFAULT 'enable' COMMENT '启用或禁用，enable为启用',
        'comment' => 'required|between:1,955', //  varchar(1000) not null default '' comment '备注说明',
    ];
    public static $_validator_description = [//验证字段说明
        'name' => '权限项名称', // varchar(35) not null comment '权限项名称',
        'code' => '权限项代码', // varchar(35) unique not null comment '权限项代码',
        'power_item_group_id' => '权限项组', //int unsigned not null comment '权限项组ID',
        'status' => '权限项状态', // enum('disable','enable') NOT NULL DEFAULT 'enable' COMMENT '启用或禁用，enable为启用',
        'comment' => '备注说明', // varchar(1000) not null default '' comment '备注说明',
    ];
    public static $_validator_messages = []; //验证统一说明
    protected $table = 'power_item'; //表名
    protected static $validates = ['name']; //允许验证可用字段

    /**
     * 关联菜单
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function menus() {
        return $this->hasMany(Menu::class, 'power_item_id');
    }

    /**
     * 关联所在组处理
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function group() {
        return $this->hasOne(ItemGroup::class, 'id', 'power_item_group_id');
    }

    /**
     * 关联所在角色
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles() {
        return $this->belongsToMany(Role::class, 'power_role_item', 'power_item_id', 'power_role_id');
    }

    /**
     * 通过权限码获取权限项数据
     * @param string $code
     * @return self
     */
    public static function findCode(string $code) {
        return self::where('code', $code)->first();
    }

    /**
     * 判断用户是否有权限访问
     * @staticvar array $allows
     * @param UserContract $admin
     * @param string $code
     * @param bool $noneDefault
     * @return bool
     */
    public static function allow(UserContract $admin, string $code, bool $noneDefault = false) {
        static $allows = [];
        if (isset($allows[$admin->getKey()][$code])) {
            return $allows[$admin->getKey()][$code];
        }
        $item = static::where('code', '=', $code)->first();
        if (empty($item) || $item->status !== 'enable') {//不存在或禁用返回默认值
            $allow = $noneDefault;
        } else {
            $allow = RoleItem::where('power_item_id', '=', $item->getKey())
                            ->whereIn('power_role_id', function ($query)use ($admin) {
                                $query->from('power_role_admin')
                                ->where('power_admin_id', '=', $admin->getKey())
                                ->select('power_role_id');
                            })
                            ->count() ? $item : false;
        }
        return $allows[$admin->getKey()][$code] = $allow;
    }

    /**
     * 获取指定用户的所有允许权限项
     * @param UserContract $admin
     * @param null|string|array $with
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function items(UserContract $admin, $with = null) {
        $query = static::whereIn('power_item_id', function ($query) use ($admin) {
                    static::addItemWhere($query, $admin);
                })->where('status', '=', 'enable')
                ->orderBy('code', 'asc');
        if ($with) {
            $query->with($with);
        }
        return $query->get();
    }

    /**
     * 处理权限查询
     * @param Illuminate\Database\Eloquent\Builder $builder
     * @param UserContract $admin
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function addItemWhere($builder, UserContract $admin) {
        return $builder->from('power_role_item')
                        ->whereIn('power_role_id', function ($query) use ($admin) {//关联权限
                            $query->from('power_role_admin')
                            ->where('power_admin_id', '=', $admin->getKey())
                            ->select('power_role_id');
                        })
                        ->whereIn('power_role_id', function ($query) {//禁用的不能提取
                            $query->from('power_role')
                            ->where('status', '=', 'enable')
                            ->select('id');
                        })
                        ->select('power_item_id')
                        ->distinct();
    }
}
