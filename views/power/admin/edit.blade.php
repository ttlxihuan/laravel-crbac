<?php $no_all_role = true; ?>
@extends('public.edit')
@section('body')
<div class="row my-3">
    <label class="col-sm-2 col-form-label text-end bg-light"><b class="text-danger">*</b> 真实姓名</label>
    <div class="col-sm-4 position-relative">
        <input type="text" class="form-control" name="realname" placeholder="真实姓名" value="{{$item?$item->realname:''}}" required="true" minlength="2" maxlength="30"/>
    </div>
</div>
<div class="row my-3">
    <label class="col-sm-2 col-form-label text-end bg-light"><b class="text-danger">*</b> 用户名</label>
    <div class="col-sm-4 position-relative">
        <input type="text" class="form-control" name="username" placeholder="用户名" value="{{$item?$item->username:''}}" required="true" minlength="3" maxlength="30" remote="{{validate_url($item?$item:$modelClass, 'username')}}"/>
    </div>
</div>
<div class="row my-3">
    <label class="col-sm-2 col-form-label text-end bg-light"> Email</label>
    <div class="col-sm-4 position-relative">
        <input type="text" class="form-control" name="email" placeholder="Email地址" value="{{$item->email}}" email="true"/>
    </div>
</div>
<div class="row my-3">
    <label class="col-sm-2 col-form-label text-end bg-light">@if(empty($item))<b class="text-danger">*</b>@endif 密码</label>
    <div class="col-sm-4 position-relative">
        <input type="text" class="form-control" name="password" placeholder="{{$item?'不修改密码无需填写':'登录密码'}}" value=""<?php if (!$item) { ?> required="true"<?php } ?> minlength="6" maxlength="20"/>
    </div>
</div>
<div class="row my-3">
    <label class="col-sm-2 col-form-label text-end bg-light">email</label>
    <div class="col-sm-4 position-relative">
        <input type="text" class="form-control" name="email" placeholder="Email地址" value="{{$item?$item->email:''}}" email="true"/>
    </div>
</div>
<div class="row my-3">
    <label class="col-sm-2 col-form-label text-end bg-light"><b class="text-danger">*</b> 状态</label>
    <div class="col-sm-4 position-relative">
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="status" value="enable" required="true" id="status-enable"{{(!$item || $item->status == 'enable')?' checked="checked"':''}}/>
            <label class="form-check-label" for="status-enable">启用</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="status" value="disable" required="true" id="status-disable"{{(!$item || $item->status == 'disable')?' checked="checked"':''}}/>
            <label class="form-check-label" for="status-disable">禁用</label>
        </div>
    </div>
</div>
<div class="row my-3">
    <label class="col-sm-2 col-form-label text-end bg-light"><b class="text-danger">*</b> 所在菜单组</label>
    <div class="col-sm-4 position-relative">
        <div class="input-group">
            <input type="text" class="form-control" name="power_menu_group_id" class="width100" id="single-power_menu_group-id" placeholder="所在权限组ID" value="{{$item?$item->power_menu_group_id:''}}" required="true" number="true" readonly="readonly"/>
            <input type="text" class="form-control" placeholder="所在权限组名" id="single-power_menu_group-name" value="{{$item?$item->menuGroup->name:''}}" disabled="disabled"/>
            <button class="btn btn-outline-secondary" type="button" onclick="open_window('{{crbac_route('power.menu-group.select', ['single'])}}');">选择组</button>
        </div>
    </div>
</div>
@include('power.role.select_edit')
@stop
