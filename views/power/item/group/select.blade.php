@extends('public.select')
@section('body')
<div class="alert alert-primary">选择权限组</div>
<form>
    <div class="container mx-0 px-0">
        <div class="row justify-content-start navbar-expand">
            <div class="col mb-2">
                <div class="input-group">
                    <span class="input-group-text" id="basic-addon1">权限组名</span>
                    <input type="text" class="form-control" name="name" value="{{request('name')}}" placeholder="权限组名"/>
                </div>
            </div>
            <div class="col mb-2">
                <button type="submit" class="btn btn-primary">查询</button>
            </div>
        </div>
    </div>
</form>
<table class="table table-sm table-striped table-hover table-bordered">
    <thead>
        <tr class="table-secondary">
            <th><input id="all-select-item" type="checkbox"/> ID</th>
            <th>权限组名</th>
            <th width='100'>操作</th>
        </tr>
    </thead>
    <tbody>
        @foreach($lists as $item)
        <tr>
            <td>
                <input class="select-item" type="checkbox"/>
                {{$item->getKey()}}
            </td>
            <td title="{{$item->comment}}">{{$item->name}}</td>
            <td>
                <a href="javascript:void(0);" class="select-item" data-id="{{$item->getKey()}}" data-name="{{$item->name}}">选择</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@stop