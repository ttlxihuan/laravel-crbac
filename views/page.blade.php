<?php
$size = 10;
?>
<div class="d-flex flex-wrap justify-content-between align-items-center">
    <div class="d-flex align-items-center mb-2">
        <small class="text-muted me-3">共{{$paginator->total()}}记录</small>
        <small class="text-muted me-3">每页{{$paginator->perPage()}}行</small>
        <small class="text-muted">共{{$paginator->lastPage()}}页</small>
    </div>
    <ul class="pagination pagination-sm justify-content-end mb-0">
        @if($paginator->currentPage()>1)
        <li class="page-item"><a class="page-link" href="{{$paginator->url(1)}}">{{lang('pagination.first')}}</a></li>
        <li class="page-item"><a class="page-link" href="{{$paginator->previousPageUrl()}}">{{lang('pagination.previous')}}</a></li>
        @endif
        @for($pageNum=max(round($paginator->currentPage()-$size/2),1),$length=0;$length<$size && $pageNum<=$paginator->lastPage();$length++,$pageNum++)
        @if($paginator->currentPage()==$pageNum)
        <li class="page-item active"><a class="page-link" href="javascript:void(0);">{{$pageNum}}</a></li>
        @else
        <li class="page-item"><a class="page-link" href="{{$paginator->url($pageNum)}}">{{$pageNum}}</a></li>
        @endif
        @endfor
        @if($paginator->lastPage() > $paginator->currentPage())
        <li class="page-item"><a class="page-link" href="{{$paginator->nextPageUrl()}}">{{lang('pagination.next')}}</a></li>
        <li class="page-item"><a class="page-link" href="{{$paginator->url($paginator->lastPage())}}">{{lang('pagination.last')}}</a></li>
        @endif
    </ul>
    <div class="input-group input-group-sm mb-2" style="max-width: 160px;">
        <input type="text" class="form-control" placeholder="页号" value="{{$paginator->currentPage()}}" size="3" onchange="this.value = Math.max(Math.min(parseInt(this.value.replace(/\D+/g, '')),'{{$paginator->lastPage()}}'), 1)"/>
        <button class="btn btn-outline-secondary" type="button" onclick="var text = this.previousSibling; while (text.nodeType != 1){text = text.previousSibling; }; text.onchange(); text.value != '{{$paginator->currentPage()}}' && (location.href ='{{$paginator->url(1)}}'.replace(/page=\d+/, 'page=' + text.value));">转到</button>
    </div>
</div>
