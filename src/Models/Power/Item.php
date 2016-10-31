<?php

/*
 * 权限项
 */

namespace XiHuan\Crbac\Models\Power;

use XiHuan\Crbac\Models\Model;
use XiHuan\Crbac\Models\StatusTrait;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;

class Item extends Model {

    use StatusTrait;

    public static $_validator_rules = [//验证规则
        'name' => 'required|between:3,30', // varchar(35) not null comment '权限项名称',
        'code' => 'required|between:3,75|unique:power_item|string', // varchar(80) unique not null comment '权限项代码',
        'power_item_group_id' => 'required|exists:power_item_group', //int unsigned not null comment '权限项组ID',
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
    protected $primaryKey = 'power_item_id'; //主键名
    protected static $validates = ['name']; //允许验证可用字段

    /*
     * 作用：关联菜单
     * 参数：无
     * 返回值：Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function menus() {
        return $this->hasMany(Menu::class, $this->primaryKey);
    }
    /*
     * 作用：关联所在组处理
     * 参数：无
     * 返回值：Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function group() {
        return $this->hasOne(ItemGroup::class, 'power_item_group_id', 'power_item_group_id');
    }
    /*
     * 作用：关联所在角色
     * 参数：无
     * 返回值：Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles() {
        return $this->belongsToMany(Role::class, 'power_role_item', 'power_item_id', 'power_role_id');
    }
    /*
     * 作用：通过权限码获取权限项数据
     * 参数：$code string 权限码
     * 返回值：self
     */
    public static function findCode($code) {
        return self::where('code', $code)->first();
    }
    /*
     * 作用：判断用户是否有权限访问
     * 参数：$admin Illuminate\Contracts\Auth\Authenticatable 当前登录用户Model
     *      $code string 权限码
     *      $default bool 如果权限不存在或禁用返回默认值
     * 返回值：bool
     */
    public static function allow(UserContract $admin, $code, $default = false) {
        static $allows = [];
        if (isset($allows[$admin->getKey()][$code])) {
            return $allows[$admin->getKey()][$code];
        }
        $item = static::where('code', '=', $code)->first();
        if (empty($item) || $item->status !== 'enable') {//不存在或禁用返回默认值
            $allow = $default;
        } else {
            $allow = RoleItem::where($item->getKeyName(), '=', $item->getKey())
                            ->whereIn('power_role_id', function($query)use($admin) {
                                $query->from('power_role_admin')
                                ->where('admin_id', '=', $admin->getKey())
                                ->select('power_role_id');
                            })
                            ->count() ? $item : false;
        }
        return $allows[$admin->getKey()][$code] = $allow;
    }
    /*
     * 作用：获取指定用户的所有允许权限项
     * 参数：$admin Illuminate\Contracts\Auth\Authenticatable 当前登录用户Model
     *       $with null|string|array 关联查询
     * 返回值：Illuminate\Database\Eloquent\Collection
     */
    public static function items(UserContract $admin, $with = null) {
        $query = static::whereIn('power_item_id', function($query) use($admin) {
                    static::addItemWhere($query, $admin);
                })->where('status', '=', 'enable');
        if ($with) {
            $query->with($with);
        }
        return $query->get();
    }
    /*
     * 作用：处理权限查询
     * 参数：$builder Illuminate\Database\Eloquent\Builder
     *       $admin Illuminate\Contracts\Auth\Authenticatable 当前登录用户Model
     * 返回值：Illuminate\Database\Eloquent\Builder
     */
    //
    public static function addItemWhere($builder, UserContract $admin) {
        return $builder->from('power_role_item')
                        ->whereIn('power_role_id', function($query) use($admin) {//关联权限
                            $query->from('power_role_admin')
                            ->where('admin_id', '=', $admin->getKey())
                            ->select('power_role_id');
                        })
                        ->whereIn('power_role_id', function($query) {//禁用的不能提取
                            $query->from('power_role')
                            ->where('status', '=', 'enable')
                            ->select('power_role_id');
                        })
                        ->select('power_item_id')
                        ->distinct();
    }
}
