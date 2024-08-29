@extends('layout.template')

@section('title_page', 'Catálogo')

@section('content')
    <div class="container-fluid">
        <div class="page-titles">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('catalogo_index') }}">Catálogo</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Detalle</a></li>
            </ol>
        </div>

    <div class="col-lg-12" id="app">
        <product-detail-component id_product="{{ $id_product }}" opc="{{ $opc }}"/>
    </div>
@endsection
