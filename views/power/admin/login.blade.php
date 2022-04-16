@extends('layout')
@section('css')
<style type="text/css">
    .login.popbox{border:none;background-color: rgba(200,200,200,.5);border-radius: 10px;}
    .login div.field-value{width:200px;}
    .login div.field-value input{width:200px;}
    .login .widget-title{text-align: center;margin-bottom: 30px;border-radius: 10px 10px 0px 0px;padding: 15px 0px;border-top: none;border-bottom: 3px rgba(200,200,200,0.9) solid;}
    .login div.field-group,.login div.field-value{background: none;}
</style>
@stop
@section('main')
<form>
    <div class="popbox login">
        <h5 class="widget-title">管理后台登录</h5>
        <div class="field-group clear">
            <label class="field-label"><span class="redD">*</span>账号 :</label>
            <div class="field-value">
                <input type="text" name="username" placeholder="账号名" value="" required="true" minlength="3" maxlength="30"/>
            </div>
        </div>
        <div class="field-group clear">
            <label class="field-label"><span class="redD">*</span>密码 :</label>
            <div class="field-value">
                <input type="password" name="password" placeholder="密码" value="" required="true" minlength="3" maxlength="30"/>
            </div>
        </div>
        <div class="form-button">
            @if(function_exists('csrf_field')){{csrf_field()}}@endif
            <input type="button" class=" btn-success ajax-submit-data" value="登录"/>
        </div>
    </div>
</form>
@stop