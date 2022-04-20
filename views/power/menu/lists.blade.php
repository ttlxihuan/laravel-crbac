@extends('public.lists')
@section('search')
<form>
    <ul class="search-lists clear">
        <li>{{$description}}名 : <input type="text" name="name" value="{{request('name')}}" placeholder="{{$description}}名"/></li>
        <li><input type="submit" class=" btn-info" value="查询"/></li>
    </ul>
</form>
@stop
@section('lists')
<table class="table-lists">
    <thead>
        <tr>
            <th width="150">菜单名</th>
            <th width="250">链接地址</th>
            <th>备注说明</th>
            <th width="170" class="{{$toOrder('created',false)}}" onclick="location.href ='{{$toOrder('created')}}'">创建时间</th>
            <th width="80">操作</th>
        </tr>
    </thead>
    <tbody>
        @foreach($lists as $item)
        <tr>
            <td>{{$item->name}}</td>
            <td>{{$item->url}}</td>
            <td>{{$item->comment}}</td>
            <td>{{$item->created_at}}</td>
            <td>
                @if(isControllerPower('edit'))<a href="{{crbac_route('.edit',[$item->getKey()])}}" title="编辑{{$description}}">编辑</a>@endif
                @if(isControllerPower('delete'))<a href="{{crbac_route('.delete',[$item->getKey()])}}" title="删除{{$description}}" class="confirm">删除</a>@endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@stop
