@extends('public.edit')
@section('body')
<div class="field-group clear">
    <label class="field-label">请求地址 :</label>
    <div class="field-value">
        <input type="text" placeholder="如果权限项有请求地址，可以快捷生成权限码" value="{{isset($route)?$route->url:''}}" id="router-url"/>
        <input type="button" onclick="getRouteUses('GET')" value="GET"/>
        <input type="button" onclick="getRouteUses('POST')" value="POST"/>
    </div>
    <div class="field-value redD">
        注意：该地址非必填，并且不作数据保存，仅仅只是快捷生成有请求地址的权限码，非路由权限项，无需操作
    </div>
</div>
<div class="field-group clear">
    <label class="field-label"><span class="redD">*</span>名称 :</label>
    <div class="field-value">
        <input type="text" name="name" placeholder="权限项名称" value="{{$item?$item->name:''}}" required="true" minlength="3" maxlength="30"/>
    </div>
</div>
<div class="field-group clear">
    <label class="field-label"><span class="redD">*</span>权限码 :</label>
    <div class="field-value">
        <input type="text" name="code" id="power-code" placeholder="唯一权限码" value="{{$item?$item->code:''}}" required="true" minlength="3" maxlength="80" remote="{{validate_url($item?$item:$modelClass,'code')}}"/>
    </div>
    <div class="field-value redD">
        注意：路由权限项该码由请求地址处操作生成，权限码须为controller@action的结构保存，勿手动修改。
    </div>
</div>
@include('power.item.edit_relate')
<div class="field-group clear">
    <label class="field-label"><span class="redD">*</span>备注说明 :</label>
    <div class="field-value">
        <textarea name="comment" placeholder="备注说明用途，作用，以便后续快速理解">{{$item?$item->comment:''}}</textarea>
    </div>
</div>
<div class="form-button">
    <input type="button" class=" btn-success ajax-submit-data" value="{{$item?'编辑':'创建'}}"/>
</div>
@stop
