@php
    if(empty($menu)){
        $menu = '1';
    }

    if(empty($idTraining)){
        $idTraining = '';
    }
@endphp

@extends('layout.template')

@section('title_page', 'Capacitaciones')

@section('content')
<div class="col-lg-12" id="app">
    <page-training-component menu="{{ $menu }}" idtraining="{{ $idTraining }}"></page-training-component>
</div>
@endsection
