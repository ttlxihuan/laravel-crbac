@extends('layout')
@if(isControllerPower('add') && !isset($not_add))
@section('nimble')<a href="{{$add_url ?? crbac_route('.add')}}">新增{{$description}}</a>@stop
@endif
<style>
    .order-by{
        position: relative;
        cursor: pointer;
        box-sizing: border-box;
        background-clip: padding-box;
    }
    .order-by:before,.order-by:after{
        display:inline-block;
        position: absolute;
        right: 0px;
        width: 20px;
        height: 20px;
        color: #9A9A9A;
    }
    .order-by:before{
        content: '∧';
        top:0px;
    }
    .order-by:after{
        content: '∨';
        bottom: 0px;
    }
    .order-desc:after{
        color: #009900;
    }
    .order-asc:before{
        color: #009900;
    }
</style>
@section('main')
@include('public.crumbs')
<div class="container-fluid">
    @section('search')@show
    @yield('lists')
    {!!$lists->render()!!}
    @if(isset($uses_confirm) || isControllerPower('delete'))
    <script type="text/javascript">
        $('a.confirm').click(function () {
            var title = $(this).attr('data-confirm');
            return confirm(typeof title === 'string' ? title : '确定删除这条记录吗？');
        });
    </script>
    @endif
</div>
@stop