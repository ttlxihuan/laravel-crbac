@extends('layout')
@section('nimble')<a href="javascript:history.back();">返回</a>@stop
@section('main')
@include('public.crumbs')
<style>form .row{margin-left: 0;margin-right: 0;}</style>
<form method="post" enctype="multipart/form-data" class="needs-validation">
    @yield('body')
    @if(function_exists('csrf_field')){{csrf_field()}}@endif
    <input type="hidden" name="_referrer" value="{{URL::previous()}}">
    <div class="text-center my-3">
        @section('button')<button type="button" class="btn btn-primary ajax-submit-data">{{empty($item)?'创建':'保存'}}</button>@show
    </div>
</form>
<script type="text/javascript">
    function open_window(url) {
        return window.open(url, '_blank', 'toolbar=no,location=yes,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=700,height=650');
    }
</script>
@stop