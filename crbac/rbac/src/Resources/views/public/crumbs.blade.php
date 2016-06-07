<?php
//面包屑
?>
<div class="crumbs clear">
    @section('crumbs')
    @if(isset($crumbs) && count($crumbs))
    <a href="{{$crumbs[0]['url']}}">{{$crumbs[0]['name']}}</a>
    @foreach(array_slice($crumbs,1) as $key=>$menu)
    <a href="{{$menu['url']}}"<?php if ($key == count($crumbs) - 2) { ?> class="current"<?php } ?>>{{$menu['name']}}</a>
    @endforeach
    @endif
    @if(isset($title))
    <a href="javascript:void(0)">{{$title}}</a>
    @endif
    @show
    <div class="fr">
    @section('nimble')@show
    </div>
</div>
