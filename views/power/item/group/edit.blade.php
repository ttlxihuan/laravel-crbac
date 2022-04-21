@extends('public.edit')
@section('body')
<div class="row my-3">
    <label class="col-sm-2 col-form-label text-end"><b class="text-danger">*</b> 名称</label>
    <div class="col-sm-4">
        <input type="text" class="form-control" name="name" placeholder="权限项组名称" value="{{$item?$item->name:''}}" required="true" minlength="3" maxlength="30" remote="{{validate_url($item?$item:$modelClass,'name')}}"/>
    </div>
</div>
<div class="row my-3">
    <label class="col-sm-2 col-form-label text-end"><b class="text-danger">*</b> 备注说明</label>
    <div class="col-sm-4">
        <textarea class="form-control" name="comment" placeholder="备注说明用途，作用，以便后续快速理解" required="true">{{$item?$item->comment:''}}</textarea>
    </div>
</div>
@stop
