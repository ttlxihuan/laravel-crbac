@if(isset($menus[$menu->getKey()]))
<ul>
    @foreach($menus[$menu->getKey()] as $menu)
    <li<?php if (in_array($menu->getKey(), $crumbs_ids)) { ?> class="current"<?php } ?>>
        <a title="{{$menu['comment']}}" href="{{$menu['url']}}"<?php if (isset($menus[$menu->getKey()])) { ?> class="subordinate"<?php } ?>>{{$menu['name']}}</a>
        @include('public.sub_menu')
    </li>
    @endforeach
</ul>
@endif