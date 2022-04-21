@if(isset($menus[$menu->level_id]))
<ul class="dropdown-menu dropdown-menu-end"@if(isset($is_child)) data-bs-popper="static"@endif>
    @foreach($menus[$menu->level_id] as $menu)
    <li class="nav-item{{isset($menus[$menu->level_id])?' dropdown dropend':''}}">
        <a class="nav-link{{isset($menus[$menu->level_id])?' dropdown-toggle':''}}{{in_array($menu->getKey(), $crumbs_ids)?' active':''}}" title="{{$menu['comment']}}" href="{{$menu['url']}}">{{$menu['name']}}</a>
        @include('public.sub_menu', ['is_child'=>true])
    </li>
    @endforeach
</ul>
@endif