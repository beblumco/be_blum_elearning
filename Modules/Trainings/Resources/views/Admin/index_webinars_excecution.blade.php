@extends('layout.template')

@section('title_page', 'Capacitaciones')

@section('content')
<div class="col-lg-12" id="app">
    <page-webinars-component id_grupo="{{ auth()->user()->id_grupo }}"></page-webinars-component>
</div>
@endsection
