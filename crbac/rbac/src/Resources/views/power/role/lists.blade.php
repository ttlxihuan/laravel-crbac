@extends('public.lists')
@section('search')
<form>
    <ul class="search-lists clear">
        <li>{{$description}}名 : <input type="text" name="name" value="{{Input::get('name')}}" placeholder="{{$description}}名"/></li>
        <li>
            状态 :
            <select  name="status">
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
            <th width="200">角色名</th>
            <th>备注说明</th>
            <th width="60">状态</th>
            <th width="170">创建时间</th>
            <th width="180">操作</th>
        </tr>
    </thead>
    <tbody>
        @foreach($lists as $item)
        <tr>
            <td>{{$item->name}}</td>
            <td>{{$item->comment}}</td>
            <td>{{$item->statusName()}}</td>
            <td>{{$item->created_at}}</td>
            <td>
                @if(isControllerPower('edit'))<a href="edit/{{$item->getKey()}}.html" title="编辑{{$description}}">编辑</a>@endif
                @if(isControllerPower('admins'))<a href="admins/bind/{{$item->getKey()}}.html" title="查看这个角色下的管理员列表">管理员</i></a>@endif
                @if(isControllerPower('items'))<a href="items/{{$item->getKey()}}.html" title="角色下权限项编辑">权限</a>@endif
                @if(isControllerPower('delete'))<a href="delete/{{$item->getKey()}}.html" title="删除角色" class="confirm">删除</a>@endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@stop
