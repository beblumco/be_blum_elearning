@extends('layout.template_page_public')

@section('content')
    <div id="app">
        <detalle-auditoria-component :data_auditoria="{{ json_encode($data) }}"></detalle-auditoria-component>
    </div>
@endsection
