@extends('layout')
@section('main')
<div class="login-page-wrapper">
    <div class="login-decor login-decor-1"></div>
    <div class="login-decor login-decor-2"></div>
    <div class="login-decor login-decor-3"></div>
    <div class="login-box">
        <div class="login-logo">
            <div class="login-logo-icon"><i class="fas fa-shield-alt"></i></div>
            <div class="login-logo-text"><b>{{config('app.name')}}</b>管理后台</div>
        </div>
        <div class="card login-card">
            <div class="login-card-body">
                <p class="login-box-msg">请登录您的账号</p>
                <form method="post">
                    @if(function_exists('csrf_field')){{csrf_field()}}@endif
                    <div class="input-group mb-3 login-input">
                        <span class="input-group-text login-input-icon"><i class="fas fa-user"></i></span>
                        <input type="text" class="form-control" name="username" placeholder="请输入账号" value="" required="true" minlength="3" maxlength="30"/>
                    </div>
                    <div class="input-group mb-3 login-input">
                        <span class="input-group-text login-input-icon"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" name="password" placeholder="请输入密码" value="" required="true" minlength="3" maxlength="30"/>
                    </div>
                    <div class="row align-items-center mt-4">
                        <div class="col-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember-login" value="1"/>
                                <label class="form-check-label" for="remember-login">记住登录</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <button type="button" class="btn btn-primary w-100 login-btn ajax-submit-data">登录</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="login-footer">Powered by {{config('app.name')}} &copy; {{date('Y')}}</div>
    </div>
</div>
@stop
