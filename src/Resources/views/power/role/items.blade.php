@extends('public.edit')
@section('body')
<h4 style="text-align: center;">角色：<span style="font-size: 14px;">{{$role->name}}</span></h4>
<table class="table-lists">
    <thead>
        <tr>
            <th width="120">
                权限组
            </th>
            <th>菜单权限项</th>
            <th>权限项</th>
        </tr>
    </thead>
    <tbody>
        @foreach($group_lists as $group)
        @if($lists->has($group->getKey()))
        <tr>
            <td class="width150">
                <input type="checkbox" class="all-checked"/>
                {{$group->name}}
            </td>
            <td>
                @foreach($lists[$group->getKey()] as $item)
                @if($item->is_menu=$item->menus->count())
                <div class="fl width150 tl" title="{{$item->comment}}">
                    <input name="items[]" value="{{$item->getKey()}}" type="checkbox" id="power_item_{{$item->getKey()}}"<?php if (in_array($item->getKey(), $items)) { ?> checked="checked"<?php } ?>>
                    <label for="power_item_{{$item->getKey()}}">{{$item->name}}</label>
                </div>
                @endif
                @endforeach
            </td>
            <td>
                @foreach($lists[$group->getKey()] as $item)
                @if(!$item->is_menu)
                <div class="fl width210 tl" title="{{$item->comment}}">
                    <input name="items[]" value="{{$item->getKey()}}" type="checkbox" id="power_item_{{$item->getKey()}}"<?php if (in_array($item->getKey(), $items)) { ?> checked="checked"<?php } ?>>
                    <label for="power_item_{{$item->getKey()}}">
                        {{$item->name}}
                    </label>
                </div>
                @endif
                @endforeach
            </td>
        </tr>
        @endif
        @endforeach
    </tbody>
</table>
<div class="form-button">
    <input type="button" class=" btn-success ajax-submit-data" value="保存"/>
</div>
<script type="text/javascript">
    $(':checkbox.all-checked').click(function () {
        var checked = this.checked;
        $(this).parent().nextAll('td').find(':checkbox').each(function () {
            this.checked = checked;
        });
    });
</script>
@stop
