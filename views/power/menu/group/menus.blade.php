@foreach($lists[$parent_id]??[] as $menu)
<li class="nav-item dropdown dropend my-1">
    <span class="nav-link dropdown-toggle border border-3 rounded" title="{{$menu->comment}}">{{$menu->name}}</span>
    <input type="hidden" name="level[{{$level}}][{{array_get($level_lists, $menu->parent_id, $menu->getKey())}}][]" value="{{$menu->getKey()}}"/>
    <ul class="dropdown-menu dropdown-menu-end" data-bs-popper="static">
        @include('power.menu.group.menus', ['level'=>$level+1, 'parent_id'=>$menu->level_id])
        <li class="nav-item dropdown bg-warning my-1">
            <span class="nav-link" onclick="return addMenu('{{$level+1}}', this,'{{$menu->getKey()}}');"  data-ids="{{implode(',',$lists->has($menu->level_id)?array_pluck($lists[$menu->level_id],$menu->getKeyName()):[])}}">添加{{$level+1}}级菜单</span>
        </li>
    </ul>
</li>
@endforeach