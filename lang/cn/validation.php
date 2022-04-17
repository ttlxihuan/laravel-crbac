<?php

return [

    /*
    |--------------------------------------------------------------------------
    | 验证语言字典
    |--------------------------------------------------------------------------
    | 特别注意，以下验证是以 laravel9+ 源进行翻译，低版本不一定保证有以下所有验证规则
    | 如果不清楚可以去查看对应版本文档或者查看模型验证源代码。
    | 注意占位符，占位符在实际验证失败后输出前会进行替换
    | 占用符是以冒号开始后面是占位别名，主要有 :attribute、:value、:min、:max、:date、:other 等，
    | 占位是个数是别名是验证规则预定的不可修改，在配置语言字典时可以不使用但不能自定义占位别名
    */

    'accepted' => ':attribute 必须接受.',
    'accepted_if' => ':other 为 :value 时 :attribute 必须接受.',
    'active_url' => ':attribute 不是一个有效的网址.',
    'after' => ':attribute 日期必须在 :date 之后.',
    'after_or_equal' => ':attribute 日期必须等于或在 :date 之后.',
    'alpha' => ':attribute 仅只能包含字母.',
    'alpha_dash' => ':attribute 仅只能包含字母,数字,减号,下划线.',
    'alpha_num' => ':attribute 仅只能包含字母,数字.',
    'array' => ':attribute 必须是个数组.',
    'before' => ':attribute 日期必须在 :date 之前.',
    'before_or_equal' => ':attribute 日期必须等于或在 :date 之前.',
    'between' => [
        'numeric' => ':attribute 大小必须在 :min 和 :max 之间.',
        'file'    => ':attribute 空间必须在 :min 和 :max K字节之间.',
        'string'  => ':attribute 长度必须在 :min 和 :max 字符之间.',
        'array'   => ':attribute 个数必须在 :min 和 :max 之间.',
    ],
    'boolean' => ':attribute 必须为真或假.',
    'confirmed' => ':attribute 确认值不相同.',
    'current_password' => '密码不正确.',
    'date' => ':attribute 不是有效日期.',
    'date_equals' => ':attribute 日期必须等于 :date.',
    'date_format' => ':attribute 不匹配格式 :format.',
    'declined' => ':attribute 必须拒绝.',
    'declined_if' => ':other 为 :value 时 :attribute 必须拒绝.',
    'different' => ':attribute 和 :other 必须不同.',
    'digits' => ':attribute 必须为 :digits 位数字.',
    'digits_between' => ':attribute 必须为 :min 到 :max 位数字.',
    'dimensions' => ':attribute 不在图片尺寸范围.',
    'distinct' => ':attribute 的值传入重复.',
    'email' => ':attribute 邮箱格式不正确.',
    'ends_with' => ':attribute 必须以 :values 任一结尾.',
    'enum' => ':attribute 选项无效.',
    'exists' => ':attribute 选择无效.',
    'file' => ':attribute 必须是一个文件.',
    'filled' => ':attribute 必填.',
    'gt' => [
        'numeric' => ':attribute 大小必须大于 :value.',
        'file'    => ':attribute 空间必须大于 :value K字节.',
        'string'  => ':attribute 长度必须大于 :value字符.',
        'array'   => ':attribute 个数必须大于 :value.',
    ],
    'gte' => [
        'numeric' => ':attribute 大小必须大于或等于 :value.',
        'file'    => ':attribute 空间必须大于或等于 :value K字节.',
        'string'  => ':attribute 长度必须大于或等于 :value字符.',
        'array'   => ':attribute 个数必须大于或等于 :value.',
    ],
    'image' => ':attribute 必须是图片.',
    'in' => ':attribute 选项无效.',
    'in_array' => ':attribute 不存在 :other.',
    'integer' => ':attribute 必须是整数.',
    'ip' => ':attribute 必须是有效IP地址.',
    'ipv4' => ':attribute 必须是有效IP4地址.',
    'ipv6' => ':attribute 必须是有效IP6地址.',
    'json' => ':attribute 必须是有效josn字符串.',
    'lt' => [
        'numeric' => ':attribute 大小必须小于 :value.',
        'file'    => ':attribute 空间必须小于 :value K字节.',
        'string'  => ':attribute 长度必须小于 :value字符.',
        'array'   => ':attribute 个数必须小于 :value.',
    ],
    'lte' => [
        'numeric' => ':attribute 大小必须小于或等于 :value.',
        'file'    => ':attribute 空间必须小于或等于 :value K字节.',
        'string'  => ':attribute 长度必须小于或等于 :value字符.',
        'array'   => ':attribute 个数必须小于或等于 :value.',
    ],
    'mac_address' => ':attribute 必须是有效MAC地址',
    'max' => [
        'numeric' => ':attribute 大小最大 :value.',
        'file'    => ':attribute 空间最大 :value K字节.',
        'string'  => ':attribute 长度最大 :value字符.',
        'array'   => ':attribute 个数最大 :value.',
    ],
    'mimes' => ':attribute 文件后缀必须是 :values.',
    'mimetypes' => ':attribute 文件MIME必须是 :values.',
    'min' => [
        'numeric' => ':attribute 大小最小 :value.',
        'file'    => ':attribute 空间最小 :value K字节.',
        'string'  => ':attribute 长度最小 :value字符.',
        'array'   => ':attribute 个数最小 :value.',
    ],
    'multiple_of' => ':attribute 必须是 :value 的倍数.',
    'not_in' => ':attribute 选择无效.',
    'not_regex' => ':attribute 格式无效.',
    'numeric' => ':attribute 必须是个数字.',
    'present' => ':attribute 必须存在有值.',
    'prohibited' => ':attribute 不可存在.',
    'prohibited_if' => ':other 为 :value 时 :attribute 不可存在.',
    'prohibited_unless' => ':other 不为 :value 时 :attribute 不可存在.',
    'prohibits' => ':other 不含 :value 时 :attribute 不可存在.',
    'regex' => ':attribute 格式无效.',
    'required' => ':attribute 不能为空.',
    'required_array_keys' => ':attribute 是数组且必须包含 :values.',
    'required_if' => ':other 为 :value 时 :attribute 必填.',
    'required_unless' => ':other 不为 :value 时 :attribute 必填.',
    'required_with' => ':values 指定时 :attribute 必填.',
    'required_with_all' => ':values 指定时 :attribute 必填.',
    'required_without' => ':values 未指定时 :attribute 必填.',
    'required_without_all' => ':values 未指定时 :attribute 必填.',
    'same' => ':attribute 和 :other 必须匹配.',
    'size' => [
        'numeric' => ':attribute 必需是 :size.',
        'file'    => ':attribute 必需是 :size K字节.',
        'string'  => ':attribute 必需是 :size 字符.',
        'array'   => ':attribute  必须包含 :size 项.',
    ],
    'starts_with' => ':attribute 必须以 :values 任一开始.',
    'string' => ':attribute 必须是字符串.',
    'timezone' => ':attribute 必须是有效时区.',
    'unique' => ':attribute 已经占用.',
    'uploaded' => ':attribute 上传失败.',
    'url' => ':attribute URL地址无效.',
    'uuid' => ':attribute 必须是有效UUID.',

    /*
    |--------------------------------------------------------------------------
    | 以下是自定义验证字段对应验证规则语言字典
    |--------------------------------------------------------------------------
    | 如果验证通用语言字典说明不能很好的解释验证失败原因可以在这里配置指定字段指定验证规则的独享输出验证语言字典
    | 指定后上面通用语言字典将被劫持为以下语言字典，并进行占位替换处理，占位符个数以通用语言字典中为准
    |
    */

    'custom' => [
//        'attribute-name' => [
//            'rule-name' => 'custom-message',
//        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | 以下自定义验证字段别名，可以修改在语言字典中占位内容
    |--------------------------------------------------------------------------
    | 如果有些字段名比较长或者比较特殊，可以在这里自定义字段别名，当验证失败时就可以自动替换对应占位内容
    | 占位符 :attribute 
    |
    */

    'attributes' => [],

];