@extends('public.lists')
@section('search')
<form>
    <div class="container mx-0 px-0">
        <div class="row justify-content-start navbar-expand">
            <div class="col mb-2">
                <div class="input-group">
                    <span class="input-group-text" id="basic-addon1">配置键</span>
                    <input type="text" class="form-control" name="key" value="{{request('key')}}" placeholder="配置键"/>
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
            <th class="{{$toOrder('key',false)}}" onclick="location.href ='{{$toOrder('key')}}'">配置键(key)</th>
            <th class="{{$toOrder('value',false)}}" onclick="location.href ='{{$toOrder('value')}}'">配置值(value)</th>
            <th width="80">类型(type)</th>
            <th>说明(comment)</th>
            <th width="80">状态(status)</th>
            <th width="170" class="{{$toOrder('updated',false)}}" onclick="location.href ='{{$toOrder('updated')}}'">更新时间</th>
            <th width="80">操作</th>
        </tr>
    </thead>
    <tbody>
        @foreach($lists as $index=>$item)
        <tr>
            <td>{{$index+1}}</td>
            <td>{{$item->key}}</td>
            <td>{{Str::limit($item->value, 80)}}</td>
            <td>{{$item->type}}</td>
            <td>{{$item->comment}}</td>
            <td>{{$item->getStatus()}}</td>
            <td>{{$item->updated_at}}</td>
            <td>
                @if(isControllerPower('edit'))<a href="{{crbac_route('.edit',[$item->getKey()])}}" title="编辑{{$description}}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>@endif
                @if(isControllerPower('delete'))<a href="{{crbac_route('.delete',[$item->getKey()])}}" title="删除{{$description}}" class="btn btn-sm btn-outline-danger confirm"><i class="fas fa-trash"></i></a>@endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@stop
