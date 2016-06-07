@extends('public.lists')
@section('search')
<form>
    <ul class="search-lists clear">
        <li>真实姓名 : <input type="text" name="realname" value="{{Input::get('realname')}}" placeholder="真实姓名"/></li>
        <li>用户名 : <input type="text" name="username" value="{{Input::get('username')}}" placeholder="用户名"/></li>
        <li>
            状态 :
            <select  name="status" >
                <option value="" >请选择</option>
                @foreach(XiHuan\Crbac\Models\Admin::$_STATUS as $key=>$val)
                <option value="{{$key}}_"<?php if (Input::get('status') == $key . '_') { ?> selected="selected"<?php } ?>>{{$val}}</option>
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
            <th width="18%">真实姓名</th>
            <th width="25%">用户名</th>
            <th>菜单组</th>
            <th width="60">状态</th>
            <th width="170" class="{{$toOrder('created',false)}}" onclick="location.href ='{{$toOrder('created')}}'">创建时间</th>
            <th width="80">操作</th>
        </tr>
    </thead>
    <tbody>
        @foreach($lists as $item)
        <tr>
            <td>{{$item->realname}}</td>
            <td>{{$item->username}}</td>
            <td>{{$item->menuGroup?$item->menuGroup->name:'-'}}</td>
            <td>{{$item->statusName()}}</td>
            <td>{{$item->created_at}}</td>
            <td>
                @if(isControllerPower('edit'))<a href="edit/{{$item->getKey()}}.html" title="编辑权限项">编辑</a>@endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@stop
