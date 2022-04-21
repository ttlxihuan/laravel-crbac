<div class="row my-3">
    <label class="col-sm-2 col-form-label text-end"> 所在角色</label>
    <div class="col-sm-4">
        <div class="input-group">
            <input type="text" class="form-control" name="roles" id="many-power_role-id" placeholder="所在角色ID" value="{{$item?implode(',',$item->roles->modelKeys()):''}}" readonly="readonly" autocomplete="off"/>
            <button class="btn btn-outline-secondary" type="button" onclick="open_window('{{crbac_route('power.role.select', ['many'])}}');">选择角色</button>
            @if(!isset($no_all_role))
            <button class="btn btn-outline-info" type="button" onclick="$('#many-power_role-id').val('all'); $('#many-power_role-name').empty();">所有角色</button>
            @endif
        </div>
        <div id="many-power_role-name" class="mt-3">
            @if($item)
            @foreach($item->roles as $role)
            <span class="badge bg-primary m-1">{{$role->name}}<i class="item-remove" onclick="remove_power_role_item(this,{{$role->getKey()}})">X</i></span>
            @endforeach
            @endif
        </div>
    </div>
</div>
<script type="text/javascript">
    function remove_power_role_item(elem, vid) {
        $(elem).parent().remove();
        var ids = $('#many-power_role-id').val().split(/\D+/g),
                newIds = [];
        $.each(ids, function (k, v) {
            if (v != vid) {
                newIds.push(v);
            }
        });
        $('#many-power_role-id').val(newIds.join(','));
    }
</script>