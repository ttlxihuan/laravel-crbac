@extends('public.edit')
@section('body')
<div class="row my-3">
    <label class="col-sm-2 col-form-label text-end bg-light"> 真实姓名</label>
    <div class="col-sm-4 position-relative">
        <input type="text" class="form-control" placeholder="真实姓名" value="{{$item->realname}}" disabled=""/>
    </div>
</div>
<div class="row my-3">
    <label class="col-sm-2 col-form-label text-end bg-light"> 用户名</label>
    <div class="col-sm-4 position-relative">
        <input type="text" class="form-control" placeholder="用户名" value="{{$item->username}}" disabled=""/>
    </div>
</div>
<div class="row my-3">
    <label class="col-sm-2 col-form-label text-end bg-light"> Email</label>
    <div class="col-sm-4 position-relative">
        <input type="text" class="form-control" name="email" placeholder="Email地址" value="{{$item->email}}" disabled=""/>
    </div>
</div>
<div class="row my-3">
    <label class="col-sm-2 col-form-label text-end bg-light"><b class="text-danger">*</b> 原密码</label>
    <div class="col-sm-4 position-relative">
        <input type="password" class="form-control" name="old_password" placeholder="原登录密码" value="" required="true" minlength="6" maxlength="20"/>
    </div>
</div>
<div class="row my-3">
    <label class="col-sm-2 col-form-label text-end bg-light"><b class="text-danger">*</b> 新密码</label>
    <div class="col-sm-4 position-relative">
        <input type="password" class="form-control" name="password" placeholder="新登录密码" value="" required="true" minlength="6" maxlength="20"/>
    </div>
</div>
<div class="row my-3">
    <label class="col-sm-2 col-form-label text-end bg-light"><b class="text-danger">*</b> 确认密码</label>
    <div class="col-sm-4 position-relative">
        <input type="password" class="form-control" name="password_confirmation" placeholder="确认新登录密码" value="" required="true" minlength="6" maxlength="20"/>
    </div>
</div>
@stop
