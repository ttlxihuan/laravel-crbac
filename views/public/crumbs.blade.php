<!--面包屑-->
<div class="content-header content-header-compact">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <h4 class="content-header-title">{{$title ?? $description ??''}}</h4>
            <ol class="breadcrumb m-0">
                @section('crumbs')
                @foreach($crumbs ?? [] as $key => $menu)
                <li class="breadcrumb-item"><a href="{{$menu['url']}}">{{$menu['name']}}</a></li>
                @endforeach
                @if(isset($title))
                <li class="breadcrumb-item active">{{$title}}</li>
                @endif
                @show
            </ol>
        </div>
        <div class="content-header-tools">
            @yield('nimble')
        </div>
    </div>
</div>
