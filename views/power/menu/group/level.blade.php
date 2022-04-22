@extends('public.edit')
@section('body')
<div class="alert alert-info py-1">
    菜单组：<span class="fs-4">{{$item->name}}</span>
</div>
<div class="alert alert-danger">选中菜单后，按键操作：上向移动（Down 或 PgDn）、下向移动（Up 或 PgDn）、删除当前菜单（Delete）、撤销上次操作（Ctrl + Z）</div>
<div class="container-fluid">
    <nav class="navbar navbar-expand-lg navbar-light bg-light align-items-stretch">
        <ul class="nav flex-column">
            @include('power.menu.group.menus')
            <li class="nav-item dropdown dropend bg-warning my-1">
                <a class="nav-link dropdown-toggle" href="javascript:void(0);" onclick="addMenu('{{$level}}', this, '0');"  data-ids="{{implode(',',$lists->has(0)?array_pluck($lists[0],$lists[0][0]->getKeyName()):[])}}">添加{{$level}}级菜单</a>
            </li>
        </ul>
    </nav>
</div>
<script type="text/javascript">
    $('body').on('keydown', function(){
        
    });
    var setMenu;
    //添加菜单
    function addMenu(level, elem, parent_id) {
        level++;
        var _$ = $(elem),
                ids = getData(elem),
                myWindow = open_window('{{crbac_route("power.menu.select", ["callback"])}}');
        setMenu = function (id, name) {
            var exist = false;
            ids = getData(elem);
            $.each(ids, function (k, v) {
                return !(exist = v == id);
            });
            //判断是否存在
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
            $(elem).parent().before(html);
        };
    }
    //移除菜单
    function removeMenu(elem, id) {
        var div = $(elem).parents('div.level-lists-item:first'),
                button = div.next().find(':button'),
                ids = getData(button),
                newIds = [];
        div.remove();
        $.each(ids, function (k, v) {
            if (v != id) {
                newIds.push(v);
            }
        });
        button.data('ids', newIds);
    }
    //菜单上移
    function menuUp(elem) {
        var par = $(elem).parents('div.level-lists-item:first'),
                prev = par.prev('div.level-lists-item');
        if (prev.size()) {
            prev.before(par);
        }
    }
    //菜单下移
    function menuDown(elem) {
        var par = $(elem).parents('div.level-lists-item:first'),
                next = par.next('div.level-lists-item');
        if (next.size()) {
            next.after(par);
        }
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
    <div class="level-lists-item">
        <div class="level-lists-menu">
            <div class="level-lists-govern">
                <a href="javascript:void(0);" title="菜单上移" onclick="menuUp(this)">&and;</a>
                <a href="javascript:void(0);" title="菜单下移" onclick="menuDown(this)">&or;</a>
                <a href="javascript:void(0);" onclick="removeMenu(this, '$menu_id')" title="删除这个菜单">&Chi;</a>
            </div>
            <label class="label">$name</label>
            <input type="hidden" name="level[$level][$parent_id][]" value="$menu_id">
        </div>
        <div class="level-lists-child">
            <div class="button">
                <input type="button" class=" btn-primary" onclick="addMenu('$levelName', this, '$menu_id')" value="添加$levelName级菜单"/>
            </div>
        </div>
    </div>
</script>
@stop
