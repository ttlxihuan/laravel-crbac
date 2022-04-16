<?php

/*
 * 创建表
 */

namespace Laravel\Crbac\Console;

use Schema;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class CrbacTableCommand extends Command {

    //控制台命令名
    protected $name = 'crbac:table';
    //控制台备注说明
    protected $description = '创建Crbac所需表';

    /**
     * 执行控制台命令
     */
    public function handle() {
        $basePath = $this->laravel->basePath();
        $path = str_replace($basePath, '', __DIR__ . '/../Database/Tables');
        $this->call('migrate', ['--path' => $path]);
        if (!$this->option('empty')) {//写入基本数据
            //写数据处理
            $this->call('crbac:seeder');
        }
        Schema::dropIfExists('migrations');
    }

    /**
     * 获取控制命令选项
     * @return array
     */
    protected function getOptions() {
        return [
            ['empty', null, InputOption::VALUE_NONE, '只创建空表，不要插入基本数据'],
        ];
    }

}
