<!--面包屑-->
<div class="container-fluid clearfix mt-1">
    <ol class="breadcrumb float-start my-0">
        @section('crumbs')
        @foreach($crumbs??[] as $key=>$menu)
        <li class="breadcrumb-item"><a href="{{$menu['url']}}">{{$menu['name']}}</a></li>
        @endforeach
        @if(isset($title))
        <li class="breadcrumb-item"><a href="javascript:void(0)">{{$title}}</a></li>
        @endif
        @show
    </ol>
    <ul class="navbar-nav navbar-expand float-end">
        <li class="nav-item">
            @section('nimble')@show
        </li>
    </ul>
</div>
<hr class="my-2"/>
