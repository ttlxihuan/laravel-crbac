@extends('public.edit')
@section('body')
<div class="row my-3">
    <label class="col-sm-2 col-form-label text-end bg-light bg-light"><b class="text-danger">*</b> 名称</label>
    <div class="col-sm-4 position-relative">
        <input type="text" class="form-control" name="name" placeholder="角色名称" value="{{$item?$item->name:''}}" required="true" minlength="3" maxlength="30" remote="{{validate_url($item?$item:$modelClass,'name')}}"/>
    </div>
</div>
<div class="row my-3">
    <label class="col-sm-2 col-form-label text-end bg-light"><b class="text-danger">*</b> 状态</label>
    <div class="col-sm-4 position-relative">
        @foreach(Laravel\Crbac\Models\Power\Role::$_STATUS as $key=>$val)
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="status" value="{{$key}}" required="true" id="status-{{$key}}"@if($item && $item->status == $key) checked="checked"@endif/>
            <label class="form-check-label" for="status-{{$key}}">{{$val}}</label>
        </div>
        @endforeach
    </div>
</div>
<div class="row my-3">
    <label class="col-sm-2 col-form-label text-end bg-light"><b class="text-danger">*</b> 备注说明</label>
    <div class="col-sm-4 position-relative">
        <textarea class="form-control" name="comment" placeholder="备注详细说明用途" required="true">{{$item?$item->comment:''}}</textarea>
    </div>
</div>
@stop
