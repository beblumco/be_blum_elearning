@extends('layout.template')

@section('title_page', $page_title)

@section('content')
<div class="col-lg-12" id="app">
    <catalogue-page-component has_ajust_pres="{{ auth()->user()->can_ajust_pres }}" can_to_approve="{{ auth()->user()->can_to_approve }}"/>
</div>
    
@endsection
