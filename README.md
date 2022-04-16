laravel5 + 权限管理系统
===============

该模板依赖于laravel5，包含基本权限管理所需要功能改模块，安装后简单配置即可使用，并且提供View，比较适合项目初期。

目录
-----------------
* [安装](#installation)
* [配置](#configuration)
* [语言](#lang)
* [表及数据](#table)
* [注意项](#attention)

Installation
------------
**注意**安装前确定laravel5已经安装就绪，版本laravel5+。
使用GIT安装
使用GIT命令下载程序包。
```
git clone https://github.com/ttlxihuan/laravel-crbac
```


使用composer安装
```
composer require ttlphp/laravel-crbac dev-master
```


Configuration
-------------
需要在laravel5中启用应用，需要作几个配置修改。

打开 `config/app.php` 添加应用服务到容器:
```php
Laravel\Crbac\ServiceProvider::class,
```

当使用手动安装时需要做以下几个修改
打开 `vendor/composer/autoload_psr4.php` 添加命令空间加载目录：
```php
'Laravel\\Crbac\\' => array($vendorDir . '/laravel-crbac/src'),
```
打开 `vendor/composer/autoload_static.php` 添加命令空间加载目录，此文件有多处需要添加，暂不示例

打开 `vendor/composer/autoload_files.php` 添加助手函数加载：
```php
$vendorDir . '/laravel-crbac/src/helpers.php',
```


lang
---------
为了方便额外添加了几个中文包文件，在安装的lang目录下。
需要中文的可以把 `lang/ch` 目录复制到laravel5 的 `resources/lang/ch` 下并修改 `config/app.php`
```php
'locale' => 'ch',
```
实现中文验证及分页相关语言显示。


table
-------------
当使用手动安装或初始化权限数据表结构时，在这里提供了 artisan 命令，方便手动重置表结构及基础数据。

### 表结构如下

 表名               | 说明
:-------------------|:----------
 power_role_admin   | 管理员表
 power_item         | 权限项表，记录所有权限项基础数据
 power_item_group   | 权限项组表，用于权限项分组，当前只设计一级。
 power_menu         | 菜单表，记录所有菜单基础数据，并且可以关联到权限项
 power_menu_group   | 菜单组表，用于区分不同菜单组的菜单结构。
 power_menu_level   | 菜单层级表，记录菜单的组成层级结构。
 power_role         | 角色表。
 power_role_admin   | 角色与管理员关联表
 power_role_item    | 角色与权限项关联表
 power_route        | 路由记录表，方便添加权限项。


安装或重置表及数据命令
```
php artisan crbac:table
```


attention
-------------
view中的JS及CSS未经过严格的兼容测试，一般支持CSS3的浏览器均无异常。
