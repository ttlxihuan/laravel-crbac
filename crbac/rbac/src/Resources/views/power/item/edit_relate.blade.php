<?php
if ($item && !$item instanceof \XiHuan\Crbac\Models\Power\Item) {
    $item = $item->item;
}
?>
<div class="field-group clear">
    <label class="field-label"><span class="redD">*</span>状态 :</label>
    <div class="field-value">
        <input type="radio" name="status" value="enable" id="status-enable"<?php if (!$item || $item->status == 'enable') { ?> checked="checked"<?php } ?>/> <label for="status-enable" class="label">启用</label>
        <input type="radio" name="status" value="disable" id="status-disable"<?php if ($item && $item->status == 'disable') { ?> checked="checked"<?php } ?>/> <label for="status-disable" class="label">禁用</label>
    </div>
</div>
<div class="field-group clear">
    <label class="field-label"><span class="redD">*</span>所在权限组 :</label>
    <div class="field-value">
        <input type="text" class="width100" name="power_item_group_id" id="single-power_group_item-id" placeholder="所在权限组ID" value="{{$item?$item->power_item_group_id:''}}" required="true" number="true" readonly="readonly"/>
        <input type="text" class="width200" placeholder="所在权限组名" id="single-power_group_item-name" value="{{$item?$item->group->name:''}}" disabled="disabled"/>
        <input type="button" onclick="open_window('{{route('power.group.item.select','single')}}');" value="选择组"/>
    </div>
</div>
@include('power.role.select_edit')
<script type="text/javascript">
    function getRouteUses(method,callback) {
        var url = $.trim($('#router-url').val());
        if (!url) {
            return alert('请输入地址');
        }
        if (/^http:\/\/.+/ig.test(url)) {
            url = url.replace(/^http:\/\/[^\/]+/, '');
        }
        if (url.charAt(0) !== '/') {
            url = '/' + url;
        }
        if($('#router-url').data('old')===url){
            return;
        }
        $('#router-url').val(url).data('old',url);
        $._ajax({
            url: url,
            type: method,
            dataType: 'json',
            headers: {
                'GET-ROUTER-USERS': 'true'
            },
            success: function (json) {
                if (json.status === 'success') {
                    typeof callback==='function'&&callback();
                    $('#power-code').val(json.message.uses);
                    return false;
                }
            }, error: function () {
                alert('请求地址404！');
                $('#power-code').val('');
                return false;
            }
        });
    }
</script>