@extends('layout.template')

@section('title_page', $page_title)

@section('content')
<div class="col-lg-12" id="app">
    <h1>cargar usuarios</h1>
    <form method="POST" action="{{ route('migrarExcel') }}" enctype="multipart/form-data">
        @csrf
        <input type="file" name="excel_file" required>
        <button type="submit">Importar Excel usuarios</button>
    </form>

    <br><br><br>

    <h1>cargar certificados</h1>
    <form method="POST" action="{{ route('migrarExcelCertificados') }}" enctype="multipart/form-data">
        @csrf
        <input type="file" name="excel_file" required>
        <button type="submit">Importar Excel Certificados</button>
    </form>


    <br><br><br>

    <h1>cargar acompañamientos</h1>
    <form method="POST" action="{{ route('migrarExcelAcompañamiento') }}" enctype="multipart/form-data">
        @csrf
        <input type="file" name="excel_file" required>
        <button type="submit">Importar Excel ACOMPAÑAMIENTO</button>
    </form>

    <br><br><br>

    <h1>cargar capacitaciones asistidas</h1>
    <form method="POST" action="{{ route('migrarCapacitacionesAsistidas') }}" enctype="multipart/form-data">
        @csrf
        <div class="col-md-6">
            <label for="id_centro">Ingresa id de centro de costo a migrar</label>
            <input type="text" name="id_centro" value="{{ $centro->id }}" required>
        </div>
        <div class="col-md-6">
            <label for="id_centro">Ingresa fecha para migrar capacitaciones presenciales despues de esa fecha: </label>
            <input type="date" name="fecha" required>
        </div>
        <button type="submit">Importar CAPACITACIONES ASISTIDAS</button>
    </form>
</div>
@endsection
