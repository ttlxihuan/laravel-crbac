@extends('public.edit')
@section('body')
<div class="row my-3">
    <label class="col-sm-2 col-form-label text-end bg-light"> 请求地址</label>
    <div class="col-sm-4">
        <div class="input-group position-relative">
            <input type="text" class="form-control" placeholder="如果权限项有请求地址，可以快捷生成权限码" value="{{isset($route)?$route->url:''}}" id="router-url"/>
            <button class="btn btn-outline-secondary" type="button" onclick="getRouteUses('GET')">GET</button>
            <button class="btn btn-outline-primary" type="button" onclick="getRouteUses('POST')">POST</button>
        </div>
        <p class="text-danger">注意：该地址非必填，并且不作数据保存，仅仅只是快捷生成有请求地址的权限码，非路由权限项，无需操作</p>
    </div>
</div>
<div class="row my-3">
    <label class="col-sm-2 col-form-label text-end bg-light"><b class="text-danger">*</b> 名称</label>
    <div class="col-sm-4 position-relative">
        <input type="text" class="form-control" name="name" placeholder="权限项名称" value="{{$item?$item->name:''}}" required="true" minlength="3" maxlength="30"/>
    </div>
</div>
<div class="row my-3">
    <label class="col-sm-2 col-form-label text-end bg-light"><b class="text-danger">*</b> 权限码</label>
    <div class="col-sm-4">
        <div class="position-relative">
            <input type="text" class="form-control" name="code" id="power-code" placeholder="唯一权限码" value="{{$item?$item->code:''}}" required="true" minlength="3" maxlength="80" remote="{{validate_url($item?$item:$modelClass,'code')}}"/>
        </div>
        <p class="text-danger">注意：路由权限项该码由请求地址处操作生成，权限码须为controller@action的结构保存，勿手动修改。</p>
    </div>
</div>
@include('power.item.edit_relate')
<div class="row my-3">
    <label class="col-sm-2 col-form-label text-end bg-light"><b class="text-danger">*</b> 备注说明</label>
    <div class="col-sm-4 position-relative">
        <textarea class="form-control" name="comment" placeholder="备注详细说明用途" required="true">{{$item?$item->comment:''}}</textarea>
    </div>
</div>
@if(isset($route) && !$item)
<script type="text/javascript">
    $(function () {
        getRouteUses('{{explode(",", $route->methods)[0]}}');
    });
</script>
@endif
@stop