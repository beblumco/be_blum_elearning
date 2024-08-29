@extends('layout.template')

@section('title_page', $page_title )

@section('content')
    <div class="container-fluid" id="app">
        <drive-index-component user="{{ auth()->user()->id }}" type="2"></drive-index-component>
    </div>
@endsection
