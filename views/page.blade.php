<?php
$size = 10;
?>
<ul class="pagination justify-content-end">
    <li class="page-item mx-1">
        <div class="input-group mb-3">
            <span class="input-group-text">共{{$paginator->total()}}记录</span>
            <span class="input-group-text">每页{{$paginator->perPage()}}行</span>
            <span class="input-group-text">共{{$paginator->lastPage()}}页</span>
        </div>
    </li>
    @if($paginator->currentPage()>1)
    <li class="page-item"><a class="page-link" href="{{$paginator->url(1)}}">{{lang('pagination.first')}}</a></li>
    <li class="page-item"><a class="page-link" href="{{$paginator->previousPageUrl()}}">{{lang('pagination.previous')}}</a></li>
    @endif
    @for($pageNum=max(round($paginator->currentPage()-$size/2),1),$length=0;$length<$size && $pageNum<=$paginator->lastPage();$length++,$pageNum++)
    @if($paginator->currentPage()==$pageNum)
    <li class="page-item disabled"><a class="page-link" href="javascript:void(0);">{{$pageNum}}</a></li>
    @else
    <li class="page-item"><a class="page-link" href="{{$paginator->url($pageNum)}}">{{$pageNum}}</a></li>
    @endif
    @endfor
    @if($paginator->lastPage() > $paginator->currentPage())
    <li class="page-item"><a class="page-link" href="{{$paginator->nextPageUrl()}}">{{lang('pagination.next')}}</a></li>
    <li class="page-item"><a class="page-link" href="{{$paginator->url($paginator->lastPage())}}">{{lang('pagination.last')}}</a></li>
    @endif
    <li class="page-item mx-1">
        <div class="input-group mb-3">
            <input type="text" class="form-control" placeholder="展示页号" value="{{$paginator->currentPage()}}" size="3" onchange="this.value = Math.max(Math.min(parseInt(this.value.replace(/\D+/g, '')),'{{$paginator->lastPage()}}'), 1)"/>
            <button class="btn btn-outline-secondary" type="button" onclick="var text = this.previousSibling; while (text.nodeType != 1){text = text.previousSibling; }; text.onchange(); text.value != '{{$paginator->currentPage()}}' && (location.href ='{{$paginator->url(1)}}'.replace(/page=\d+/, 'page=' + text.value));">转到</button>
        </div>
    </li>
</ul>