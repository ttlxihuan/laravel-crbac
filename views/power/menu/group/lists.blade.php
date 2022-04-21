@extends('public.lists')
@section('search')
@if(isAction('copy'))
<?php $not_add = true; ?>
<div class="alert alert-info py-1">
    复制菜单组：<span class="fs-4">{{$copy->name}}</span>
</div>
@endif
<form>
    <div class="container mx-0 px-0">
        <div class="row justify-content-start navbar-expand">
            <div class="col mb-2">
                <div class="input-group">
                    <span class="input-group-text" id="basic-addon1">{{$description}}名</span>
                    <input type="text" class="form-control" name="name" value="{{request('name')}}" placeholder="{{$description}}名"/>
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
