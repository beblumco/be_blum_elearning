@extends('layout.template')

@section('title_page', $page_title)

@section('content')
<div class="col-lg-12" id="app">
    <report-training-component></report-training-component>
</div>
@endsection