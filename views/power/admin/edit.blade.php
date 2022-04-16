<?php $no_all_role = true; ?>
@extends('public.edit')
@section('body')
<div class="field-group clear">
    <label class="field-label"><span class="redD">*</span>真实姓名 :</label>
    <div class="field-value">
        <input type="text" name="realname" placeholder="真实姓名" value="{{$item?$item->realname:''}}" required="true" minlength="2" maxlength="30"/>
    </div>
</div>
<div class="field-group clear">
    <label class="field-label"><span class="redD">*</span>用户名 :</label>
    <div class="field-value">
        <input type="text" name="username" placeholder="用户名" value="{{$item?$item->username:''}}" required="true" minlength="3" maxlength="30" remote="{{validate_url($item?$item:$modelClass,'username')}}"/>
    </div>
</div>
<div class="field-group clear">
    <label class="field-label"><?php if(!$item){?><span class="redD">*</span><?php }?>密码 :</label>
    <div class="field-value">
        <input type="text" name="password" placeholder="{{$item?'不修改密码无需填写':'登录密码'}}" value=""<?php if(!$item){?> required="true"<?php }?> minlength="6" maxlength="20"/>
    </div>
</div>
<div class="field-group clear">
    <label class="field-label">email :</label>
    <div class="field-value">
        <input type="text" name="email" placeholder="Email地址" value="{{$item?$item->email:''}}" email="true"/>
    </div>
</div>
<div class="field-group clear">
    <label class="field-label"><span class="redD">*</span>状态 :</label>
    <div class="field-value">
        <select  name="status" >
            @foreach(Laravel\Crbac\Models\Power\Admin::$_STATUS as $key=>$val)
            <option value="{{$key}}"<?php if (($item && $item->status == $key) || (!$item && $key == 1)) { ?> selected="selected"<?php } ?>>{{$val}}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="field-group clear">
    <label class="field-label"><span class="redD">*</span>所在菜单组 :</label>
    <div class="field-value">
        <input type="text" name="power_menu_group_id" class="width100" id="single-power_menu_group-id" placeholder="所在权限组ID" value="{{$item?$item->power_menu_group_id:''}}" required="true" number="true" readonly="readonly"/>
        <input type="text" class="width200" placeholder="所在权限组名" id="single-power_menu_group-name" value="{{$item?$item->menuGroup->name:''}}" disabled="disabled"/>
        <input type="button" onclick="open_window('{{crbac_route('power.menu-group.select', ['single'])}}');" value="选择组"/>
    </div>
</div>
@include('power.role.select_edit')
<div class="form-button">
    <input type="button" class=" btn-success ajax-submit-data" value="{{$item?'编辑':'创建'}}"/>
</div>
<script type="text/javascript">
</script>
@stop
