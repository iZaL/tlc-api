<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Trans Logistic Company">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'TLC') }}</title>
    @section('styles')
        @include('partials.styles')
    @show
</head>

<body class="m-page--fluid m--skin- m-content--skin-light2 m-header--fixed m-header--fixed-mobile m-aside-left--enabled m-aside-left--skin-dark m-aside-left--fixed m-aside-left--offcanvas m-footer--push m-aside--offcanvas-default">
<div class="m-grid m-grid--hor m-grid--root m-page">

    @include('partials._header-base')

    <div class="m-grid__item m-grid__item--fluid m-grid m-grid--ver-desktop m-grid--desktop m-body">

        @include('partials._aside-left')

        <div class="m-grid__item m-grid__item--fluid m-wrapper">

            @yield('breadcrumb')

            <div class="m-content">
                @yield('content')
            </div>
        </div>
    </div>

    @include('partials._footer-default')

</div>

@include('partials._layout-quick-sidebar')

@include('partials._layout-scroll-top')

@section('scripts')
    @include('partials.scripts')
@show

</body>
</html>