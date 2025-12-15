<!--菜单栏-->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">{{config('app.name')}}管理后台</a>
        <button class="navbar-toggler" type="button">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav me-auto my-2 my-lg-0 navbar-nav-scroll" style="--bs-scroll-height: 100px;">
                @if(isset($menus[0]))
                @foreach($menus[0] as $menu)
                <li class="nav-item{{isset($menus[$menu->level_id])?' dropdown':''}}">
                    <a class="nav-link{{isset($menus[$menu->level_id])?' dropdown-toggle':''}}{{in_array($menu->getKey(), $crumbs_ids)? ' active':''}}" title="{{$menu['comment']}}" href="{{$menu['url']}}">{{$menu['name']}}</a>
                    @include('public.sub_menu')
                </li>
                @endforeach
                @endif
            </ul>
            @if(Auth::check())
            <ul class="navbar-nav navbar-expand">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="javascript:void(0);" >{{Auth::user()->realname}}</a>
                    <ul class="dropdown-menu dropdown-menu-end" data-bs-popper="static">
                        <li class="dropdown-item"><a class="nav-link" href="{{route('mvc-crbac', ['power','admin','password'])}}"><i class="icon-key oi"></i> 修改密码</a></li>
                        <li class="dropdown-item"><hr class="dropdown-divider"/></li>
                        <li class="dropdown-item"><a class="nav-link" href="{{route('logout')}}"><i class="icon-bell oi"></i> 退出</a></li>
                    </ul>
                </li>
            </ul>
            @endif
        </div>
    </div>
</nav>
<script type="text/javascript">
    $(function () {
        $('a.dropdown-toggle').on('click', function () {
            var $this = $(this), curr = $this.nextAll('.dropdown-menu').toggleClass('show');
            $('.dropdown-menu').not(curr).not($this.parents('.dropdown-menu')).removeClass('show');
            return false;
        });
        $('button.navbar-toggler').on('click', function () {
            $(this).nextAll('.collapse').toggleClass('show');
        });
        $(document).on('click', function () {
            $('.dropdown-menu').removeClass('show');
        });
    });
</script>