<?php

/*
 * 复制语言包
 */

namespace Laravel\Crbac\Console;

use Illuminate\Console\Command;

class CrbacCopyLangCommand extends Command {

    //控制台命令名
    protected $name = 'crbac:lang';
    //控制台备注说明
    protected $description = '复制内置中文语言包到项目';

    /**
     * 执行控制台命令
     */
    public function handle() {
        $langPath = realpath($this->laravel->resourcePath('lang/'));
        if (!$langPath) {
            $langPath = realpath($this->laravel->basePath('lang/'));
        }
        if ($langPath) {
            $this->info('复制语言文件');
            $this->copy(__DIR__ . '/../../lang/', $langPath);
        } else {
            $this->error('找不到项目语言目录！');
        }
    }

    /**
     * 复制文件
     * @param type $srcDir
     * @param type $dstDir
     */
    protected function copy($srcDir, $dstDir) {
        $srcDir = realpath($srcDir);
        $size = strlen($srcDir);
        foreach (glob($srcDir . '/*') as $file) {
            $child = substr($file, $size);
            $dest = $dstDir . $child;
            if (is_dir($file)) {
                if (!file_exists($dest)) {
                    mkdir($dest);
                    $this->info('创建目录：' . realpath($dest));
                }
                $this->copy($file . '/', $dest);
            } elseif (!file_exists($dest)) {
                copy($file, $dest);
                $this->info('创建文件：' . realpath($dest));
            }
        }
    }

}
