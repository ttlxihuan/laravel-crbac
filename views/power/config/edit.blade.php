@extends('public.edit')
@section('body')
<div class="row my-3">
    <label class="col-sm-2 col-form-label text-end bg-light"><b class="text-danger">*</b> 配置键(key)</label>
    <div class="col-sm-4 position-relative">
        <input type="text" class="form-control" name="key" placeholder="配置键名，如：site_name" value="{{$item?$item->key:''}}" required="true" maxlength="100" @if($item) readonly="readonly" @endif remote="{{validate_url($item?$item:$modelClass,'key')}}"/>
    </div>
</div>
<div class="row my-3">
    <label class="col-sm-2 col-form-label text-end bg-light"><b class="text-danger">*</b> 类型(type)</label>
    <div class="col-sm-4 position-relative">
        @foreach(['string'=>'字符串','number'=>'数字','json'=>'JSON','boolean'=>'布尔'] as $tkey=>$tval)
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="type" value="{{$tkey}}" required="true" id="type-{{$tkey}}"@if(($item && $item->type == $tkey) || (!$item && $tkey == 'string')) checked="checked"@endif/>
            <label class="form-check-label" for="type-{{$tkey}}">{{$tval}}</label>
        </div>
        @endforeach
    </div>
</div>
<div class="row my-3">
    <label class="col-sm-2 col-form-label text-end bg-light"><b class="text-danger">*</b> 配置值(value)</label>
    <div class="col-sm-4 position-relative" id="value-container">
        <div id="value-string" class="value-control d-none">
            <input type="text" class="form-control" name="value" placeholder="请输入字符串值" value="{{$item?$item->value:''}}" required="true" maxlength="65535"/>
        </div>
        <div id="value-number" class="value-control d-none">
            <input type="number" class="form-control" name="value" placeholder="请输入数字" value="{{$item?$item->value:''}}" required="true"/>
        </div>
        <div id="value-boolean" class="value-control d-none">
            <div class="d-flex align-items-center" style="gap:1.2em;padding-top:0.35rem;">
                <div class="form-check form-check-inline mb-0">
                    <input class="form-check-input" type="radio" name="value" value="1" id="bool-true" required="true" @if($item && $item->value == 1) checked="checked"@endif/>
                    <label class="form-check-label" for="bool-true">true (1)</label>
                </div>
                <div class="form-check form-check-inline mb-0">
                    <input class="form-check-input" type="radio" name="value" value="0" id="bool-false" required="true" @if($item && $item->value == 0) checked="checked"@endif/>
                    <label class="form-check-label" for="bool-false">false (0)</label>
                </div>
            </div>
        </div>
        <div id="value-json" class="value-control d-none">
            <textarea class="form-control" name="value" rows="6" placeholder='请输入合法JSON，如：{"key":"value"}' value="{{$item?$item->value:''}}" required="true" maxlength="65535"></textarea>
            <div class="form-text text-muted">请输入合法的 JSON 格式内容</div>
        </div>
    </div>
</div>
<div class="row my-3">
    <label class="col-sm-2 col-form-label text-end bg-light"><b class="text-danger">*</b> 说明(comment)</label>
    <div class="col-sm-4 position-relative">
        <input type="text" class="form-control" name="comment" placeholder="配置项说明" value="{{$item?$item->comment:''}}" maxlength="255" required="true"/>
    </div>
</div>
<div class="row my-3">
    <label class="col-sm-2 col-form-label text-end bg-light"><b class="text-danger">*</b> 状态</label>
    <div class="col-sm-4 position-relative">
        @foreach(Laravel\Crbac\Models\Power\Config::$_STATUS as $key=>$val)
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="status" value="{{$key}}" required="true" id="status-{{$key}}"@if($item && $item->status == $key) checked="checked"@endif/>
            <label class="form-check-label" for="status-{{$key}}">{{$val}}</label>
        </div>
        @endforeach
    </div>
</div>
<script>
(function() {
    function updateValueType() {
        var type =$('input[name="type"]:checked').val();
        $('#value-container .value-control').addClass('d-none').find('input,textarea').prop('disabled', true);
        $('#value-' + type).removeClass('d-none').find('input,textarea').prop('disabled', false);
    }
    updateValueType();
    $('input[name="type"]').on('change', updateValueType);
})();
</script>
@stop