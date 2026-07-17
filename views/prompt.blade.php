@extends('layout')
@section('main')
@php $alertClass = ['success' => 'success', 'error' => 'danger', 'warn' => 'warning', 'info' => 'info'][$status ?? 'error'] ?? 'danger'; @endphp
<div class="card">
    <div class="card-header">
        <h3 class="card-title">系统提示</h3>
    </div>
    <div class="card-body">
        <div class="alert alert-{{$alertClass}}" role="alert">
            <h5><i class="icon fas fa-{{$alertClass == 'success' ? 'check-circle' : ($alertClass == 'danger' ? 'exclamation-triangle' : 'info-circle')}}"></i> {{$message['title']}}</h5>
            @if(is_array($message['info']))
            @foreach($message['info'] as $info)
            <p>{{$info}}</p>
            @endforeach
            @else
            <p>{{$message['info']}}</p>
            @endif
        </div>
        @if(isset($redirect)&&$redirect)
        <div class="alert alert-light" role="alert">
            系统将在 <b id="wait" class="text-danger">{{$timeout}}</b> 秒后自动跳转,如果不想等待,直接点击 <a id="href" href="{{$redirect}}">这里</a> 跳转
        </div>
        @endif
    </div>
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
