<?php
if ($item && !$item instanceof \Laravel\Crbac\Models\Power\Item) {
    $item = $item->item;
}
?>
<div class="row my-3">
    <label class="col-sm-2 col-form-label text-end bg-light"><b class="text-danger">*</b> 状态</label>
    <div class="col-sm-4 position-relative">
        @foreach(Laravel\Crbac\Models\Power\Item::$_STATUS as $key=>$val)
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="status" value="{{$key}}" required="true" id="status-{{$key}}"@if($item && $item->status == $key) checked="checked"@endif/>
            <label class="form-check-label" for="status-{{$key}}">{{$val}}</label>
        </div>
        @endforeach
    </div>
</div>
<div class="row my-3">
    <label class="col-sm-2 col-form-label text-end bg-light"><b class="text-danger">*</b> 所在权限组</label>
    <div class="col-sm-4 position-relative">
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
            return $.popup.alert('请输入地址');
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
        $('#power-code').val('');
        $('#single-power_item_group-id').val('');
        $('#single-power_item_group-name').val('');
        $('#many-power_role-id').val('');
        $('#many-power_role-name').html('');
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
                        var elemShow = $('#many-power_role-name');
                        for (var key in json.message.item.roles){
                            add_many_select_item(elemShow, json.message.item.roles[key], key)
                        }
                    }
                    typeof callback === 'function' && callback(json.message);
                    return false;
                }
            }, error: function () {
                $.popup.alert('请求地址未配置授权或地址不存在');
                return false;
            }
        });
    }
</script>