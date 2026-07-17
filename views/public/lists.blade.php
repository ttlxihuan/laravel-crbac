@extends('layout')
@if(isControllerPower('add') && !isset($not_add))
@section('nimble')<a href="{{$add_url ?? crbac_route('.add')}}" class="btn btn-default btn-sm"><i class="fas fa-plus"></i> 新增{{$description}}</a>@stop
@endif
@section('main')
<div class="card">
    <div class="card-body">
        @section('search')@show
        @yield('lists')
    </div>
    <div class="card-footer">
        {!!$lists->render()!!}
    </div>
</div>
@if(isset($uses_confirm) || isControllerPower('delete'))
<script type="text/javascript">
    $('a.confirm').click(function () {
        var title = $(this).attr('data-confirm');
        return confirm(typeof title === 'string' ? title : '确定删除这条记录吗？');
    });
</script>
@endif
@stop
