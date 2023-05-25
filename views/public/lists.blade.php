@extends('layout')
@if(isControllerPower('add') && !isset($not_add))
@section('nimble')<a href="{{$add_url ?? crbac_route('.add')}}">新增{{$description}}</a>@stop
@endif
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