<?php

/*
 * 系统配置相关
 */

namespace Laravel\Crbac\Services\Power;

use Laravel\Crbac\Models\Model;
use Laravel\Crbac\Services\CacheService;
use Laravel\Crbac\Services\Power\Service;
use Laravel\Crbac\Services\Service as BaseService;
use Laravel\Crbac\Models\Power\Config as ConfigModel;

class Config extends Service {

    /**
     * 修改数据前处理：自动设置updated_at
     * @param array $data
     * @param BaseService $service
     * @param Model|string $item
     */
    protected function editBefore(array &$data, BaseService $service, $item) {
        $data['updated_at'] = time();

        // 根据类型验证配置值
        if (isset($data['type']) && array_key_exists('value', $data)) {
            switch ($data['type']) {
                case 'number':
                    if ($data['value'] !== '' && !is_numeric($data['value'])) {
                        $this->setError('validator.value', '配置值必须为有效数字');
                        return false;
                    }
                    break;
                case 'json':
                    if ($data['value'] !== '') {
                        json_decode($data['value']);
                        if (json_last_error() !== JSON_ERROR_NONE) {
                            $this->setError('validator.value', '配置值必须为有效的JSON格式');
                            return false;
                        }
                    }
                    break;
                case 'boolean':
                    // 标准化布尔值
                    if (!in_array($data['value'], ['0', '1'], true)) {
                        $data['value'] = $data['value'] ? '1' : '0';
                    }
                    break;
            }
        }
    }

    /**
     * 修改数据后处理：清除配置缓存
     * @param Model $result
     * @param BaseService $service
     */
    protected function editAfter(Model $result, BaseService $service) {
        CacheService::clearConfig();
    }

    /**
     * 获取配置值
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null) {
        $all = $this->all();
        return array_key_exists($key, $all) ? $all[$key] : $default;
    }

    /**
     * 设置配置项
     * @param string $key
     * @param mixed $value
     * @param string $type
     * @return void
     */
    public function set($key, $value, $type = 'string') {
        $config = ConfigModel::where('key', $key)->first();
        if ($config) {
            $config->value = $value;
            $config->type = $type;
            $config->updated_at = time();
            $config->save();
        } else {
            ConfigModel::create([
                'key' => $key,
                'value' => $value,
                'type' => $type,
                'comment' => '',
                'updated_at' => time(),
            ]);
        }
        CacheService::clearConfig();
    }

    /**
     * 获取所有配置（key => value映射）
     * @return array
     */
    public function all() {
        return CacheService::get(CacheService::CONFIG_KEY) ?: $this->loadAndCache(86400);
    }

    /**
     * 从数据库加载并缓存
     * @param int $ttl
     * @return array
     */
    protected function loadAndCache($ttl) {
        $configs = ConfigModel::where('status', 'enable')->select('key', 'value')->pluck('value', 'key')->toArray();
        CacheService::put(CacheService::CONFIG_KEY, $configs, $ttl);
        return $configs;
    }

    /**
     * 删除配置项
     * @param string $key
     * @return void
     */
    public function forget($key) {
        ConfigModel::where('key', $key)->delete();
        CacheService::clearConfig();
    }

}
