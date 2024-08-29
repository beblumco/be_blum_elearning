@extends('layout.template')

@section('title_page', 'Certificados')

@section('content')
<div class="container-fluid">
    <div>
        <div class="page-titles">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('trainings_index') }}">Capacitaciones</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Certificados</a></li>
            </ol>
        </div>

        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-body">
                       {{-- TABLA CERTIFICADOS --}}
                            <div class="col-lg-12">
                                <div class="pt-4">
                                    <div class="table-responsive">
                                        <table class="table table-responsive-md">
                                            <thead class="text-center">
                                                <tr>
                                                    <th>CAPACITACIÓN</th>
                                                    <th>FECHA</th>
                                                    <th>ACCIONES</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <img src="{{ asset('img/shop_module/file-pdf.svg') }}" class="icon-file-table-svg">Buenas Prácticas de Manufactura klaxen
                                                    </td>
                                                    <td>
                                                        23 Junio de 2022
                                                    </td>
                                                    <td class="text-center">
                                                        <img src="{{ asset('img/shop_module/cloud-download-alt-solid.svg') }}"
                                                            class="icon-table-svg" data-toggle="tooltip" data-placement="top"
                                                            title="Descargar" onclick='OnClickDownloadDocumentation(1)'>
                                                        <img src="{{ asset('img/shop_module/share-alt-square-solid.svg') }}"
                                                            class="icon-table-svg" data-toggle="tooltip" data-placement="top"
                                                            title="Compartir" onclick="OnClickSharePdf(1)">
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>
                                                        <img src="{{ asset('img/shop_module/file-pdf.svg') }}" class="icon-file-table-svg">Protocolo casos COVID-19
                                                    </td>
                                                    <td>
                                                        21 Junio de 2022
                                                    </td>
                                                    <td class="text-center">
                                                        <img src="{{ asset('img/shop_module/cloud-download-alt-solid.svg') }}"
                                                            class="icon-table-svg" data-toggle="tooltip" data-placement="top"
                                                            title="Descargar" onclick='OnClickDownloadDocumentation(2)'>
                                                        <img src="{{ asset('img/shop_module/share-alt-square-solid.svg') }}"
                                                            class="icon-table-svg" data-toggle="tooltip" data-placement="top"
                                                            title="Compartir" onclick="OnClickSharePdf(2)">
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        {{-- FIN - TABLA CERTIFICADOS --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
