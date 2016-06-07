<?php
/**
 * @文件名 page
 * @字符集 UTF-8
 * @创建人 奚欢
 * @版权所有 版权属创建人所有，任何人要复制、翻译、改编或演出等均需要得到版权所有人的许可
 * @创建时间 2016-5-20 23:38:09
 * @版本 1.0
 * @说明 分页显示
 */
$size = 10;
?>
<div class="paginator">
    <span>共<b>{{$paginator->total()}}</b>记录/行数<b>{{$paginator->perPage()}}</b>/分<b>{{$paginator->lastPage()}}</b>页</span>
    @if($paginator->currentPage()>1)
    <a href="{{$paginator->url(1)}}">{{lang('pagination.first')}}</a>
    <a href="{{$paginator->previousPageUrl()}}">{{lang('pagination.previous')}}</a>
    @endif
    @for($pageNum=max($paginator->currentPage()-$size/2,1),$length=0;$length<$size && $pageNum<=$paginator->lastPage();$length++,$pageNum++)
    @if($paginator->currentPage()==$pageNum)
    <a href="javascript:void(0);" class="current">{{$pageNum}}</a>
    @else
    <a href="{{$paginator->url($pageNum)}}">{{$pageNum}}</a>
    @endif
    @endfor
    @if($paginator->lastPage() > $paginator->currentPage())
    <a href="{{$paginator->nextPageUrl()}}">{{lang('pagination.next')}}</a>
    <a href="{{$paginator->url($paginator->lastPage())}}">{{lang('pagination.last')}}</a>
    @endif
    <input type="text" value="{{$paginator->currentPage()}}" size="3" onchange="this.value = Math.max(Math.min(parseInt(this.value.replace(/\D+/g, '')),'{{$paginator->lastPage()}}'), 1)"/>
    <input type="button" value="转到" onclick="var text = this.previousSibling; while (text.nodeType != 1){text = text.previousSibling; }; text.onchange(); text.value != '{{$paginator->currentPage()}}' && (location.href ='{{$paginator->url(1)}}'.replace(/page=\d+/, 'page=' + text.value));"/>
</div>
