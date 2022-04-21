<?php
if ($item && !$item instanceof \Laravel\Crbac\Models\Power\Item) {
    $item = $item->item;
}
?>
<div class="row my-3">
    <label class="col-sm-2 col-form-label text-end"><b class="text-danger">*</b> 状态</label>
    <div class="col-sm-4">
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="status" value="enable" id="status-enable"{{(!$item || $item->status == 'enable')?' checked="checked"':''}}/>
            <label class="form-check-label" for="status-enable">启用</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="status" value="disable" id="status-disable"{{(!$item || $item->status == 'disable')?' checked="checked"':''}}/>
            <label class="form-check-label" for="status-disable">禁用</label>
        </div>
    </div>
</div>
<div class="row my-3">
    <label class="col-sm-2 col-form-label text-end"><b class="text-danger">*</b> 所在权限组</label>
    <div class="col-sm-4">
        <div class="input-group">
            <input type="text" class="form-control" name="power_item_group_id" id="single-power_item_group-id" placeholder="所在权限组ID" value="{{$item?$item->power_item_group_id:''}}" required="true" number="true" readonly="readonly"/>
            <input type="text" class="form-control" placeholder="所在权限组名" id="single-power_item_group-name" value="{{$item?$item->group->name:''}}" disabled="disabled"/>
            <button class="btn btn-outline-secondary" type="button" onclick="open_window('{{crbac_route('power.item-group.select',['single'])}}');">选择权限组</button>
        </div>
    </div>
</div>
@include('power.role.select_edit')
<script type="text/javascript">
    function getRouteUses(method, callback) {
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
//        if($('#router-url').data('old')===url){
//            return;
//        }
//        $('#router-url').val(url).data('old',url);
        $._ajax({
            url: url,
            type: method,
            dataType: 'json',
            headers: {
                'GET-ROUTER-USERS': 'true'
            },
            success: function (json) {
                if (json.status === 'success') {
                    $('#power-code').val(json.message.uses);
                    if (json.message.item){
                        $('#status-' + json.message.item.status).attr('checked', true);
                        $('#single-power_item_group-id').val(json.message.item.power_item_group_id);
                        $('#single-power_item_group-name').val(json.message.item.power_item_group_name);
                        var role = [], role_name = '';
                        for (var key in json.message.item.roles){
                            role.push(key);
                            role_name += '<span class="add-item">' + json.message.item.roles[key] + '<i class="item-remove" onclick="remove_power_role_item(this,' + key + ')">X</i></span>';
                        }
                        $('#many-power_role-id').val(role.join(','));
                        $('#many-power_role-name').html(role_name);
                    }
                    typeof callback === 'function' && callback(json.message);
                    return false;
                }
            }, error: function () {
                alert('请求地址未配置授权或地址不存在');
                $('#power-code').val('');
                $('#single-power_item_group-id').val('');
                $('#single-power_item_group-name').val('');
                $('#many-power_role-id').val('');
                $('#many-power_role-name').html('');
                return false;
            }
        });
    }
</script>