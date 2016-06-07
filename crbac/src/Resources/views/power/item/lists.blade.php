@extends('public.lists')
@section('search')
<form>
    <ul class="search-lists clear">
        <li>{{$description}}名 :<input type="text" name="name" value="{{Input::get('name')}}" placeholder="{{$description}}名" /></li>
        <li>权限项码 :<input type="text" name="code" value="{{Input::get('code')}}" placeholder="权限项码" /></li>
        <li>权限组 :
            <select  name="group_id" >
                <option value="" >请选择</option>
                @foreach(XiHuan\Crbac\Models\Power\ItemGroup::all() as $group)
                <option value="{{$group->getKey()}}"<?php if (Input::get('group_id') == $group->getKey()) { ?> selected="selected"<?php } ?>>{{$group->name}}</option>
                @endforeach
            </select>
        </li>
        <li>状态 :
            <select  name="status" >
                <option value="" >请选择</option>
                @foreach(XiHuan\Crbac\Models\Power\Item::$_STATUS as $key=>$val)
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
            <th width="18%">权限项/组</th>
            <th width="25%">权限码</th>
            <th>备注说明</th>
            <th width="60">状态</th>
            <th width="170" class="{{$toOrder('created',false)}}" onclick="location.href ='{{$toOrder('created')}}'">创建时间</th>
            <th width="80">操作</th>
        </tr>
    </thead>
    <tbody>
        @foreach($lists as $item)
        <tr>
            <td>{{$item->name}}<br/>【<a href="?group_id={{$item->group->getKey()}}">{{$item->group->name}}</a>】</td>
            <td>{{$item->code}}</td>
            <td>{{$item->comment}}</td>
            <td>{{$item->statusName()}}</td>
            <td>{{$item->created_at}}</td>
            <td>
                @if(isControllerPower('edit'))<a href="edit/{{$item->getKey()}}.html" title="编辑权限项">编辑</a>@endif
                @if(isControllerPower('delete'))<a href="delete/{{$item->getKey()}}.html" title="删除权限项" class="confirm">删除</a>@endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@stop
