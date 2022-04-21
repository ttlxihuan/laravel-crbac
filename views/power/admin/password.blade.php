@extends('public.edit')
@section('body')
<div class="field-group clear">
    <label class="field-label">真实姓名 :</label>
    <div class="field-value">
        {{$item->realname}}
    </div>
</div>
<div class="field-group clear">
    <label class="field-label">用户名 :</label>
    <div class="field-value">
        {{$item->username}}
    </div>
</div>
<div class="field-group clear">
    <label class="field-label">email :</label>
    <div class="field-value">
        <input type="text" name="email" placeholder="Email地址" value="{{$item->email}}" email="true"/>
    </div>
</div>
<div class="field-group clear">
    <label class="field-label"><span class="redD">*</span>原密码 :</label>
    <div class="field-value">
        <input type="password" name="old_password" placeholder="原登录密码" value="" required="true" minlength="6" maxlength="20"/>
    </div>
</div>
<div class="field-group clear">
    <label class="field-label"><span class="redD">*</span>新密码 :</label>
    <div class="field-value">
        <input type="password" name="password" placeholder="新登录密码" value="" required="true" minlength="6" maxlength="20"/>
    </div>
</div>
<div class="field-group clear">
    <label class="field-label"><span class="redD">*</span>确认密码 :</label>
    <div class="field-value">
        <input type="password" name="password_confirmation" placeholder="确认新登录密码" value="" required="true" minlength="6" maxlength="20"/>
    </div>
</div>
@stop
