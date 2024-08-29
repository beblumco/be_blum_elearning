@extends('layout.fullPage')

@section('title', 'Asistencia')

@section('css')
    <link href="{{ assets_version('assets/css/main.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ assets_version('assets/register-asist/main.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ assets_version('assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}">
    <link rel="stylesheet" href="{{ assets_version('assets/vendor/select2/css/select2.min.css') }}">
@endsection

@section('content')
    <div class="header dev_header">
        <nav class="nav-header">
            <a href="/dashboard" class="brand-logo">
                <img class="logo-abbr" src="{{ asset('img/logo_savak.png') }}" alt="">
                <img class="logo-compact" src="{{ asset('img/logo_savak.png') }}" alt="">
                <h1 class="brand-title"><b>Entrenamiento</b></h1>
            </a>
        </nav>
    </div>

    <div class="page-titles col-lg-12 dev_titles m-0">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">{{ $training_asistida->nom_cap }}</a></li>
            {{--  <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ $training_asistida->nom_mod}}</a></li>  --}}
        </ol>
    </div>

    <div class="col-lg-6" id="app">
        <register-asist-asistida-component :id_training="{{ $training_asistida }}" ></register-asist-asistida-component>
    </div>
@endsection
