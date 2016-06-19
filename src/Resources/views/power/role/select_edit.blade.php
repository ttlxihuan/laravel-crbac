<div class="field-group clear">
    <label class="field-label">所在角色 :</label>
    <div class="field-value">
        <input type="text" name="roles" id="many-power_role-id" placeholder="所在角色ID" value="{{$item?implode(',',$item->roles->modelKeys()):''}}" readonly="readonly" autocomplete="off"/>
        <input type="button" onclick="open_window('{{route('power.role.select','many')}}');" value="选择角色"/>
        @if(!isset($no_all_role))
        <input type="button" onclick="$('#many-power_role-id').val('all'); $('#many-power_role-name').empty();" value="所有角色"/>
        @endif
    </div>
    <div class="field-value" id="many-power_role-name">
        @if($item)
        @foreach($item->roles as $role)
        <span class="add-item">{{$role->name}}<i class="item-remove" onclick="remove_role_item(this,{{$role->getKey()}})">X</i></span>
        @endforeach
        @endif
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