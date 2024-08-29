@extends('layout.fullPage')

@section('title_page', '')

@section('content')
<div class="col-lg-12" id="app">
    <page-modal-get-share-training nom_organizacion="{{$organizacion}}" id_training="{{$id_training}}" main_account_id="{{$main_account_id}}" email_administrador="{{$email}}"></page-modal-get-share-training>
</div>
@endsection

