@extends('layout.template')

@section('title_page', $page_title)

@section('content')
<div class="col-lg-12" id="app">
    <shopping-car-page-component  has_approve="{{ auth()->user()->can_to_approve }}"/>
</div>
    
@endsection
