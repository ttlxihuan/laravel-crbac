@extends('public.lists')
@section('search')
<form>
    <div class="container mx-0 px-0">
        <div class="row justify-content-start navbar-expand">
            <div class="col mb-2">
                <div class="input-group">
                    <label class="input-group-text">操作类型</label>
                    <select class="form-select" name="type">
                        <option value="">全部</option>
                        @foreach(Laravel\Crbac\Models\Power\UpdateLog::$_TYPES as $key=>$val)
                        <option value="{{$key}}"<?php if (request('type') == $key) { ?> selected="selected"<?php } ?>>{{$val}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col mb-2">
                <button type="submit" class="btn btn-primary">查询</button>
            </div>
        </div>
    </div>
</form>
@stop
@section('lists')
<table class="table table-sm table-striped table-hover table-bordered">
    <thead>
        <tr class="table-secondary">
            <th>序号</th>
            <th>模型名</th>
            <th>操作类型</th>
            <th>操作人</th>
            <th>操作IP</th>
            <th width="310">操作地址</th>
            <th width="310">终端标识</th>
            <th width="170" class="{{$toOrder('created',false)}}" onclick="location.href ='{{$toOrder('created')}}'">操作时间</th>
            <th width="180">操作</th>
        </tr>
    </thead>
    <tbody>
        @foreach($lists as $index=>$item)
        <tr>
            <td>{{$index+1}}</td>
            <td>{{$item->model}}</td>
            <td>{{$item->typeName()}}</td>
            <td>{{$item->admin ? $item->admin->realname : '-'}}</td>
            <td>{{$item->ip}}</td>
            <td>{{$item->url}}</td>
            <td>{{$item->user_agent}}</td>
            <td>{{$item->created_at}}</td>
            <td>
                <script type="text/javascript">
                    var update_old_{{$index}} = {!!$item->old_data!!};
                    var update_new_{{$index}} = {!!$item->new_data!!};
                </script>
                <a href="javascript:void(0);" data-index="{{$index}}" title="查看变动数据">查看数据</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
<script type="text/javascript">
    $(function () {
        $('a[data-index]').click(function () {
            var index = this.getAttribute('data-index'), body = '<div class="row">';
            body += '<div class="col"><h4>原数据</h4><pre>' + JSON.stringify(window['update_old_' + index], null, 4) + '</pre></div>';
            body += '<div class="col"><h4>新数据</h4><pre>' + JSON.stringify(window['update_new_' + index], null, 4) + '</pre></div>';
            body += '</div>';
            $.popup.box('数据信息', body, {close: !0}).size('xl');
        });
    });
</script>
@stop
