<!--侧边栏菜单-->
<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        @if(isset($menus[0]))
        @foreach($menus[0] as $menu)
        @php $hasChild = isset($menus[$menu->level_id]); @endphp
        @php $isActive = in_array($menu->getKey(), $crumbs_ids); @endphp
        <li class="nav-item menu-level-1{{$hasChild && $isActive ? ' menu-open' : ''}}">
            <a href="{{$menu['url']}}" class="nav-link{{$isActive ? ' active' : ''}}" title="{{$menu['comment']}}">
                <i class="nav-icon fas {{ $menu->icon ?? 'fa-circle' }}"></i>
                <p>
                    {{$menu['name']}}
                    @if($hasChild)
                    <i class="right fas fa-angle-left"></i>
                    @endif
                </p>
            </a>
            @if($hasChild)
            @include('public.sub_menu', ['depth' => 2])
            @endif
        </li>
        @endforeach
        @endif
    </ul>
</nav>
