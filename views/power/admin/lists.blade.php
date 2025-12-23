@extends('public.lists')
@section('search')
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
                    <span class="input-group-text" id="basic-addon1">用户名</span>
                    <input type="text" class="form-control" name="username" value="{{request('username')}}" placeholder="用户名"/>
                </div>
            </div>
            <div class="col mb-2">
                <div class="input-group">
                    <label class="input-group-text">状态</label>
                    <select class="form-select" name="status">
                        <option value="">全部</option>
                        @foreach(Laravel\Crbac\Models\Power\Admin::$_STATUS as $key=>$val)
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
            <th width="18%">真实姓名</th>
            <th width="25%">用户名</th>
            <th>菜单组</th>
            <th width="60">状态</th>
            <th>锁定时间</th>
            <th width="170" class="{{$toOrder('created',false)}}" onclick="location.href ='{{$toOrder('created')}}'">创建时间</th>
            <th width="180">操作</th>
        </tr>
    </thead>
    <tbody>
        @foreach($lists as $index=>$item)
        <tr>
            <td>{{$index+1}}</td>
            <td>{{$item->realname}}</td>
            <td>{{$item->username}}</td>
            <td>{{$item->menuGroup?$item->menuGroup->name:'-'}}</td>
            <td>{{$item->getStatus()}}</td>
            <td>{{$item->locked_at ? date('Y-m-d H:i:s', $item->locked_at) : '-'}}</td>
            <td>{{$item->created_at}}</td>
            <td>
                @if(isControllerPower('edit'))<a href="{{crbac_route('.edit', [$item->getKey()])}}" title="编辑{{$description}}">编辑</a>@endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@stop
