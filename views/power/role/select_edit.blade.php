<div class="row my-3">
    <label class="col-sm-2 col-form-label text-end bg-light"> 所在角色</label>
    <div class="col-sm-4 position-relative">
        <div class="input-group">
            <input type="text" class="form-control" name="roles" id="many-power_role-id" placeholder="所在角色ID" value="{{$item?implode(',',$item->roles->modelKeys()):''}}" readonly="readonly" autocomplete="off"/>
            <button class="btn btn-outline-secondary" type="button" onclick="open_window('{{crbac_route('power.role.select', ['many'])}}');">选择角色</button>
            @if(!isset($no_all_role))
            <button class="btn btn-outline-info" type="button" onclick="$('#many-power_role-id').val('all'); $('#many-power_role-name').empty();">所有角色</button>
            @endif
        </div>
        <div id="many-power_role-name">
            @if($item)
            @foreach($item->roles as $role)
            <div class="d-inline-block m-2">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light">{{$role->name}}</span>
                    <span class="input-group-text"><button type="button" class="btn-close" onclick="remove_many_select_item(this,{{$role->getKey()}})"></button></span>
                </div>
            </div>
            @endforeach
            @endif
        </div>
    </div>
</div>
<script type="text/javascript">
    function remove_many_select_item(elem, vid) {
        var elemItem = $(elem).parents('.d-inline-block'),
            elemShow = elemItem.parent(),
            elemVals = get_many_select_values_elem(elemShow),
            vids = get_many_select_items(elemVals),
            index = $.inArray(String(vid), vids);
        elemItem.remove();
        if (index >= 0) {
            vids.splice(index, 1);
        }
        elemVals.val(vids.join(','));
    }
    function add_many_select_item(elemShow, title, vid) {
        var elemVals = get_many_select_values_elem(elemShow), vids = get_many_select_items(elemVals);
        console.info(vid, vids, $.inArray(String(vid), vids))
        if ($.inArray(String(vid), vids) < 0) {
            var tag = '<div class="d-inline-block m-2"><div class="input-group input-group-sm">';
            tag += '<span class="input-group-text bg-light">' + title + '</span>';
            tag += '<span class="input-group-text"><button type="button" class="btn-close" onclick="remove_many_select_item(this,' + vid + ')"></button></span>';
            tag += '</div></div>';
            $(elemShow).append(tag);
            vids.push(vid);
        } else {
            return false;
        }
        $(elemVals).val(vids.join(','));
        return true;
    }
    function get_many_select_items(elemVals) {
        var vids = [];
        $.each(elemVals.val().split(/\D+/g), function (k, v) {
            if (v > 0) {
                vids.push(v);
            }
        });
        return vids;
    }
    function get_many_select_values_elem(elemShow) {
        return $(elemShow).prevAll('div.input-group').find(':text[readonly]');
    }
</script>