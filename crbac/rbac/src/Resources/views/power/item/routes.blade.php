<?php $not_add = true; ?>
@extends('public.lists')
@section('search')
@if(isControllerPower('updateRoutes'))
@section('nimble')
<a href="/power/item/update/routes.html">更新列表</a>
@stop
@endif
<form>
    <ul class="search-lists clear">
        <li>控制器@方法 : <input type="text" name="uses" value="{{Input::get('uses')}}" placeholder="控制器@方法"/></li>
        <li>{{$description}}地址 : <input type="text" name="url" value="{{Input::get('url')}}" placeholder="{{$description}}地址"/></li>
        <li>
            状态 :
            <select  name="status" >
                <option value="" >请选择</option>
                @foreach(['yes'=>'已经添加权限','no'=>'未添加权限'] as $key=>$val)
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
            <th>{{$description}}地址</th>
            <th width="35%">控制器@方法</th>
            <th width="80">权限状态</th>
            <th width="150">操作</th>
        </tr>
    </thead>
    <tbody>
        @foreach($lists as $item)
        <tr>
            <td>{{$item->url}}</td>
            <td>{{$item->uses}}</td>
            <td>
                @if($item->item)
                {{$item->item->statusName()}}
                @else
                无权限
                @endif
            </td>
            <td>
                @if(!$item->item)
                @if(isControllerPower('add'))<a href="/power/item/add.html?route={{$item->getKey()}}" title="添加权限项">到权限项</a>@endif
                @if(isPower('XiHuan\Crbac\Controllers\Power\MenuController@add'))<a href="/power/menu/add.html?route={{$item->getKey()}}" title="添加菜单">到菜单</a>@endif
                @else
                -
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@stop
