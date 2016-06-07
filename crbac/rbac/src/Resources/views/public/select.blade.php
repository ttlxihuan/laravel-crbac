<?php
$route_name = str_replace(['-', '.'], '_', preg_replace('/.select$/i', '', Route::currentRouteName()));
$select_name = $route_name ? '-' . $route_name : '';
$not_menu = true;
?>
@extends('layout')
@section('main')
@yield('body')
<?php echo $lists->render(); ?>
@if(Route::input('select_relation') == 'many')
<div style="width: 98%;margin: 0px auto;">
    <input type="button" id="reverse-select-item" value="反选"/>
    <input type="button" id="batch-select-item" value="选择选中项"/>
</div>
@endif
@if(Route::input('select_relation') == 'single')
<script type="text/javascript">
    $(function () {
        $('a.select-item').click(function () {
            $('#single{{$select_name}}-id', window.opener.document).val($(this).data('id'));
            $('#single{{$select_name}}-name', window.opener.document).val($(this).data('name'));
            window.close();
        });
    });
</script>
@elseif(Route::input('select_relation') == 'callback')
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
            var ids = $('#many{{$select_name}}-id', window.opener.document),
                    vids = [],
                    val = $(this).data('id').toString();
            $.each(ids.val().split(/\D+/g), function (k, v) {
                if (v > 0) {
                    vids.push(v);
                }
            });
            if ($.inArray(val, vids) === -1) {
                vids.push(val);
                $('#many{{$select_name}}-id', window.opener.document).val(vids.join(','));
                $('#many{{$select_name}}-name', window.opener.document).append('<span class="add-item">' + $(this).data('name') + '<i class="item-remove" onclick="remove{{str_replace("-", "_", $select_name)}}_item(this,' + val + ')">X</i></span>');
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
                alert('没有选中项！');
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