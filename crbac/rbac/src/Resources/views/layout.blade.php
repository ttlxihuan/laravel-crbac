<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="zh-CN">
    <head>
        <title>
            @section('title') 后台管理系统 @show
        </title>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <script type="text/javascript" src="/power/static/js/jquery.min.js"></script>
        <script type="text/javascript" src="/power/static/js/jquery.validate.js"></script>
        <link rel="stylesheet" href="/power/static/css/base.css" />
        @section('css') @show
    </head>
    <body>
        @if(Auth::check() && empty($not_menu))
        @include('public.menu')
        @endif
        <!--主内容-->
        @yield('main')
        <script src="/power/static/js/public.js"></script>
    </body>
</html>