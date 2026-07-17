@extends('public.edit')
@section('body')
<style>
    .menu-tree { padding: 0; }
    .menu-tree, .tree-children { list-style: none; margin: 0; padding-left: 0; }
    .tree-children { padding-left: 24px; border-left: 1px dashed #d1d5db; margin-left: 12px; transition: max-height .25s ease; }
    .tree-item { margin: 4px 0; }
    .tree-item.collapsed > .tree-children { display: none; }
    .tree-item.collapsed > .tree-node .tree-collapse-icon { transform: rotate(-90deg); }
    .tree-node { display: flex; align-items: center; padding: 6px 10px; border-radius: 6px; background: #fff; border: 1px solid #e5e7eb; transition: all .15s; }
    .tree-node:hover { border-color: var(--lte-primary); background: #f0fdf4; }
    .tree-toggle { flex: 1; cursor: pointer; font-size: .9rem; color: #374151; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .tree-toggle.has-children { font-weight: 500; }
    .tree-collapse-btn { display: inline-flex; align-items: center; justify-content: center; width: 20px; height: 20px; cursor: pointer; border-radius: 3px; margin-right: 4px; flex-shrink: 0; }
    .tree-collapse-btn:hover { background: #e5e7eb; }
    .tree-collapse-icon { display: inline-block; transition: transform .2s; font-size: .65rem; color: #9ca3af; }
    .tree-actions { display: flex; gap: 4px; opacity: 0; transition: opacity .15s; }
    .tree-node:hover .tree-actions { opacity: 1; }
    .tree-add { margin-top: 4px; }
    .tree-add .btn { font-size: .75rem; padding: 2px 8px; }
</style>
<div class="card">
    <div class="card-header d-flex align-items-center">
        <i class="fas fa-sitemap me-2"></i>
        <strong>{{$item->name}}</strong>
        <span class="text-muted ms-2">— 菜单层级编辑</span>
        <span class="ms-auto">
            <a href="javascript:void(0);" class="btn btn-sm btn-outline-secondary" onclick="collapseAll()" title="全部折叠"><i class="fas fa-compress"></i> 折叠</a>
            <a href="javascript:void(0);" class="btn btn-sm btn-outline-secondary" onclick="expandAll()" title="全部展开"><i class="fas fa-expand"></i> 展开</a>
        </span>
    </div>
    <div class="card-body">
        <ul class="menu-tree">
            @include('power.menu.group.menus')
            <li class="tree-item tree-add">
                <a href="javascript:void(0);" class="btn btn-sm btn-outline-success" onclick="addMenu('{{$level}}', this, '0');" data-ids="{{implode(',',$lists->has(0)?array_pluck($lists[0],$lists[0][0]->getKeyName()):[])}}">
                    <i class="fas fa-plus"></i> 添加{{$level}}级菜单
                </a>
            </li>
        </ul>
    </div>
</div>
<script type="text/javascript">
    // 折叠/展开
    function toggleCollapse(el) {
        $(el).closest('.tree-item').toggleClass('collapsed');
    }
    function collapseAll() {
        $('.tree-item:has(.tree-children .tree-item:not(.tree-add))').addClass('collapsed');
    }
    function expandAll() {
        $('.tree-item.collapsed').removeClass('collapsed');
    }
    // 上移/下移
    function handleMove(btn, direction) {
        var $item = $(btn).closest('.tree-item');
        if (direction === 'up') {
            var $prev = $item.prev('.tree-item:not(.tree-add)');
            if ($prev.length) $prev.before($item);
            else $.popup.alert('已经是第一个，无法上移', 'warn', 1);
        } else {
            var $next = $item.next('.tree-item:not(.tree-add)');
            if ($next.length) $next.after($item);
            else $.popup.alert('已经是最后一个，无法下移', 'warn', 1);
        }
    }
    // 删除
    function handleDelete(btn) {
        var $item = $(btn).closest('.tree-item');
        var $addBtn = $item.siblings('.tree-add').find('[data-ids]');
        if ($addBtn.length) {
            var ids = getData($addBtn[0]);
            var id = $item.data('id').toString();
            var idx = ids.indexOf(id);
            if (idx > -1) ids.splice(idx, 1);
        }
        $item.remove();
    }
    var setMenu;
    // 添加菜单
    function addMenu(level, elem, parent_id) {
        level++;
        var ids = getData(elem),
            myWindow = open_window('{{crbac_route("power.menu.select", ["callback"])}}');
        setMenu = function (id, name) {
            var exist = false;
            ids = getData(elem);
            $.each(ids, function (k, v) {
                return !(exist = v == id);
            });
            if (exist) {
                return myWindow.$.popup.alert('菜单已经存在！');
            }
            ids.push(id);
            var html = $('#add-menu-html').html();
            html = html.replace(/\$levelName/g, level);
            html = html.replace(/\$level/g, level - 1);
            html = html.replace(/\$menu_id/g, id);
            html = html.replace(/\$name/g, name);
            html = html.replace(/\$parent_id/g, parent_id > 0 ? parent_id : id);
            $(elem).closest('.tree-add').before(html);
        };
        return false;
    }
    function getData(elem) {
        var _$ = $(elem),
            ids = _$.data('ids');
        if (ids === undefined) {
            ids = [];
        } else if (typeof ids !== 'object') {
            ids = ids.toString().split(/\D+/g);
        }
        return _$.data('ids', ids), ids;
    }
    function power_menu_select_callback(id, name, win) {
        setMenu(id, name);
    }
</script>
<script type="text/html" id="add-menu-html">
    <li class="tree-item" data-id="$menu_id">
        <div class="tree-node">
            <span class="tree-collapse-btn" onclick="toggleCollapse(this)" title="折叠/展开">
                <i class="fas fa-chevron-down tree-collapse-icon"></i>
            </span>
            <span class="tree-toggle" title="$name">
                <i class="fas fa-file-alt me-1"></i>$name
            </span>
            <input type="hidden" name="level[$level][$parent_id][]" value="$menu_id"/>
            <span class="tree-actions" style="opacity:1;">
                <a href="javascript:void(0);" class="btn btn-sm btn-outline-secondary" title="上移" onclick="handleMove(this,'up')"><i class="fas fa-arrow-up"></i></a>
                <a href="javascript:void(0);" class="btn btn-sm btn-outline-secondary" title="下移" onclick="handleMove(this,'down')"><i class="fas fa-arrow-down"></i></a>
                <a href="javascript:void(0);" class="btn btn-sm btn-outline-danger" title="删除" onclick="handleDelete(this)"><i class="fas fa-trash"></i></a>
            </span>
        </div>
        <ul class="tree-children">
            <li class="tree-item tree-add">
                <a href="javascript:void(0);" class="btn btn-sm btn-outline-success" onclick="return addMenu('$levelName', this, '$menu_id');" data-ids="">
                    <i class="fas fa-plus"></i> 添加$levelName级菜单
                </a>
            </li>
        </ul>
    </li>
</script>
@stop
