@if(isset($lists[$parent_id]))
@foreach($lists[$parent_id] as $menu)
<div class="level-lists-item">
    <div class="level-lists-menu">
        <div class="level-lists-govern">
            <a href="javascript:void(0);" title="菜单上移" onclick="menuUp(this)">&and;</a>
            <a href="javascript:void(0);" title="菜单下移" onclick="menuDown(this)">&or;</a>
            <a href="javascript:void(0);" onclick="removeMenu(this, '{{$menu->getKey()}}')" title="删除这个菜单">&Chi;</a>
        </div>
        <label class="label">{{$menu->name}}</label>
        <input type="hidden" name="level[{{$level}}][{{array_get($level_lists, $menu->parent_id, $menu->getKey())}}][]" value="{{$menu->getKey()}}">
    </div>
    <div class="level-lists-child">
        <?php $parent_id = $menu->level_id;$level++; ?>
        @include('power.menu.group.menus')
        <?php $level--;$parent_id = $menu->parent_id; ?>
        <div class="button">
            <input type="button" class=" btn-primary" onclick="addMenu('{{$level+1}}', this,'{{$menu->getKey()}}')" data-ids="{{implode(',',$lists->has($menu->level_id)?array_pluck($lists[$menu->level_id],$menu->getKeyName()):[])}}" value="添加{{$level+1}}级菜单"/>
        </div>
    </div>
</div>
@endforeach
@endif