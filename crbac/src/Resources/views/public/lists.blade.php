@extends('layout')
@if(isControllerPower('add') && !isset($not_add))
@section('nimble')<a href="add.html">新增{{$description}}</a>@stop
@endif
@section('main')
@include('public.crumbs')
<h5 class="widget-title">{{$description}}管理</h5>
@section('search')@show
@yield('lists')
<?php echo $lists->render(); ?>
@if(isset($uses_confirm) || isControllerPower('delete'))
<script type="text/javascript">
    $('a.confirm').click(function () {
        var title = $(this).attr('data-confirm');
        return confirm(typeof title === 'string' ? title : '确定删除这条记录吗？');
    });
</script>
@endif
@stop