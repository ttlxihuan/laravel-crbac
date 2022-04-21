@extends('public.lists')
@section('search')
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
                <div class="input-group">
                    <label class="input-group-text">状态</label>
                    <select class="form-select" name="status">
                        <option value="" >请选择</option>
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
                @if(isControllerPower('edit'))<a href="{{crbac_route('.edit',[$item->getKey()])}}" title="编辑{{$description}}">编辑</a>@endif
                @if(isControllerPower('admins'))<a href="{{crbac_route('.admins',['bind', $item->getKey()])}}" title="查看这个角色下的管理员列表">管理员</i></a>@endif
                @if(isControllerPower('items'))<a href="{{crbac_route('.items',[$item->getKey()])}}" title="角色下权限项编辑">权限</a>@endif
                @if(isControllerPower('delete'))<a href="{{crbac_route('.delete',[$item->getKey()])}}" title="删除{{$description}}" class="confirm">删除</a>@endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@stop
