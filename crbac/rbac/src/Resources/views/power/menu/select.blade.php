@extends('public.select')
@section('body')
<form>
    <ul class="search-lists clear">
        <li>菜单名 :<input type="text" name="name" value="{{Input::get('name')}}" placeholder="菜单名"/></li>
        <li><input type="submit" value="查询"/></li>
    </ul>
</form>
<h5 class="widget-title">选择菜单</h5>
<table class="table-lists">
    <thead>
        <tr>
            <th><input id="all-select-item" type="checkbox"/> ID</th>
            <th>菜单名</th>
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