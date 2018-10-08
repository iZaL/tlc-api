@extends('layouts.app')

@section('breadcrumb')
    @component('partials._subheader-default',['title' => isset($title) ? $title : '','breadcrumbs' => isset($breadcrumbs) ? $breadcrumbs : []])
    @endcomponent
@endsection

@section('content')


@endsection