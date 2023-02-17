<?php
$not_add = false;
$uses_confirm = true;
?>
@extends('public.lists')
@section('search')
<div class="alert alert-info py-1">
    角色：<span class="fs-4">{{$role->name}}</span>
</div>
@section('nimble')
@if($type==='bind')
@if(isControllerPower('addAdmin'))<a href="{{crbac_route('power.role.admins', ['unbind', $role->getKey()])}}">未绑管理员</a>@endif
@else
@if(isControllerPower('removeAdmin'))<a href="{{crbac_route('power.role.admins', ['bind', $role->getKey()])}}">已绑管理员</a>@endif
@endif
@stop
<form>
    <div class="container mx-0 px-0">
        <div class="row justify-content-start navbar-expand">
            <div class="col mb-2">
                <div class="input-group">
                    <span class="input-group-text" id="basic-addon1">真实姓名</span>
                    <input type="text" class="form-control" name="realname" value="{{request('realname')}}" placeholder="真实姓名"/>
                </div>
            </div>
            <div class="col mb-2">
                <div class="input-group">
                    <span class="input-group-text" id="basic-addon1">帐号</span>
                    <input type="text" class="form-control" name="username" value="{{request('username')}}" placeholder="帐号"/>
                </div>
            </div>
            <div class="col mb-2">
                <div class="input-group">
                    <label class="input-group-text">状态</label>
                    <select class="form-select" name="status">
                        <option value="">全部</option>
                        @foreach(Laravel\Crbac\Models\Power\Role::$_STATUS as $key=>$val)
                        <option value="{{$key}}"<?php if (request('status') == $key) { ?> selected="selected"<?php } ?>>{{$val}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col mb-2">
                <button type="submit" class="btn btn-primary">查询</button>
            </div>
        </div>
    </div>
</form>
@stop
@section('lists')
<table class="table table-sm table-striped table-hover table-bordered">
    <thead>
        <tr class="table-secondary">
            <th>序号</th>
            <th>真实姓名</th>
            <th>帐号</th>
            <th>状态</th>
            <th width="170">创建时间</th>
            <th width="180">操作</th>
        </tr>
    </thead>
    <tbody>
        @foreach($lists as $index=>$item)
        <tr>
            <td>{{$index+1}}</td>
            <td>{{$item->realname}}</td>
            <td>{{$item->username}}</td>
            <td>{{$item->statusName()}}</td>
            <td>{{$item->created_at}}</td>
            <td>
                @if($type==='bind')
                @if(isControllerPower('removeAdmin'))<a href="{{crbac_route('power.role.remove-admin',[$role->getKey(), $item->getKey()])}}" title="移除当前角色" data-confirm="确定把{{$item->realname}}移除角色吗？" class="confirm">移除</a>@endif
                @else
                @if(isControllerPower('addAdmin'))<a href="{{crbac_route('power.role.add-admin',[$role->getKey(), $item->getKey()])}}" title="添加到当前角色" data-confirm="确定把{{$item->realname}}添加到角色吗？" class="confirm">添加</a>@endif
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@stop
