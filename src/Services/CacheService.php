<?php

/*
 * 缓存服务
 */

namespace Laravel\Crbac\Services;

use Cache;

class CacheService {

	//缓存key统一前缀
	const PREFIX = 'crbac.';
	//权限缓存全局版本key
	const PERM_VERSION_KEY = 'crbac.perm.version';
	//菜单缓存全局版本key
	const MENU_VERSION_KEY = 'crbac.menu.version';
	//用户权限版本号key前缀（每个用户独立版本）
	const PERM_USER_VERSION_PREFIX = 'crbac.perm.uver.';
	//用户菜单版本号key前缀（每个用户独立版本）
	const MENU_USER_VERSION_PREFIX = 'crbac.menu.uver.';
	//配置缓存key（不含前缀）
	const CONFIG_KEY = 'config';
	//版本号缓存有效期（秒）
	const VERSION_TTL = 31536000;

	/**
	 * 获取缓存存储驱动
	 * @return \Illuminate\Contracts\Cache\Repository
	 */
	protected static function store() {
		return Cache::store();
	}

	/**
	 * 判断缓存是否启用
	 * @return bool
	 */
	public static function enabled() {
		return config('cache.power.enabled', true);
	}

	/**
	 * 写入缓存（兼容Laravel版本差异处理TTL单位）
	 * @param string $key
	 * @param mixed $value
	 * @param int $ttl 秒
	 * @return void
	 */
	protected static function write($key, $value, $ttl) {
		$store = static::store();
		if (version_compare(app()->version(), '5.6.0', '<')) {
			//Laravel 5.0-5.5: put()第三个参数为分钟
			$store->put($key, $value, ceil($ttl / 60));
		} else {
			//Laravel 5.6+: put()第三个参数为秒
			$store->put($key, $value, $ttl);
		}
	}

	/**
	 * 获取缓存
	 * @param string $key
	 * @return mixed
	 */
	public static function get($key) {
		if (!static::enabled()) {
			return null;
		}
		return static::store()->get(static::PREFIX . $key);
	}

	/**
	 * 设置缓存
	 * @param string $key
	 * @param mixed $value
	 * @param int $ttl 秒
	 * @return void
	 */
	public static function put($key, $value, $ttl) {
		if (!static::enabled()) {
			return;
		}
		static::write(static::PREFIX . $key, $value, $ttl);
	}

	/**
	 * 删除缓存
	 * @param string $key
	 * @return void
	 */
	public static function forget($key) {
		static::store()->forget(static::PREFIX . $key);
	}

	/**
	 * 获取权限缓存全局版本号
	 * @return int
	 */
	protected static function getPermVersion() {
		return static::store()->get(static::PERM_VERSION_KEY) ?: 1;
	}

	/**
	 * 获取菜单缓存全局版本号
	 * @return int
	 */
	protected static function getMenuVersion() {
		return static::store()->get(static::MENU_VERSION_KEY) ?: 1;
	}

	/**
	 * 获取用户权限缓存版本号（不低于全局版本号）
	 * @param int $adminId
	 * @return int
	 */
	protected static function getUserPermVersion($adminId) {
		$globalVersion = static::getPermVersion();
		$userVersion = static::store()->get(static::PERM_USER_VERSION_PREFIX . $adminId) ?: 0;
		return max($globalVersion, $userVersion);
	}

	/**
	 * 获取用户菜单缓存版本号（不低于全局版本号）
	 * @param int $adminId
	 * @return int
	 */
	protected static function getUserMenuVersion($adminId) {
		$globalVersion = static::getMenuVersion();
		$userVersion = static::store()->get(static::MENU_USER_VERSION_PREFIX . $adminId) ?: 0;
		return max($globalVersion, $userVersion);
	}

	/**
	 * 生成权限缓存key（含版本号）
	 * @param int $adminId
	 * @return string
	 */
	public static function permKey($adminId) {
		return 'perm.' . static::getUserPermVersion($adminId) . '.' . $adminId;
	}

	/**
	 * 生成菜单缓存key（含版本号）
	 * @param int $adminId
	 * @return string
	 */
	public static function menuKey($adminId) {
		return 'menu.' . static::getUserMenuVersion($adminId) . '.' . $adminId;
	}

	/**
	 * 清除权限缓存
	 * @param int|null $adminId 为null时递增版本号使所有旧缓存失效
	 * @return void
	 */
	public static function clearPermission($adminId = null) {
		if (is_null($adminId)) {
			//递增全局版本号使所有用户的旧缓存key失效
			$version = static::getPermVersion();
			static::write(static::PERM_VERSION_KEY, $version + 1, static::VERSION_TTL);
		} else {
			//递增该用户版本号使其旧缓存key失效
			$userVersion = static::getUserPermVersion($adminId);
			static::write(static::PERM_USER_VERSION_PREFIX . $adminId, $userVersion + 1, static::VERSION_TTL);
		}
	}

	/**
	 * 清除菜单缓存
	 * @param int|null $adminId 为null时递增版本号使所有旧缓存失效
	 * @return void
	 */
	public static function clearMenus($adminId = null) {
		if (is_null($adminId)) {
			//递增全局版本号使所有用户的旧缓存key失效
			$version = static::getMenuVersion();
			static::write(static::MENU_VERSION_KEY, $version + 1, static::VERSION_TTL);
		} else {
			//递增该用户版本号使其旧缓存key失效
			$userVersion = static::getUserMenuVersion($adminId);
			static::write(static::MENU_USER_VERSION_PREFIX . $adminId, $userVersion + 1, static::VERSION_TTL);
		}
	}

	/**
	 * 清除配置缓存
	 * @return void
	 */
	public static function clearConfig() {
		static::forget(static::CONFIG_KEY);
	}

	/**
	 * 清除所有CRBAC缓存
	 * @return void
	 */
	public static function clearAll() {
		static::clearPermission();
		static::clearMenus();
		static::clearConfig();
	}

}
