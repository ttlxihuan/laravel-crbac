<?php $not_add = false;$uses_confirm = true; ?>
@extends('public.lists')
@section('search')
<h4 style="text-align: center;">角色：<span style="font-size: 14px;">{{$role->name}}</span></h4>
@section('nimble')
@if(Route::input('bind_style')==='bind')
<a href="../unbind/{{$role->getKey()}}.html">新增管理员</a>
@else
<a href="../bind/{{$role->getKey()}}.html">移除管理员</a>
@endif
@stop
<form>
    <ul class="search-lists clear">
        <li>真实姓名 : <input type="text" name="realname" value="{{Input::get('realname')}}" placeholder="真实姓名"/></li>
        <li>帐号 : <input type="text" name="username" value="{{Input::get('username')}}" placeholder="帐号"/></li>
        <li>
            状态 :
            <select  name="status" >
                <option value="" >请选择</option>
                @foreach(XiHuan\Crbac\Models\Power\Role::$_STATUS as $key=>$val)
                <option value="{{$key}}"<?php if (Input::get('status') == $key) { ?> selected="selected"<?php } ?>>{{$val}}</option>
                @endforeach
            </select>
        </li>
        <li><input type="submit" class=" btn-info" value="查询"/></li>
    </ul>
</form>
@stop
@section('lists')
<table class="table-lists">
    <thead>
        <tr>
            <th width="20%">真实姓名</th>
            <th width="20%">帐号</th>
            <th width="20%">状态</th>
            <th width="20%">创建时间</th>
            <th width="20%">操作</th>
        </tr>
    </thead>
    <tbody>
        @foreach($lists as $item)
        <tr>
            <td>{{$item->realname}}</td>
            <td>{{$item->username}}</td>
            <td>{{$item->statusName()}}</td>
            <td>{{$item->created_at}}</td>
            <td>
                @if(Route::input('bind_style')==='bind')
                @if(isControllerPower('removeAdmin'))<a href="/power/role/admin/remove/{{$role->getKey()}}/{{$item->getKey()}}.html" title="移除当前角色" data-confirm="确定把{{$item->realname}}移除角色吗？" class="confirm">移除</a>@endif
                @else
                @if(isControllerPower('addAdmin'))<a href="/power/role/admin/add/{{$role->getKey()}}/{{$item->getKey()}}.html" title="添加到当前角色" data-confirm="确定把{{$item->realname}}添加到角色吗？" class="confirm">添加</a>@endif
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@stop
