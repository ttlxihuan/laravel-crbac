@if(isset($menus[$menu->level_id]))
@php $currentDepth = isset($depth) ? $depth : 2; @endphp
<ul class="nav nav-treeview">
    @foreach($menus[$menu->level_id] as $menu)
    @php $hasChild = isset($menus[$menu->level_id]); @endphp
    @php $isActive = in_array($menu->getKey(), $crumbs_ids); @endphp
    <li class="nav-item menu-level-{{$currentDepth}}{{$hasChild && $isActive ? ' menu-open' : ''}}">
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
        @include('public.sub_menu', ['depth' => $currentDepth + 1])
        @endif
    </li>
    @endforeach
</ul>
@endif
