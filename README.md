laravel5+、laravel6+、laravel7+、laravel8+、laravel9+ 权限管理系统
===============

该模板依赖于laravel，包含基本权限管理所需要功能改模块，安装后简单配置即可使用，并且提供View，比较适合项目初期。

目录
-----------------
* [安装](#installation)
* [配置](#configuration)
* [语言](#lang)
* [表及数据](#table)
* [使用](#use)
* [注意项](#attention)

Installation
------------
**注意** 安装前确定laravel已经安装就绪

### 使用composer安装
```shell
composer require ttlphp/laravel-crbac dev-master
```
### 使用GIT安装
使用GIT命令下载程序包。
```
git clone https://github.com/ttlxihuan/laravel-crbac
```
修改自动加载配置

打开 `vendor/composer/autoload_psr4.php` 添加命令空间加载目录：
```php
'Laravel\\Crbac\\' => array($vendorDir . '/laravel-crbac/src'),
```
打开 `vendor/composer/autoload_static.php` 添加命令空间加载目录，此文件有多处需要添加，暂不示例

打开 `vendor/composer/autoload_files.php` 添加助手函数加载：
```php
$vendorDir . '/laravel-crbac/src/helpers.php',
```

Configuration
-------------
需要在laravel中启用应用，需要作几个配置修改。

打开配置文件 `config/app.php` 添加应用服务到容器:
```php
Laravel\Crbac\ServiceProvider::class,
```
打开配置文件 `config/auth.php` 添加授权模型:
```php
'model' => Laravel\Crbac\Models\Power\Admin::class,
```
配置数据，运行安装命令（注意多次运行此命令会覆盖重写自带的表和数据）
```shell
php artisan crbac:table
```
打开浏览器验证效果

lang
---------
项目中提供了中文基础文件，方便在验证分页等信息是自由切换，项目全部以中文提示为语言基础。
安装中文需要运行命令
```shell
php artisan crbac:lang
```
命令会复制预定中文字典复制到语言配置目录中。

打开配置文件 `config/app.php` 修改默认语言
```php
'locale' => 'cn',
```

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

use
-------------
要使用Crbac功能必需配置授权模型且必需继承预定模型（参考下面介绍），如果没有配置则整个项目会跳过路由配置和权限处理初始化，即无法正常使用权限功能。

#### 权限校验
Crbac权限处理会自动绑定在使用了auth中间件的路由上，即新增路由只需要追加auth中间件即可自动进行权限核验。

#### 权限管理
权限使用，管理员 —> 角色（多个） —> 权限项（多个）

`每个管理员可以绑定多个角色，每个角色可以绑定多个权限项，之间允许交叉和重复`

菜单使用，管理员 —> 菜单组 —> 权限项（多个）

`每个管理员绑定一套菜单组，方便各组管理员展示完全不同的菜单结构`

#### 定制权限
预定权限只适用预定全部功能，额外增加的权限即需要定制权限，路由和菜单权限预定功能即可满足，可自行配置增加权限组、权限项、菜单组、菜单项、角色等操作。

如果需要页面内或逻辑权限也可以定制添加，并需要在业务代码中增加权限判断，可以使用函数：
```php
// 必需要有登录数据，否则永远返回false
// 此函数是所有权限验证通用函数
isPower($code, $default = false):bool
```

更多协助函数可参考文件：`./vendor/ttlphp/laravel-crbac/src/helpers.php`

#### 登录和退出
Crbac会在路由加载完成后判断是否存在别名为 login 和 logout 两个路由，如果没有则会自动增加预定好的两个登录和退出路由，保证权限基本正常使用。如果需要截取预定登录和退出路由只需要手动添加两个别名路由即可。


attention
-------------
view中的JS及CSS未经过严格的兼容测试，一般支持CSS3的浏览器均无异常。

权限管理样式并不能保证通用已经存在的项目，如果需要改变样式可以将项目中的视图文件进行重写并放到自己视图处，此时预定视图会被劫持。