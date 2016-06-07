<?php

use XiHuan\Crbac\Models\Power\Menu;
use XiHuan\Crbac\Models\Power\MenuLevel;
use XiHuan\Crbac\Models\Power\MenuGroup;

$menuGroups = MenuGroup::all();
?>
@extends('public.edit')
@section('body')
<div class="field-group clear">
    <label class="field-label"><span class="redD">*</span>名称 :</label>
    <div class="field-value">
        <input type="text" name="name" placeholder="唯一的菜单名称" value="{{$item?$item->name:''}}" required="true" minlength="3" maxlength="30" remote="{{validate_url($item?$item:$modelClass,'name')}}"/>
    </div>
</div>
<div class="field-group clear">
    <label class="field-label"><span class="redD">*</span>URL地址 :</label>
    <div class="field-value">
        <input type="text" name="url" id="router-url" placeholder="唯一URL地址，如果权限项有请求地址，可以快捷生成权限码" value="{{$item?$item->url:(isset($route)?$route->url:'')}}" remote="{{validate_url($item?$item:$modelClass,'url')}}"/>
    </div>
    <div class="field-value redD">
        注意：该地址为菜单所需链接地址，所有菜单均定义为GET请求，POST请求只能添加到权限项。
    </div>
</div>
<div class="field-group clear" id="power-item-div">
    <fieldset>
        <legend>权限项相关</legend>
        <div class="field-group clear">
            <label class="field-label">权限码 :</label>
            <div class="field-value">
                <input type="text" class="width450" placeholder="所在权限码" id="power-code" name="code" value="{{$item && $item->item?$item->item->code:''}}" readonly="readonly"/>
                <input type="button" id="power-item" value="生成权限码"/>
            </div>
            <div class="field-value redD">
                注意：如果请求需要配置权限管理，需要在此生成权限项。<br/>权限码必须为controller@action的结构保存，否则权限项无效，该权限项禁止手动修改。
            </div>
        </div>
        <div class="field-group clear" id="power-item-data">
            @include('power.item.edit_relate')
        </div>
    </fieldset>
</div>
<div class="field-group clear" id="menu-group-lists">
    <label class="field-label">所在菜单组 :</label>
    @if($item && $item->groups)
    @foreach($item->groups as $key=>$_group)
    <div class="field-value menu-group-lists">
        <select class="menu-group-select">
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
        <select class="menu-level-select" data-groupid="{{$_group->getKey()}}">
            <option value="">--这级显示--</option>
            @foreach($menus as $menu)
            <option value="{{$menu->level_id}}"<?php if ($menu->level_id == $select_id) { ?> selected="selected"<?php } ?>>{{$menu->name}}</option>
            @endforeach
        </select>
        @endforeach
        <?php // $item->groups;  ?>
        <input type="button" class="remove-menu-group" value="删除"/>
    </div>
    @endforeach
    @endif
    <div class="field-value menu-group-lists">
        <select class="menu-group-select">
            <option value="">--请选择组--</option>
            @foreach($menuGroups as $group)
            <option value="{{$group->getKey()}}">{{$group->name}}</option>
            @endforeach
        </select>
        <input type="button" class="remove-menu-group" value="删除"/>
    </div>
    <div class="field-value">
        <input type="button" id="add-menu-group" value="追加菜单组"/>
    </div>
    <div class="field-value redD">
        注意：相同菜单结构组会合并为一个。
    </div>
</div>
<div class="field-group clear">
    <label class="field-label"><span class="redD">*</span>备注说明 :</label>
    <div class="field-value">
        <textarea name="comment" placeholder="备注说明用途，作用，以便后续快速理解">{{$item?$item->comment:''}}</textarea>
    </div>
</div>
<div class="form-button">
    <input type="button" class="save ajax-submit-data" value="{{$item?'编辑':'创建'}}"/>
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
            url: '/power/group/menu/select/level/' + group_id + '.html',
            data: data,
            dataType: 'json',
            success: function (json) {
                if (json.status === 'success') {
                    var select = $('<select class="menu-level-select" data-groupid="' + group_id + '"><option value="">--这级显示--</option></select>');
                    //添加选项
                    $.each(json.message.options, function (k, v) {
                        select.append('<option value="' + v.id + '">' + v.name + '</option>');
                    });
                    if (json.message.options.length === 0) {
                        select.find('option').text('--无下级菜单--');
                    }
                    select.change(function () {
                        getMenuLevel($(this));
                    });
                    current.after(select);
                    return false;
                }
            }
        });
    }
    //菜单组选择处理
    $('select.menu-group-select,select.menu-level-select').change(function () {
        getMenuLevel($(this));
    });
    $(':button.remove-menu-group').click(function () {
        if ($(this).parents('div.field-group clear').find('div.menu-group-lists').size() > 1) {//最少保留一组
            $(this).parent().remove();
        } else {
            alert('最少保存一项，允许不选择！');
        }
    });
    $('#add-menu-group').click(function () {
        var select = $(this).parent().prev('div.menu-group-lists').find('select.menu-group-select,:button.remove-menu-group').clone(true);
        var div = $('<div class="field-value menu-group-lists"></div>').append(select);
        $(this).parent().before(div);
    });
    $(':button.ajax-submit-data').click(function () {
        //整理菜单name值
        $('#menu-group-lists div.menu-group-lists').each(function (index) {
            $(this).find('select').each(function (key) {
                this.name = 'group[' + index + '][' + key + ']';
            });
        });
    });
    $('#power-item').click(function () {
        getRouteUses('GET', function () {
            $('#power-item-data').show().find(':disabled').each(function () {
                this.disabled = false;
            });
        });
    });
<?php if (!$item || !$item->item) { ?>
        $('#power-item-data').hide().find(':input').each(function () {
            this.disabled = true;
        });
<?php } ?>
</script>
@stop
