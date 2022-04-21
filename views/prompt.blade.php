@extends('layout')
@section('main')
@include('public.crumbs')
<div class="text-center">
    <div class="alert alert-danger" role="alert">
        {{$message['title']}}：
        @if(is_array($message['info']))
        @foreach($message['info'] as $info)
        <p>{{$info}}</p>
        @endforeach
        @else
        {{$message['info']}}
        @endif
    </div>
    @if(isset($redirect)&&$redirect)
    <div class="alert alert-light" role="alert">
      系统将在 <b id="wait" class="text-alert-danger">{{$timeout}}</b> 秒后自动跳转,如果不想等待,直接点击 <a id="href" href="{{$redirect}}">这里</a> 跳转
    </div>
    @endif
</div>
@if(isset($redirect)&&$redirect)
<script type="text/javascript">
    (function () {
        var wait = document.getElementById('wait'),
                href = document.getElementById('href').href;
        var interval = setInterval(function () {
            var time = --wait.innerHTML;
            if (time == 0) {
                clearInterval(interval);
                location.href = href;
            }
        }, 1000);
    })();
</script>
@endif
@stop
