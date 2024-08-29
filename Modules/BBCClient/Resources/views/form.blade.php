@extends('layout.fullPage')

@section('title_page', 'BBC')

@section('content')
<div class="container-fluid mt-5">
    
    <div class="col-lg-12 d-flex justify-content-center flex-wrap mr-5 mt-5">

        <div class="card col-lg-8 dev-card mt-5">
            <div class="card-header col-lg-12 d-flex justify-content-center">
                <h4 class="card-title col-lg-8 text-center dev-font-green">Déjanos tus <span class="dev-bold">inquietudes o novedades sobre el proceso de limpieza y desinfeccion!</span></h4>
            </div>
            <div class="card-body">
                <div class="basic-form">
                    <form>
                        <div class="form-group">
                            <input type="text" class="form-control input-default " id="dev_name" placeholder="Nombre completo">
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control input-rounded" id="dev_pdv" placeholder="Punto de venta">
                        </div>
                        <div class="form-group">
                            <textarea class="form-control" rows="5" id="dev_commit" placeholder="Cuéntanos tus necesidades, dudas o inquietudes..."></textarea>
                        </div>
                        <div class="col-lg-12">
                            <button class="btn btn-primary col-lg-12 dev-btn-second dev_btn_send">Envíar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
