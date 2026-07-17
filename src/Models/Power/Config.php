<?php

/*
 * 系统配置
 */

namespace Laravel\Crbac\Models\Power;

use Laravel\Crbac\Models\Model;

class Config extends Model {
    use \Laravel\Crbac\Models\GetMappingTrait;

    public static $_STATUS = [ //状态配置
        'enable' => '启用',
        'disable' => '禁用'
    ];

    public static $_validator_rules = [ //验证规则
        'key'=>'required|max:100|unique:power_config', // varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '配置键',
        'value'=>'nullable', // text COLLATE utf8mb4_unicode_ci COMMENT '配置值',
        'type'=>'required|in:string,number,json,boolean', // enum('string','number','json','boolean') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'string' COMMENT '配置值类型',
        'comment'=>'required|max:255', // varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '说明',
        'status'=>'required|in:disable,enable', // enum('disable','enable') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'enable' COMMENT '状态，enable为启用',
    ];

    public static $_validator_description = [ //验证字段说明
        'key'=>'配置键', // varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '配置键',
        'value'=>'配置值', // text COLLATE utf8mb4_unicode_ci COMMENT '配置值',
        'type'=>'配置类型', // enum('string','number','json','boolean') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'string' COMMENT '配置值类型',
        'comment'=>'说明', // varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '说明',
        'status'=>'状态', // enum('disable','enable') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'enable' COMMENT '状态，enable为启用',
    ];
    
    protected $table = 'power_config'; //表名
}
