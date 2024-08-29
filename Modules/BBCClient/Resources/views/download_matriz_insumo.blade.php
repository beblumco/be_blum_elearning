@extends('layout.fullPage')

@section('title_page', 'BBC')

@section('content')
<div class="container-fluid mt-5">
    
    <div class="col-lg-12 d-flex justify-content-end flex-wrap mr-5">
        
        <div class="card col-lg-5">
            
        </div>

        <div class="card col-lg-4 dev-cursor-pointer m-2 dev-card" id="dev_mat_ins_coc">
            <div class="card-body p-0 dev-card-body">
                <span class="btn subtitle dev-text-color-primary"><span class="dev-bold dev-text-color-primary">Descargar</span> Matriz de Insumo (Cocina)</span class="text-align-center">
            </div>
        </div>

        <div class="card col-lg-4 dev-cursor-pointer m-2 dev-card" id="dev_mat_ins_zone">
            <div class="card-body p-0 dev-card-body">
                <span class="btn subtitle dev-text-color-primary"><span class="dev-bold dev-text-color-primary">Descargar</span> Matriz de Insumo (Zonas comunes)</span class="text-align-center">
            </div>
        </div>
    </div>
</div>
@endsection
