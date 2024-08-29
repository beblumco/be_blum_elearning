@extends('layout.fullPage')

@section('title_page', 'BBC')

@section('content')
<div class="container-fluid mt-5">
    
    <div class="col-lg-12 d-flex justify-content-center mt-5">
        <div class="card col-lg-4 dev-cursor-pointer m-2" link="{{ route('index_bbc_download') }}" id="dev_video_BBC">
            <div class="card-body p-0 dev-card-body">
                <span class="btn btn-large">Video <span class="dev-bold dev-text-color-primary">programa de limpieza y desinfección</span></span class="text-align-center">
            </div>
        </div>
    </div>

    <div class="col-lg-12 d-flex justify-content-center">
        <div class="card col-lg-4 dev-cursor-pointer m-2" link="{{ route('index_matriz_insumo') }}" id="dev_download_matriz_BBC">
            <div class="card-body p-0 dev-card-body">
                <span class="btn btn-large">Descarga <span class="dev-bold dev-text-color-primary">tu matriz de insumo</span></span class="text-align-center">
            </div>
        </div>
    </div>

    <div class="col-lg-12 d-flex justify-content-center">
        <div class="card col-lg-4 dev-cursor-pointer m-2" link="{{ route('index_form') }}" id="dev_download_inquiet_BBC">
            <div class="card-body p-0 dev-card-body">
                <span class="btn subtitle">Novedades <span class="dev-bold dev-text-color-primary">e inquietudes</span></span class="text-align-center">
            </div>
        </div>
    </div>
</div>
@endsection
