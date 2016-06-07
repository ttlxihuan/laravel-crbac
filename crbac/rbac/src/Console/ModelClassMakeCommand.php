<?php

/*
 * Model 类生成器
 */

namespace XiHuan\Crbac\Console;

use DB;
use Illuminate\Console\Command;

class ModelClassMakeCommand extends Command {

    //控制台命令名
    protected $signature = 'model:make{?table:"指定表"}';
    //控制台备注说明
    protected $description = '创建Model类，并根据说结构生成部分验证代码';

    /*
     * 作用：执行控制台命令
     * 参数：无
     * 返回值：void
     */
    public function fire() {
        $table = $this->argument('table'); //指定表
        if ($table) {
            $tables = [$table];
        } else {
            $tables = $this->tableLists();
        }
        foreach ($tables as $table) {
            $columns = $this->descTable($table);
        }
    }
    /*
     * 作用：获取表结构
     * 参数：$table string 表名
     * 返回值：array
     */
    private function descTable($table) {
        return DB::select('desc ' . DB::getQueryGrammar()->wrapTable($table));
    }
    /*
     * 作用：获取表列表
     * 参数：无
     * 返回值：array
     */
    private function tableLists() {
        return DB::select('show tables');
    }
    /*
     * 作用：生成文件
     * 参数：无
     * 返回值：bool
     */
    private function createFile() {
        file_put_contents($filename, $data);
    }
}
