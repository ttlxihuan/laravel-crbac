@extends('public.lists')
@section('search')
@if(isAction('copy'))
<?php $not_add = true; ?>
<h4 style="text-align: center;">复制菜单组：<span style="font-size: 14px;">{{$copy->name}}</span></h4>
@endif
<form>
    <ul class="search-lists clear">
        <li>{{$description}}名 :<input type="text" name="name" value="{{request('name')}}" placeholder="{{$description}}名"/></li>
        <li><input type="submit" class=" btn-info" value="查询"/></li>
    </ul>
</form>
@stop
@section('lists')
<table class="table-lists">
    <thead>
        <tr>
            <th width="200">菜单组名</th>
            <th>备注说明</th>
            <th width="170" class="{{$toOrder('created',false)}}" onclick="location.href ='{{$toOrder('created')}}'">创建时间</th>
            <th width="170">操作</th>
        </tr>
    </thead>
    <tbody>
        @foreach($lists as $item)
        <tr>
            <td>{{$item->name}}</td>
            <td>{{$item->comment}}</td>
            <td>{{$item->created_at}}</td>
            <td>
                @if(isAction('lists'))
                @if(isControllerPower('edit'))<a href="{{crbac_route('.edit', [$item->getKey()])}}" title="编辑{{$description}}">编辑</a>@endif
                @if(isControllerPower('menus'))<a href="{{crbac_route('.menus', [$item->getKey()])}}" title="编辑菜单组下菜单层级关系">菜单</a>@endif
                @if(isControllerPower('copy'))<a href="{{crbac_route('.copy', [$item->getKey()])}}" title="复制菜单组下关系">复制</a>@endif
                @if(isControllerPower('delete'))<a href="{{crbac_route('.delete', [$item->getKey()])}}" title="删除{{$description}}" class="confirm">删除</a>@endif
                @else
                @if(isControllerPower('pasted'))<a href="{{crbac_route('.pasted', [$copy->getKey(), $item->getKey()])}}" title="粘贴菜单组关系去编辑">粘贴</a>@endif
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@stop
