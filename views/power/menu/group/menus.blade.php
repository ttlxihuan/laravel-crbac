@foreach($lists[$parent_id]??[] as $menu)
<li class="tree-item" data-id="{{$menu->getKey()}}">
    <div class="tree-node">
        <span class="tree-collapse-btn" onclick="toggleCollapse(this)" title="折叠/展开">
            <i class="fas fa-chevron-down tree-collapse-icon"></i>
        </span>
        <span class="tree-toggle{{isset($lists[$menu->level_id])?' has-children':''}}" title="{{$menu->comment}}">
            <i class="fas {{isset($lists[$menu->level_id])?'fa-folder-open':'fa-file-alt'}} me-1"></i>{{$menu->name}}
        </span>
        <input type="hidden" name="level[{{$level}}][{{array_get($level_lists, $menu->parent_id, $menu->getKey())}}][]" value="{{$menu->getKey()}}"/>
        <span class="tree-actions">
            <a href="javascript:void(0);" class="btn btn-sm btn-outline-secondary" title="上移" onclick="handleMove(this,'up')"><i class="fas fa-arrow-up"></i></a>
            <a href="javascript:void(0);" class="btn btn-sm btn-outline-secondary" title="下移" onclick="handleMove(this,'down')"><i class="fas fa-arrow-down"></i></a>
            <a href="javascript:void(0);" class="btn btn-sm btn-outline-danger" title="删除" onclick="handleDelete(this)"><i class="fas fa-trash"></i></a>
        </span>
    </div>
    <ul class="tree-children">
        @include('power.menu.group.menus', ['level'=>$level+1, 'parent_id'=>$menu->level_id])
        <li class="tree-item tree-add">
            <a href="javascript:void(0);" class="btn btn-sm btn-outline-success" onclick="return addMenu('{{$level+1}}', this,'{{$menu->getKey()}}');" data-ids="{{implode(',',$lists->has($menu->level_id)?array_pluck($lists[$menu->level_id],$menu->getKeyName()):[])}}">
                <i class="fas fa-plus"></i> 添加{{$level+1}}级菜单
            </a>
        </li>
    </ul>
</li>
@endforeach