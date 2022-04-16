@extends('public.edit')
@section('body')
<div class="field-group clear">
    <label class="field-label"><span class="redD">*</span>名称 :</label>
    <div class="field-value">
        <input type="text" name="name" placeholder="角色名称" value="{{$item?$item->name:''}}" required="true" minlength="3" maxlength="30" remote="{{validate_url($item?$item:$modelClass,'name')}}"/>
    </div>
</div>
<div class="field-group clear">
    <label class="field-label"><span class="redD">*</span>状态 :</label>
    <div class="field-value">
        <input type="radio" name="status" value="enable" id="status-enable"<?php if (!$item || $item->status == 'enable') { ?> checked="checked"<?php } ?>/> <label for="status-enable" class="label">启用</label>
        <input type="radio" name="status" value="disable" id="status-disable"<?php if ($item && $item->status == 'disable') { ?> checked="checked"<?php } ?>/> <label for="status-disable" class="label">禁用</label>
    </div>
</div>
<div class="field-group clear">
    <label class="field-label"><span class="redD">*</span>备注说明 :</label>
    <div class="field-value">
        <textarea name="comment" required="true" placeholder="备注说明用途，作用，以便后续快速理解">{{$item?$item->comment:''}}</textarea>
    </div>
</div>
<div class="form-button">
    <input type="button" class=" btn-success ajax-submit-data" value="{{$item?'编辑':'创建'}}"/>
</div>
@stop
