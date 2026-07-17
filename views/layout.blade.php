<!DOCTYPE html>
<html lang="zh-CN">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>
            @section('title') {{config('app.name')}}管理后台 @show
        </title>
        <link rel="stylesheet" href="/crbac/static/css/bootstrap.min.css">
        <link rel="stylesheet" href="/crbac/static/css/fontawesome.min.css">
        <link rel="stylesheet" href="/crbac/static/css/adminlte.min.css">
        <script src="/crbac/static/js/jquery.min.js"></script>
        <link rel="stylesheet" href="/crbac/static/css/custom.min.css">
        @if(!Auth::check())<style>.login-page{background:#1a1a2e !important;}.login-page .login-box,.login-page .login-page-wrapper{opacity:0;animation:loginCardIn .6s .1s cubic-bezier(.16,1,.3,1) forwards}</style>@endif
        @section('css') @show
    </head>
    <body class="hold-transition @if(Auth::check() && empty($not_menu))sidebar-mini @elseif(!Auth::check())login-page @endif">
        @if(Auth::check() && empty($not_menu))
        <div class="wrapper">
            <!-- 顶部导航栏 -->
            <nav class="main-header navbar navbar-expand navbar-white navbar-light">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                            <i class="fas fa-bars"></i>
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link" data-bs-toggle="dropdown" href="#">
                            <i class="fas fa-user-circle"></i>
                            <span>{{Auth::user()->realname}}</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="{{route('mvc-crbac', ['power','admin','password'])}}">
                                <i class="fas fa-key me-2"></i> 修改密码
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{route('logout')}}">
                                <i class="fas fa-sign-out-alt me-2"></i> 退出
                            </a>
                        </div>
                    </li>
                </ul>
            </nav>
            <!-- 侧边栏 -->
            <aside class="main-sidebar sidebar-dark-primary">
                <a href="#" class="brand-link">
                    <i class="fas fa-shield-alt ms-3 me-2"></i>
                    <span class="brand-text font-weight-light">{{config('app.name')}}管理后台</span>
                </a>
                <div class="sidebar">
                    @include('public.menu')
                </div>
            </aside>
            <!-- 内容区 -->
            <div class="content-wrapper">
                @include('public.crumbs')
                <section class="content">
                    <div class="container-fluid">
                        @yield('main')
                    </div>
                </section>
            </div>
            <!-- 底部 -->
            <footer class="main-footer">
                <strong>Copyright &copy; {{date('Y')}} {{config('app.name')}}.</strong>
                All rights reserved.
            </footer>
        </div>
        @else
        @yield('main')
        @endif
        <script src="/crbac/static/js/adminlte.min.js"></script>
        <script src="/crbac/static/js/jquery.validate.js"></script>
        <script src="/crbac/static/js/public.js"></script>
    </body>
</html>
