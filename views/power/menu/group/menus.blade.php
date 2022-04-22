@foreach($lists[$parent_id]??[] as $menu)
<li class="nav-item dropdown dropend bg-info my-1">
    <a class="nav-link dropdown-toggle" title="{{$menu->comment}}" href="javascript:void(0);">{{$menu->name}}</a>
    <input type="hidden" name="level[{{$level}}][{{array_get($level_lists, $menu->parent_id, $menu->getKey())}}][]" value="{{$menu->getKey()}}">
    <ul class="dropdown-menu dropdown-menu-end" data-bs-popper="static">
        @include('power.menu.group.menus', ['level'=>$level+1, 'parent_id'=>$menu->level_id])
        <li class="nav-item dropdown dropend bg-warning my-1">
            <a class="nav-link dropdown-toggle" href="javascript:void(0);" onclick="addMenu('{{$level+1}}', this,'{{$menu->getKey()}}');"  data-ids="{{implode(',',$lists->has($menu->level_id)?array_pluck($lists[$menu->level_id],$menu->getKeyName()):[])}}">添加{{$level+1}}级菜单</a>
        </li>
    </ul>
</li>
@endforeach