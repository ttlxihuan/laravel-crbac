@extends('public.edit')
@section('body')
<div class="container-fluid">
    <div class="alert alert-info py-1">
        角色：<span class="fs-4">{{$role->name}}</span>
    </div>
    <table class="table table-sm table-striped table-hover table-bordered">
        <thead>
            <tr class="table-secondary">
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
                <td>
                    <input type="checkbox" class="all-checked"/>
                    {{$group->name}}
                </td>
                <td>
                    <div class="row m-0">
                        @foreach($lists[$group->getKey()] as $item)
                        @if($item->is_menu=$item->menus->count())
                        <div class="form-check form-check-inline col-5" title="{{$item->comment}}">
                            <input class="form-check-input" name="items[]" value="{{$item->getKey()}}" type="checkbox" id="power_item_{{$item->getKey()}}"<?php if (in_array($item->getKey(), $items)) { ?> checked="checked"<?php } ?>>
                            <label class="form-check-label" for="power_item_{{$item->getKey()}}">{{$item->name}}</label>
                        </div>
                        @endif
                        @endforeach
                    </div>
                </td>
                <td>
                    <div class="row m-0">
                        @foreach($lists[$group->getKey()] as $item)
                        @if(!$item->is_menu)
                        <div class="form-check form-check-inline col-2" title="{{$item->comment}}">
                            <input class="form-check-input" name="items[]" value="{{$item->getKey()}}" type="checkbox" id="power_item_{{$item->getKey()}}"<?php if (in_array($item->getKey(), $items)) { ?> checked="checked"<?php } ?>>
                            <label class="form-check-label" for="power_item_{{$item->getKey()}}">
                                {{$item->name}}
                            </label>
                        </div>
                        @endif
                        @endforeach
                    </div>
                </td>
            </tr>
            @endif
            @endforeach
        </tbody>
    </table>
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
