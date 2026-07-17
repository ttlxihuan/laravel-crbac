<?php

/*
 * 系统配置
 */

namespace Laravel\Crbac\Controllers\Power;

use Laravel\Crbac\Models\Power\Config;
use Laravel\Crbac\Controllers\Controller;

class ConfigController extends Controller {

    //备注说明
    protected $description = '系统配置';

    /**
     * 配置列表
     * @return view
     * @methods(GET)
     */
    public function lists() {
        $where = [
            'key' => 'like',
        ];
        $order = ['key' => 'key', 'value' => 'value', 'updated' => 'updated_at'];
        $default = ['order' => 'updated', 'by' => 'desc'];
        list($lists, $toOrder) = $this->listsSelect(Config::class, $where, $order, $default);
        $description = $this->description;
        return view('power.config.lists', compact('lists', 'description', 'toOrder'));
    }

    /**
     * 编辑配置项
     * @param Config $item
     * @return mixed
     * @methods(GET,POST)
     */
    public function edit(Config $item = null) {
        $option = ['key', 'value', 'type', 'comment', 'status'];
        if ($item === null || isPower('allow_edit_config_key')) {
            $option[] = 'key';
        }
        return $this->modelEdit($item, 'power.config.edit', Config::class, $option);
    }

    /**
     * 删除配置项
     * @param Config $item
     * @return mixed
     * @methods(GET)
     */
    public function delete(Config $item) {
        $result = $this->modelDelete($item);
        \Laravel\Crbac\Services\CacheService::clearConfig();
        return $result;
    }

}
