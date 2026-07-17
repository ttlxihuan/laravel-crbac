<?php

use Laravel\Crbac\Models\Power\Menu;
use Laravel\Crbac\Models\Power\MenuLevel;
use Laravel\Crbac\Models\Power\MenuGroup;

$menuGroups = MenuGroup::all();
?>
@extends('public.edit')
@section('body')
<div class="row my-3">
    <label class="col-sm-2 col-form-label text-end bg-light"><b class="text-danger">*</b> 名称</label>
    <div class="col-sm-4 position-relative">
        <input type="text" class="form-control" name="name" placeholder="唯一的菜单名称" value="{{$item?$item->name:''}}" required="true" minlength="3" maxlength="30" remote="{{validate_url($item?$item:$modelClass,'name')}}"/>
    </div>
</div>
<div class="row my-3">
    <label class="col-sm-2 col-form-label text-end bg-light"> 菜单图标</label>
    <div class="col-sm-6">
        <div class="input-group">
            <span class="input-group-text"><i class="fas {{ $item ? $item->icon : 'fa-circle' }}" id="icon-preview"></i></span>
            <input type="text" class="form-control" name="icon" id="icon-input" placeholder="图标class名，如 fa-home" value="{{ $item ? $item->icon : 'fa-circle' }}" readonly="readonly"/>
            <button class="btn btn-outline-secondary" type="button" id="icon-picker-toggle">选择图标</button>
        </div>
        <div id="icon-picker-panel" style="display:none; margin-top:10px; padding:10px; border:1px solid #e5e7eb; border-radius:6px; background:#fff; max-height:300px; overflow-y:auto;">
            <div class="mb-2">
                <input type="text" class="form-control form-control-sm" id="icon-search" placeholder="搜索图标名..."/>
            </div>
            <div id="icon-grid" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(42px,1fr)); gap:6px;">
            </div>
        </div>
    </div>
</div>
<div class="row my-3">
    <label class="col-sm-2 col-form-label text-end bg-light"><b class="text-danger">*</b> URL地址</label>
    <div class="col-sm-4">
        <div class="position-relative">
            <input type="text" class="form-control" name="url" id="router-url" placeholder="唯一URL地址，如果权限项有请求地址，可以快捷生成权限码" value="{{$item?$item->url:(isset($route)?$route->url:'')}}"/>
        </div>
        <p class="text-danger">注意：该地址为菜单所需链接地址，所有菜单均定义为GET请求，POST请求只能添加到权限项。</p>
    </div>
</div>
<div class="container-md">
    <div class="card">
        <div class="card-header">
            权限项相关
        </div>
        <div class="card-body" id="power-item-form">
            <div class="row my-3">
                <label class="col-sm-2 col-form-label text-end bg-light"> 权限码</label>
                <div class="col-sm-7">
                    <div class="input-group position-relative">
                        <input type="text" class="form-control" placeholder="所在权限码" id="power-code" name="code" value="{{$item && $item->item?$item->item->code:''}}" readonly="readonly"/>
                        <button class="btn btn-outline-success" type="button" id="power-item">生成权限码</button>
                        <button class="btn btn-outline-secondary" type="button" id="power-clean">不要权限</button>
                    </div>
                    <p class="text-danger">注意：如果请求需要配置权限管理，需要在此生成权限项。<br/>权限码必须为controller@action的结构保存，否则权限项无效，该权限项禁止手动修改。</p>
                </div>
            </div>
            @include('power.item.edit_relate')
        </div>
    </div>
</div>
<div class="row my-3" id="menu-group-lists">
    <label class="col-sm-2 col-form-label text-end bg-light"> 所在菜单组</label>
    <div class="col-sm-8">
        @if($item && $item->groups)
        @foreach($item->groups as $key=>$_group)
        <div class="input-group mb-3">
            <select class="form-select">
                <option value="">--请选择组--</option>
                @foreach($menuGroups as $group)
                <option value="{{$group->getKey()}}"<?php if ($_group->getKey() == $group->getKey()) { ?> selected="selected"<?php } ?>>{{$group->name}}</option>
                @endforeach
            </select>
            <?php
            $parent_id = $_group->parent_id;
            $level_id = 0; //$_group->level_id;
            $levels = [];
            do {
                $menus = Menu::level($_group->getKey(), $parent_id); //取当前级
                $level = $parent_id ? MenuLevel::find($parent_id) : 0;
                $levels[$level_id] = $menus;
                $parent_id = $level ? $level->parent_id : 0;
                $level_id = $level ? $level->getKey() : 0;
            } while ($level);
            ?>
            <!--每级菜单显示-->
            @foreach(array_reverse($levels,true) as $select_id=>$menus)
            <select class="menu-level-select form-select" data-groupid="{{$_group->getKey()}}">
                <option value="">--这级显示--</option>
                @foreach($menus as $menu)
                <option value="{{$menu->level_id}}"<?php if ($menu->level_id == $select_id) { ?> selected="selected"<?php } ?>>{{$menu->name}}</option>
                @endforeach
            </select>
            @endforeach
            <button type="button" class="btn btn-danger remove-menu-group">删除</button>
        </div>
        @endforeach
        @endif
        <div class="input-group mb-3">
            <select class="form-select">
                <option value="">--请选择组--</option>
                @foreach($menuGroups as $group)
                <option value="{{$group->getKey()}}">{{$group->name}}</option>
                @endforeach
            </select>
            <button type="button" class="btn btn-danger remove-menu-group">删除</button>
        </div>
        <div class="mb-3">
            <button type="button" class="btn btn-info" id="add-menu-group">追加菜单组</button>
            <p class="text-danger mt-3">注意：相同菜单结构组会合并为一个，构变化会删除结构不相同的下级菜单。</p>
        </div>
    </div>
</div>
<div class="row my-3">
    <label class="col-sm-2 col-form-label text-end bg-light"><b class="text-danger">*</b> 备注说明</label>
    <div class="col-sm-4 position-relative">
        <textarea class="form-control" name="comment" placeholder="备注详细说明用途" required="true">{{$item?$item->comment:''}}</textarea>
    </div>
</div>
<script type="text/javascript">
    function getMenuLevel(current) {
        current.nextAll('select.menu-level-select').remove();
        if (!current.val()) {
            return false;
        }
        var group_id = current.data('groupid'),
                data = {};
        if (group_id === undefined) {
            group_id = current.val();
        } else {
            var data = {parent_id: current.val()};
        }
        $._ajax({
            url: "{{crbac_route('power.menu-group.level-option')}}/" + group_id,
            data: data,
            dataType: 'json',
            success: function (json) {
                if (json.status === 'success') {
                    var select = $('<select class="menu-level-select form-select" data-groupid="' + group_id + '"><option value="">--这级显示--</option></select>');
                    //添加选项
                    $.each(json.message.options, function (k, v) {
                        select.append('<option value="' + v.id + '">' + v.name + '</option>');
                    });
                    if (json.message.options.length === 0) {
                        select.find('option').text('--无下级菜单--');
                    }
                    current.after(select);
                    updateSelectName();
                    return false;
                }
            }
        });
    }
    function updateSelectName() {
        //整理菜单name值
        $('#menu-group-lists div.input-group').each(function (index) {
            $(this).find('select').each(function (key) {
                this.name = 'group[' + index + '][' + key + ']';
            });
        });
    }
    $(function () {
        updateSelectName();
    });
    //菜单组选择处理
    $(document).on('change focus', 'select.form-select,select.menu-level-select', function (event) {
        if (event.type === 'change' || (this.value > 0 && $(this).nextAll('select.menu-level-select').length <= 0)) {
            getMenuLevel($(this));
        }
    });
    $(':button.remove-menu-group').click(function () {
        if ($('#menu-group-lists div.input-group').size() > 1) {//最少保留一组
            $(this).parent().remove();
        } else {
            $.popup.alert('最少保存一项，允许不选择！');
        }
    });
    $('#add-menu-group').click(function () {
        var select = $(this).parent().prev('div.input-group').find('select.form-select,:button.remove-menu-group').clone(true);
        var div = $('<div class="input-group mb-3"></div>').append(select);
        $(this).parent().before(div);
    });
    $('#power-item').click(function () {
        $('#power-item-form :input').attr('disabled', false);
        getRouteUses('GET', function () {
            $('#power-item-data').show().find(':disabled').each(function () {
                this.disabled = false;
            });
        });
    });
    $('#power-clean').click(function () {
        $('#power-item-form :input:not(#power-item)').attr('disabled', true);
    });
<?php if (!$item || !$item->item) { ?>
        $('#power-item-data').hide().find(':input').each(function () {
            this.disabled = true;
        });
<?php } ?>
    // 图标选择器
    var iconList = [];
    if(document.styleSheets){
        for (var i = 0; i < document.styleSheets.length; i++) {
            var styleSheet = document.styleSheets[i];
            if (/fontawesome/.test(styleSheet.href) && styleSheet.cssRules.length > 0) {
                for (var j = 0; j < styleSheet.cssRules.length; j++) {
                    var cssRule = styleSheet.cssRules[j];
                    if (/^\.fa-[^:]+::before/.test(cssRule.selectorText)) {
                        iconList.push(cssRule.selectorText.match(/\.(fa-[^:]+)::before/)[1]);
                    }
                }
            }
        }
    }
    function renderIcons(filter) {
        var html = '';
        $.each(iconList, function(i, icon) {
            if (filter && icon.indexOf(filter) === -1) return;
            var active = ($('#icon-input').val() === icon) ? 'background:#d1fae5;border-color:#10b981;' : '';
            html += '<div class="icon-item text-center" data-icon="' + icon + '" style="cursor:pointer;padding:8px;border:1px solid #e5e7eb;border-radius:4px;' + active + '" title="' + icon + '"><i class="fas ' + icon + '"></i></div>';
        });
        $('#icon-grid').html(html);
    }
    $('#icon-picker-toggle').click(function() {
        var panel = $('#icon-picker-panel');
        if (panel.is(':visible')) {
            panel.slideUp(150);
        } else {
            renderIcons('');
            panel.slideDown(150);
        }
    });
    $('#icon-search').on('input', function() {
        renderIcons($(this).val().toLowerCase());
    });
    $(document).on('click', '.icon-item', function() {
        var icon = $(this).data('icon');
        $('#icon-input').val(icon);
        $('#icon-preview').attr('class', 'fas ' + icon);
        renderIcons($('#icon-search').val());
    });
</script>
@stop
