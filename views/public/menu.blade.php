<!--导航栏-->
<ul class="navbar clear">
    <li>后台管理系统</li>
    @if(isset($menus[0]))
    @foreach($menus[0] as $menu)
    <li class="menu-item <?php if (in_array($menu->getKey(), $crumbs_ids)) { ?> current<?php } ?>">
        <a title="{{$menu['comment']}}" href="{{$menu['url']}}">{{$menu['name']}}</a>
        @include('public.sub_menu')
    </li>
    @endforeach
    @endif
    <li>
        <a href="/"><span class="text">欢迎您 {{Auth::user()->realname}}</span></a>
        <a href="{{crbac_route('power.admin.password')}}"><i class="icon-key"></i> 修改密码</a>
        <a href="{{route('logout')}}"><i class="icon-key"></i> 退出</a>
    </li>
</ul>