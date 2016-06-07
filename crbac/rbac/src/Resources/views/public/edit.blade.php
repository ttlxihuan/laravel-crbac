@extends('layout')
@section('nimble')<a href="javascript:history.back();">返回</a>@stop
@section('main')
@include('public.crumbs')
<h5 class="widget-title">{{$title}}</h5>
<form method="post" enctype="multipart/form-data">
    @yield('body')
    @if(function_exists('csrf_field')){{csrf_field()}}@endif
    <input type="hidden" name="_referrer" value="{{URL::previous()}}">
</form>
<script type="text/javascript">
    function open_window(url) {
        return window.open(url, '_blank', 'toolbar=no,location=yes,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=700,height=650');
    }
</script>
@stop