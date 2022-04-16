<?php $not_add = true; ?>
@extends('public.lists')
@section('search')
@if(isControllerPower('updateRoutes'))
@section('nimble')
<a href="{{crbac_route('.update-routes')}}">更新列表</a>
@stop
@endif
<form>
    <ul class="search-lists clear">
        <li>控制器@方法 : <input type="text" name="uses" value="{{request('uses')}}" placeholder="控制器@方法"/></li>
        <li>{{$description}}地址 : <input type="text" name="url" value="{{request('url')}}" placeholder="{{$description}}地址"/></li>
        <li>
            状态 :
            <select  name="status" >
                <option value="" >请选择</option>
                @foreach(['yes'=>'已经添加权限','no'=>'未添加权限'] as $key=>$val)
                <option value="{{$key}}"<?php if (request('status') == $key) { ?> selected="selected"<?php } ?>>{{$val}}</option>
                @endforeach
            </select>
        </li>
        <li>
            可用 :
            <select  name="is_usable" >
                <option value="" >请选择</option>
                @foreach(['yes'=>'是','no'=>'否'] as $key=>$val)
                <option value="{{$key}}"<?php if (request('is_usable') == $key) { ?> selected="selected"<?php } ?>>{{$val}}</option>
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
            <th width="80">请求类型</th>
            <th width="80">路由可用</th>
            <th width="80">权限状态</th>
            <th width="150">操作</th>
        </tr>
    </thead>
    <tbody>
        @foreach($lists as $item)
        <tr>
            <td>{{$item->url}}</td>
            <td>{{$item->uses}}</td>
            <td>{{$item->methods}}</td>
            <td>{{$item->is_usable}}</td>
            <td>
                @if($item->item)
                {{$item->item->statusName()}}
                @else
                无权限
                @endif
            </td>
            <td>
                @if(!$item->item)
                @if(isControllerPower('add'))<a href="{{crbac_route('.add')}}?route={{$item->getKey()}}" title="添加权限项">到权限项</a>@endif
                @if(isPower('Laravel\Crbac\Controllers\Power\MenuController@add'))<a href="{{crbac_route('power.menu.add')}}?route={{$item->getKey()}}" title="添加菜单">到菜单</a>@endif
                @else
                -
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@stop
