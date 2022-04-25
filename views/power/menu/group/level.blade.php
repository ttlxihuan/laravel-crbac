@extends('public.edit')
@section('body')
<div class="alert alert-info py-1">
    菜单组：<span class="fs-4">{{$item->name}}</span>
</div>
<div class="alert alert-danger">选中菜单后编辑按键操作：向上移动（&uarr;）、向下移动（&darr;）、删除（Delete）</div>
<div class="container-fluid">
    <nav class="navbar navbar-expand-lg navbar-light bg-light align-items-stretch">
        <ul class="nav flex-column dropdown-menu">
            @include('power.menu.group.menus')
            <li class="nav-item dropdown bg-warning my-1">
                <span class="nav-link" onclick="addMenu('{{$level}}', this, '0');"  data-ids="{{implode(',',$lists->has(0)?array_pluck($lists[0],$lists[0][0]->getKeyName()):[])}}">添加{{$level}}级菜单</span>
            </li>
        </ul>
    </nav>
</div>
<script type="text/javascript">
    $(function () {
        $('body').on('click', 'span.dropdown-toggle,span.nav-link', function () {
            var $this = $(this);
            if($this.is('span.dropdown-toggle')){
                var $this = $(this), curr = $this.nextAll('.dropdown-menu').toggleClass('show');
                $('.dropdown-menu').not(curr).not($this.parents('.dropdown-menu')).removeClass('show');
                $this.toggleClass('border-info');
                $('span.dropdown-toggle.border').not($this).removeClass('border-info');
            }
            return false;
        });
        $(document).on('click', function () {
            $('span.dropdown-toggle.border').removeClass('border-info');
        });
        var handle = {
            moveUp: function (menu) {
                var prev = menu.prev();
                if(prev.size() > 0){
                    prev.before(menu);
                }else{
                    $.popup.alert('已经是最上无法再向上移动', 'error', 1);
                }
            },
            moveDown: function (menu) {
                var _next = menu.next();
                if(_next.size() > 0 && !_next.find('span:first').is('span[data-ids]')){
                    _next.after(menu);
                }else{
                    $.popup.alert('已经是最下无法再向上移动', 'error', 1);
                }
            },
            _delete: function (menu) {
                var ids = getData(_parent.nextAll('li').find('span[data-ids]')),
                        id= menu.next('input:hidden').val(),
                        newIds = [];
                menu.remove();
                $.each(ids, function (k, v) {
                    if (v != id) {
                        newIds.push(v);
                    }
                });
                button.data('ids', newIds);
            }
        }
        $('body').on('keydown', function (event) {
            var method = null;
            if (!event.ctrlKey) {
                switch (event.keyCode) {
                    case 38: // 向上
                        method = 'moveUp';
                        break;
                    case 40: // 向下
                        method = 'moveDown';
                        break;
                    case 46: // 删除
                        method = '_delete';
                        break;
                    default:
                        return;
                }
                var menu = $('span.dropdown-toggle.border-info:first').parent();
                if(menu.size() > 0){
                    handle[method](menu);
                } else {
                    $.popup.alert('没有选中菜单！', 'error', 1);
                }
            }
        });
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
    <li class="nav-item dropdown dropend my-1">
        <span class="nav-link dropdown-toggle border border-3 rounded" title="$name">$name</span>
        <input type="hidden" name="level[$level][$parent_id][]" value="$menu_id"/>
        <ul class="dropdown-menu dropdown-menu-end" data-bs-popper="static">
            <li class="nav-item dropdown bg-warning my-1">
                <span class="nav-link" onclick="return addMenu('$levelName', this, '$menu_id');" data-ids="">添加$levelName级菜单</span>
            </li>
        </ul>
    </li>
</script>
@stop
