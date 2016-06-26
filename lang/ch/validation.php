<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => ':attribute 必须被接受.',
    'active_url'           => ':attribute 不是一个有效的网址.',
    'after'                => ':attribute 日期必须在 :date 之后.',
    'alpha'                => ':attribute 仅只能包含字母.',
    'alpha_dash'           => ':attribute 仅只能包含字母,数字,减号,下划线.',
    'alpha_num'            => ':attribute 仅只能包含字母,数字.',
    'array'                => ':attribute 必须是个数组',
    'before'               => ':attribute 日期必须在 :date 之前.',
    'between'              => [
        'numeric' => ':attribute 必须在 :min 和 :max 之间.',
        'file'    => ':attribute 必须在 :min 和 :max K字节之间.',
        'string'  => ':attribute 必须在 :min 和 :max 字符之间.',
        'array'   => ':attribute 必须在 :min 和 :max 项之间.',
    ],
    'boolean'              => ':attribute 必须为真或假.',
    'confirmed'            => ':attribute 确认值不相同.',
    'date'                 => ':attribute 不是有效日期.',
    'date_format'          => ':attribute 不匹配格式 :format.',
    'different'            => ':attribute 和 :other 必须不同.',
    'digits'               => ':attribute 必须为 :digits 位数字.',
    'digits_between'       => ':attribute 必须为 :min 到 :max 位数字.',
    'email'                => ':attribute 邮箱格式不正确',
    'filled'               => ':attribute 必填.',
    'exists'               => ':attribute 选择无效.',
    'image'                => ':attribute 必须是图片.',
    'in'                   => ':attribute 选项无效.',
    'integer'              => ':attribute 必须是整数.',
    'ip'                   => ':attribute 必须是有效IP地址.',
    'max'                  => [
        'numeric' => ':attribute 不能大于 :max.',
        'file'    => ':attribute 不能大于 :max K字节.',
        'string'  => ':attribute 不能大于 :max 字符数.',
        'array'   => ':attribute 不能超过 :max 项.',
    ],
    'mimes'                => ':attribute 文件类型必须是: :values.',
    'min'                  => [
        'numeric' => ':attribute 不能小于 :min.',
        'file'    => ':attribute 不能小于 :min K字节.',
        'string'  => ':attribute 不能小于 :min 字符数',
        'array'   => ':attribute 不能小于 :min 项.',
    ],
    'not_in'               => ':attribute 选择无效.',
    'numeric'              => ':attribute 必须是个数字',
    'regex'                => ':attribute 格式无效.',
    'required'             => ':attribute 不能为空.',
    'required_if'          => ':other 为 :value 时 :attribute 必填.',
    'required_with'        => ':values 指定时 :attribute 必填.',//有一个指定则必填
    'required_with_all'    => ':values 指定时 :attribute 必填.',//全部指定则必填
    'required_without'     => ':values 未指定时 :attribute 必填.',//有一个未指定则必填
    'required_without_all' => ':values 未指定时 :attribute 必填.',//全部未指定则必填
    'same'                 => ':attribute 和 :other 必须匹配.',
    'size'                 => [
        'numeric' => ':attribute 必需是 :size.',
        'file'    => ':attribute 必需是 :size K字节.',
        'string'  => ':attribute 必需是 :size 字符.',
        'array'   => ':attribute  必须包含 :size 项.',
    ],
    'string'               => ':attribute 必须是字符串.',
    'timezone'             => ':attribute 必须是有效时区.',
    'unique'               => ':attribute 已经占用.',
    'url'                  => ':attribute URL地址无效.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [],

];
