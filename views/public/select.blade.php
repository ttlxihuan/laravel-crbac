<?php
$item = $lists[0];
if ($item) {
    $route_name = $item->getTable();
    $select_name = '-' . $route_name;
} else {
    $select_name = $route_name = '';
}
$not_menu = true;
?>
@extends('layout')
@section('main')
@yield('body')
{!!$lists->render()!!}
@if($relation == 'many')
<div style="width: 98%;margin: 0px auto;">
    <input type="button" id="reverse-select-item" value="反选"/>
    <input type="button" id="batch-select-item" value="选择选中项"/>
</div>
@endif
@if($relation == 'single')
<script type="text/javascript">
    $(function () {
        $('a.select-item').click(function () {
            $('#single{{$select_name}}-id', window.opener.document).val($(this).data('id')).valid();
            $('#single{{$select_name}}-name', window.opener.document).val($(this).data('name'));
            window.close();
        });
    });
</script>
@elseif($relation == 'callback')
<script type="text/javascript">
    $(function () {
        $('a.select-item').click(function () {
            var func = '{{$route_name}}_select_callback';
            if (window.opener[func] && typeof window.opener[func] === 'function') {
                window.opener[func]($(this).data('id'), $(this).data('name'), window);
            }
        });
    });
</script>
@else
<script type="text/javascript">
    $(function () {
        $('a.select-item').click(function () {
            if (window.opener.add_many_select_item) {
                if (!window.opener.add_many_select_item($('#many{{$select_name}}-name', window.opener.document), $(this).data('name'), $(this).data('id').toString())) {
                    $.popup.alert('已经存在：' + $(this).data('name'), 'warn', 3);
                }
            }
        });
        //全选处理
        $('#all-select-item').click(function () {
            var checked = this.checked;
            $('input.select-item').each(function () {
                this.checked = checked;
            });
        });
        //反选处理
        $('#reverse-select-item').click(function () {
            $('input.select-item').each(function () {
                this.checked = !this.checked;
            });
            $('#all-select-item')[0].checked = $('input.select-item:checked').size() >= checkboxs;
        });
        var checkboxs = $('input.select-item').click(function () {
            $('#all-select-item')[0].checked = this.checked && $('input.select-item:checked').size() >= checkboxs;
        }).size();
        //选中项批量处理
        $('#batch-select-item').click(function () {
            var checkeds = $('input.select-item:checked');
            if (!checkeds.length) {
                $.popup.alert('没有选中项！');
            } else {
                checkeds.each(function () {
                    $(this).parents('tr').find('td:last a').click();
                });
            }
        });
    });
</script>
@endif
@stop