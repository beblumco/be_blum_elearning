{{-- Extends layout --}}
@extends('layout.template')

{{-- Content --}}
@section('content')
<div id="app">
   <div class="container-fluid">
     <div class="col-lg-12" style="min-height: 300px;">
        <div class="card">
            <div class="card-body" style="padding-top: 10px;padding-bottom: 10px;">
               <span class="d-flex justify-content-center" style="font-size: 20px;color:#3b3b3b;font-weight: 600;">Capacitaciones recibidas por E-Learning</span>
            </div>
        </div>

        <div class="row">
            <card-indicator-component title="Horas de entrenamiento" quantity="{{ $minutosElearningTotales }}" icon="dev_training_ent_orange"></card-indicator-component>
            <card-indicator-component title="Certificados generados" quantity="{{ $certificadosElearnig }}" icon="dev_certificados_orange"></card-indicator-component>
            <card-indicator-component title="Puntos obtenidos" quantity="{{ $puntosElearningTotales }}" icon="dev_points_orange"></card-indicator-component>
        </div>

     </div>
   </div>
</div>

@endsection
