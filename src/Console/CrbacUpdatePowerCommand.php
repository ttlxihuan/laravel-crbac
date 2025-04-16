<?php

/*
 * 自动更新权限项
 */

namespace Laravel\Crbac\Console;

use ReflectionClass;
use ReflectionMethod;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Laravel\Crbac\Models\Power\Role;
use Laravel\Crbac\Models\Power\Item;
use Laravel\Crbac\Models\Power\Menu;
use Illuminate\Support\Facades\Route;
use Laravel\Crbac\Models\Power\RoleItem;
use Laravel\Crbac\Models\Power\ItemGroup;
use Laravel\Crbac\Models\Power\MenuGroup;
use Laravel\Crbac\Models\Power\MenuLevel;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Laravel\Crbac\Models\Power\Route as RouteModel;

class CrbacUpdatePowerCommand extends Command {

    /**
     * @var string 命令结构
     */
    protected $name = 'crbac:power';

    /**
     * @var string 命令说明
     */
    protected $description = '自动更新权限项&菜单，控制器必需指定 description 属性。更新分两类：mvc结构路由&配置路由。';

    /**
     * @var string 命令说明
     */
    protected $path;

    /**
     * @var string 命令说明
     */
    protected $namespace;

    /**
     * @var Role 角色
     */
    protected $role;

    /**
     * @var MenuGroup 菜单组
     */
    protected $menuGroup;

    /**
     * 执行控制台命令。
     */
    public function handle() {
        if (!app('translator')->has('power')) {
            $this->error('请先配置好 power 语言，建议运行 php artisan crbac:lang 后，再修改默认语言配置');
            return;
        }
        $inserts = [];
        $now = time();
        $this->path = realpath($this->argument('path'));
        $this->namespace = trim($this->argument('namespace'), '\\');
        $this->role = Role::where('name', $this->option('role'))->first();
        if (empty($this->role)) {
            $this->error('未知角色名：' . $this->option('role'));
        }
        $this->menuGroup = MenuGroup::where('name', $this->option('menu-group'))->first();
        if (empty($this->menuGroup)) {
            $this->error('未知菜单组：' . $this->option('menu-group'));
        }
        if ($this->option('rework')) { // 删除重新写
            $this->info('删除已经存在的权限项');
            DB::beginTransaction();
            $itemGroupIds = [];
            $count = 0;
            while (true) {
                $items = Item::where('code', 'like', addslashes($this->namespace) . '\\\\%')
                        ->select('id', 'power_item_group_id')
                        ->orderBy('id', 'asc')
                        ->limit(200)
                        ->get();
                if (count($items) <= 0) { // 已经删除完了
                    break;
                }
                $count += count($items);
                $ids = [];
                foreach ($items as $item) {
                    $ids[] = $item['id'];
                    $itemGroupIds[$item['id']] = $item['id'];
                }
                // 删除权限项
                Item::whereIn('id', $ids)->delete();
                // 删除权限项绑定的角色
                RoleItem::whereIn('power_item_id', $ids)->delete();
                // 获取绑定的菜单
                $menuIds = Menu::whereIn('power_item_id', $ids)->select('id')->get()->pluck('id')->toArray();
                // 删除菜单绑定的菜单组
                MenuLevel::whereIn('power_menu_id', $menuIds)->delete();
                // 删除权限项绑定的菜单
                Menu::whereIn('power_item_id', $ids)->delete();
            }
            if (count($itemGroupIds)) {
                // 删除权限项组（没有关联即可）
                $existsItemGroupIds = Item::whereIn('power_item_group_id', $itemGroupIds)->select('power_item_group_id')->get()->pluck('id')->toArray();
                if (count($existsItemGroupIds)) {
                    $itemGroupIds = array_diff($itemGroupIds, $existsItemGroupIds);
                }
                if (count($itemGroupIds)) {
                    ItemGroup::whereIn('id', $itemGroupIds)->delete();
                }
            }
            DB::commit();
            $this->info('已经删除权限项：' . $count);
        }
        foreach ($this->eachAction() as $data) {
            list($desc, $ref, $method, $methods) = $data;
            // 添加到路由列表中，用于手动添加权限项
            // 通用结构
            switch ($method->getName()) {
                case 'lists':
                    $this->addMenu($ref, $method, lang('power.lists', ['name' => $desc]), $desc);
                    break;
                case 'add':
                    if (!$ref->hasMethod('edit')) { // 没有编辑就不操作添加
                        continue 2;
                    }
                    $this->addItem($ref, $method, lang('power.add', ['name' => $desc]));
                    break;
                case 'edit':
                    $this->addItem($ref, $method, lang('power.edit', ['name' => $desc]));
                    break;
                case 'select':
                    continue 2;
                default:
                    // 提取配置注解
                    if (preg_match('/@power(Menu|Item)\s*\(\s*(\'[^\']+\'|"[^"]+")\s*\)/i', $method->getDocComment(), $matches)) {
                        $title = trim($matches[2], '"\'');
                        switch ($matches[1]) {
                            case 'Menu':
                                $this->addMenu($ref, $method, $title, $title);
                                break;
                            case 'Item':
                                $this->addItem($ref, $method, $title);
                                break;
                        }
                    } else {
                        $methods = array_map(function ($item) {
                            return strtoupper(trim(ltrim($item, ',')));
                        }, array_slice($matches, 1));
                        $uses = $ref->getName() . '@' . $method->getName();
                        $inserts[] = [
                            'uses' => $uses,
                            'url' => $this->getUrl($method),
                            'methods' => implode(',', $methods),
                            'is_usable' => 'yes',
                            RouteModel::CREATED_AT => $now,
                            RouteModel::UPDATED_AT => $now,
                        ];
                    }
                    break;
            }
        }
        RouteModel::truncate(); //清空表
        RouteModel::insert($inserts);
    }

    /**
     * 循环处理器
     */
    protected function eachAction() {
        // 循环控制器
        foreach ($this->eachController() as $desc => $ref) {
            foreach ($ref->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                if ($method->isAbstract() || $method->isStatic() || !preg_match('/@methods\(\s*([a-z]+)(\s*,\s*[a-z]+)*\s*\)/i', $method->getDocComment(), $matches)) {
                    continue;
                }
                $methods = array_map(function ($item) {
                    return strtoupper(trim(ltrim($item, ',')));
                }, array_slice($matches, 1));
                yield [$desc, $ref, $method, $methods];
            }
        }
        // 循环路由
        foreach (Route::getRoutes()->getIterator() as $route) {
            $action = $route->getAction();
            if (!$this->hasAuth($action)) {
                continue;
            }
            $uses = array_get($action, 'uses');
            if (is_string($uses) && strpos($uses, $this->namespace) === 0 && strpos($uses, '@') > 0) {
                list($controller, $method) = explode('@', $uses);
                if (method_exists($controller, $method)) {
                    $ref = new ReflectionClass($controller);
                    if ($this->isVainController($ref)) {
                        $desc = $ref->getProperty('description')->getDefaultValue();
                        yield [$desc, $ref, $ref->getMethod($method), $route->methods()];
                    }
                }
            }
        }
    }

    /**
     * 判断是否存在授权中间件
     * @param array $action
     * @return boolean
     */
    protected function hasAuth(array $action) {
        foreach ($action['middleware'] ?? [] as $middleware) {
            if ($middleware == 'auth' || strpos($middleware, 'auth:')) {
                return true;
            }
        }
        return false;
    }

    /**
     * 循环控制器
     */
    protected function eachController() {
        $array = [];
        foreach ($this->forFile($this->path) as $file) {
            $class = $this->namespace . '\\' . str_replace(['.php', '/'], ['', '\\'], $file);
            if (class_exists($class)) {
                $ref = new ReflectionClass($class);
                if ($this->isVainController($ref)) {
                    continue;
                }
                $arr = explode('\\', $ref->getNamespaceName());
                $key = count($arr) * 10;
                if (end($arr) !== preg_replace('/Controller$/', '', $ref->getShortName())) {
                    $key += 1;
                }
                $array[$key . $ref->getName()] = $ref;
            }
        }
        // 排序处理，保证层级写入关系是从上到下
        ksort($array);
        foreach ($array as $ref) {
            yield $ref->getProperty('description')->getDefaultValue() => $ref;
        }
    }

    /**
     * 添加菜单
     * @param ReflectionClass $class
     * @param ReflectionMethod $method
     * @param string $title
     * @param string $desc
     */
    protected function addMenu(ReflectionClass $class, ReflectionMethod $method, string $title, string $desc) {
        $url = $this->getUrl($method);
        $item_id = $this->addItem($class, $method, $title);
        $this->info('添加菜单：' . $url);
        if (!Menu::where('url', $url)->count()) {
            $parent_id = 0;
            $groupName = $this->getLevels($class);
            if (count($groupName) == 1 && $groupName[0] == $desc) {
                $title = lang('power.menu', ['name' => $desc]);
                $menu = Menu::where('name', lang('power.menu', ['name' => $desc]))->first();
                if ($menu) {
                    $menu->update([
                        'name' => $title,
                        'url' => $url,
                        'power_item_id' => $item_id,
                        'comment' => $title,
                    ]);
                } else {
                    // 当前是顶级菜单
                    $parent_id = $this->createMenu($title, $item_id, $url);
                }
            } else {
                // 菜单层级搜索
                for ($key = count($groupName) - 1; $key >= 0; $key--) {
                    $menuID = Menu::where('name', lang('power.menu', ['name' => $groupName[$key]]))->value('id');
                    if ($menuID) {
                        $parent_id = MenuLevel::where('power_menu_group_id', $this->menuGroup['id'])->where('power_menu_id', $menuID)->value('id');
                        break;
                    }
                }
                // 创建顶层
                if (count($groupName) == 1 && !$parent_id) {
                    $parent_id = $this->createMenu(lang('power.menu', ['name' => $groupName[0]]), $item_id, 'javascript:void(0);');
                }
            }
            $this->createMenu($title, $item_id, $url, $parent_id);
        }
    }

    /**
     * 创建菜单
     * @param string $title
     * @param int $item_id
     * @param string $url
     * @param int $parent_id
     * @return int
     */
    protected function createMenu(string $title, int $item_id, string $url, int $parent_id = 0): int {
        $menu = Menu::create([
                    'name' => $title,
                    'url' => $url,
                    'power_item_id' => $item_id,
                    'comment' => $title,
        ]);
        return MenuLevel::create([
                    'power_menu_id' => $menu['id'],
                    'power_menu_group_id' => $this->menuGroup['id'],
                    'parent_id' => $parent_id,
                    'sort' => MenuLevel::where('parent_id', 0)->where('power_menu_group_id', $this->menuGroup['id'])->max('sort') ?: 0,
                ])['id'];
    }

    /**
     * 添加权限项
     * @param ReflectionClass $class
     * @param ReflectionMethod $method
     * @param string $title
     */
    protected function addItem(ReflectionClass $class, ReflectionMethod $method, string $title) {
        $code = $class->getName() . '@' . $method->getName();
        $this->info('添加权限项：' . $code);
        // 已经存在就跳过
        $item = Item::where('code', $code)->first();
        if (empty($item)) {
            $groupName = lang('power.group', ['name' => $this->getLevels($class)[0]]);
            $itemGroup = ItemGroup::where('name', $groupName)->first();
            if (!$itemGroup) {
                $itemGroup = ItemGroup::create([
                            'name' => $groupName,
                            'comment' => $groupName,
                ]);
            }
            $item = Item::create([
                        'name' => $title,
                        'code' => $code,
                        'power_item_group_id' => $itemGroup['id'],
                        'status' => 'enable',
                        'comment' => $title
            ]);
            RoleItem::create([
                'power_role_id' => $this->role['id'],
                'power_item_id' => $item['id'],
            ]);
        }
        return $item['id'];
    }

    /**
     * 获取层级关系名
     * @staticvar array $data
     * @param ReflectionClass $class
     * @return array
     */
    protected function getLevels(ReflectionClass $class): array {
        static $data = [];
        $key = $class->getName();
        if (empty($data[$key])) {
            // 获取当前类的顶层desc
            $baseClass = $this->namespace . '\\';
            $group = [];
            foreach (array_filter(explode('\\', substr($class->getNamespaceName(), strlen($baseClass)))) as $name) {
                $className = $baseClass . $name . 'Controller';
                foreach ([$baseClass . $name . 'Controller', $baseClass . $name . '\\' . $name . 'Controller'] as $className) {
                    if (class_exists($className)) {
                        $ref = new ReflectionClass($className);
                        if ($this->isVainController($ref)) {
                            continue;
                        }
                        $group[] = $ref->getProperty('description')->getDefaultValue();
                    }
                }
                $baseClass .= $name . '\\';
            }
            $group[] = $class->getProperty('description')->getDefaultValue();
            $data[$key] = array_unique($group);
        }
        return $data[$key];
    }

    /**
     * 是否为不可用控制器
     * @param ReflectionClass $ref
     * @return bool
     */
    protected function isVainController(ReflectionClass $ref) {
        return $ref->isAbstract() || !is_subclass_of($ref->getName(), \Laravel\Crbac\Controllers\Controller::class) || !$ref->hasProperty('description');
    }

    /**
     * 生成链接地址
     * @param ReflectionMethod $method
     * @return string
     */
    protected function getUrl(ReflectionMethod $method) {
        $file = str_replace([$this->path, '\\'], ['', '/'], $method->getFileName());
        return implode('/', array_map(function ($item) {
                            return trim(preg_replace_callback('/[A-Z]/', function ($str) {
                                return '-' . strtolower($str[0]);
                            }, $item), '-');
                        }, explode('/', str_ireplace('Controller.php', '', $file) . '/' . $method->getName()))) . $this->option('url-suffix');
    }

    /**
     * 循环指定目录内所有文件
     * @param string $dir
     * @param string $prefix
     */
    protected function forFile(string $dir, string $prefix = '') {
        foreach (scandir($dir) as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            $path = "$dir/$file";
            if (is_file($path)) {
                yield $prefix . $file;
            } else {
                yield from $this->forFile($path, $prefix . $file . '/');
            }
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments() {
        return [
            ['path', InputArgument::OPTIONAL, '处理controller根目录', realpath(app_path('Http/Controllers'))],
            ['namespace', InputArgument::OPTIONAL, '处理controller根命令空间', 'App\\Http\\Controllers']
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions() {
        return [
            ['--url-suffix', '-U', InputOption::VALUE_OPTIONAL, 'url地址后缀，mvc结构专用', '.html'],
            ['--menu-group', '-G', InputOption::VALUE_OPTIONAL, '指定要添加的菜单组', '标准菜单'],
            ['--role', '-R', InputOption::VALUE_OPTIONAL, '指定角色名', '超级管理员'],
            ['--rework', null, InputOption::VALUE_NONE, '删除已经写入根命令空间所有权限及菜单数据并重新写入'],
        ];
    }
}
