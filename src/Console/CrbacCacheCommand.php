<?php

/*
 * 缓存管理命令
 */

namespace Laravel\Crbac\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Laravel\Crbac\Services\CacheService;

class CrbacCacheCommand extends Command {

	//控制台命令名
	protected $name = 'crbac:cache';
	//控制台备注说明
	protected $description = '管理Crbac缓存（清除权限/菜单/全部缓存）';

	/**
	 * 执行控制台命令
	 */
	public function handle() {
		$action = $this->argument('action');
		switch ($action) {
			case 'clear':
				CacheService::clearAll();
				$this->info('已清除所有CRBAC缓存。');
				break;
			case 'clear-perm':
				CacheService::clearPermission();
				$this->info('已清除所有权限缓存。');
				break;
			case 'clear-menu':
				CacheService::clearMenus();
				$this->info('已清除所有菜单缓存。');
				break;
			default:
				$this->error('未知操作：' . $action . '，可用操作：clear|clear-perm|clear-menu');
				return 1;
		}
	}

	/**
	 * 获取控制命令参数
	 * @return array
	 */
	protected function getArguments() {
		return [
			['action', InputArgument::REQUIRED, '操作类型：clear|clear-perm|clear-menu'],
		];
	}

}
