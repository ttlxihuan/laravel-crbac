@extends('layout')
@section('main')
<form>
    <div class="card modal-dialog modal-content">
        <h4 class="card-header text-center py-3">{{config('app.name')}}管理后台登录</h4>
        <div class="card-body">
            <div class="row my-3">
                <label class="col-sm-3 col-form-label text-end"><b class="text-danger">*</b> 账号</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" name="username" placeholder="账号名" value="" required="true" minlength="3" maxlength="30"/>
                </div>
            </div>
            <div class="row my-3">
                <label class="col-sm-3 col-form-label text-end"><b class="text-danger">*</b> 密码</label>
                <div class="col-sm-8">
                    <input class="form-control" type="password" name="password" placeholder="密码" value="" required="true" minlength="3" maxlength="30"/>
                </div>
            </div>
            <div class="row my-3">
                <label class="col-sm-3 col-form-label text-end"></label>
                <div class="col-sm-8">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember-login" value="1"/>
                        <label class="form-check-label" for="remember-login">记住登录</label>
                    </div>
                </div>
            </div>
            <div class="text-center my-3">
                @if(function_exists('csrf_field')){{csrf_field()}}@endif
                <button type="button" class="btn btn-primary ajax-submit-data">登录</button>
            </div>
        </div>
    </div>
</form>
@stop