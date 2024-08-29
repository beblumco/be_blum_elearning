<div class="container-fluid">
    <div class="d-flex justify-content-between">
        <div>
            <h3>DETALLE AUDITORÍA</h3>
        </div>
    </div>
    <br />
    <div class="contenido">
        <div style="background-color: #f2f2f2; border-radius: 1.4rem; padding-left: 20px; padding-right: 20px;">
            <table class="datosPrincipales" width="100%">
                <tbody>
                    <tr>
                        <td>
                            <b>Fecha: </b><span>{{ $datos->fecha }} </span>
                        </td>
                        <td>
                            <b>Centro de costo: </b><span>{{ $datos->centro_costo }} </span>
                        </td>
                        @if (isset($datos->nombreArea))
                            <td style="margin-right: 20px">
                                <b>Área: </b><span>{{ $datos->nombreArea }} </span>
                            </td>
                        @endif
                        <td rowspan="2">
                            @if ($datos->auditoria_id != 65)
                                <div class="resultadoFinal d-flex justify-content-center">
                                    <div>
                                        <div style="height: 20px; font-size: 14px !important">
                                            <p style="margin-top: 10px">Resultado final</p>
                                        </div>
                                        <div>
                                            <p style="font-size: 25px !important">
                                                {{ $datos->resultado }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <br>
                                <div class="d-flex justify-content-center">
                                    <div>
                                        <div style="height: 30px; font-size: 14px !important">
                                            <p style="margin-top: 20px"></p>
                                        </div>
                                        <div>
                                            <p style="font-size: 25px !important">

                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        @if (isset($datos->firma))
                            <td>
                                <br>
                                <b>Auditor: </b> <span>{{ $datos->auditor }} </span>
                            </td>
                        @else
                            <td>
                                <b>Auditor: </b> <span>{{ $datos->auditor }} </span>
                            </td>
                        @endif
                        <td>
                            <b>Coordinado con: </b>
                            <span>{{ $datos->responsable }} </span>
                            @if (isset($datos->firma))
                                <img style="max-width: 80px; max-height: 60px; margin-left: 10px"
                                    src="{{ 'https://klaxen.co/storage/Signatures/' . $datos->firma }}" alt="">
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="datosPrincipales">
                <table>
                    <tr class="datosPrincipales">
                        <td><b>Comentarios: </b></td>
                    </tr>
                    <tr>
                        <td align="justify" colspan="3">
                            <p> {{ $datos->observacion }} </p>
                        </td>
                    </tr>
                </table>

            </div>
        </div>
        <br>
        @if ($datos->auditoria_id != 65)
            <div class="d-flex justify-content-center">
                <table style="margin-left: auto; margin-right: auto;">
                    @foreach ($datos->resultadosPorCategoria as $item)
                        <td class="resultadoPorCategoria d-flex justify-content-center">
                            <div>
                                <div style="font-size: 12px !important; height: 70px">
                                    <p> {{ $item->CATEGORIA }} </p>
                                </div>
                                <div>
                                    <p>
                                        <b> {{ $item->RESULTADO }} %</b>
                                    </p>
                                </div>
                            </div>
                        </td>
                    @endforeach
                </table>
            </div>
        @endif
        <div class="page-break">
            @if ($datos->auditoria_id != 65)
                @foreach ($datos->detalle as $item)
                    <table id="customers" class="table" style="width: 100%">
                        <thead>
                            <tr>
                                <th style="text-transform: uppercase" colspan="4">
                                    {{ $item[0]->categoria }}
                                </th>
                            </tr>
                            <tr>
                                <th><b>ÍTEM</b></th>
                                <th><b>RESULTADO</b></th>
                                <th><b>OBSERVACIÓN</b></th>
                                <th><b>EVIDENCIAS</b></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($item as $pregunta)
                                <tr>
                                    <td style="width: 40%"> {{ $pregunta->pregunta }} </td>
                                    <td style="text-align:center;">
                                        @if ($pregunta->respuesta == 'No cumple')
                                            <b style="text-align:center; color: red;">
                                                {{ $pregunta->respuesta }}
                                            </b>
                                        @endif

                                        @if ($pregunta->respuesta == 'Cumple')
                                            <b style="text-align:center; color: green;">
                                                {{ $pregunta->respuesta }}
                                            </b>
                                        @endif

                                        @if ($pregunta->respuesta == 'No aplica' || $pregunta->respuesta == 'N/A')
                                            <b style="text-align:center;">
                                                {{ $pregunta->respuesta }}
                                            </b>
                                        @endif



                                    </td>
                                    <td style="width: 30%"> {{ $pregunta->observacion }} </td>
                                    <td style="width: 20%">
                                        <table>
                                            @foreach ($pregunta->imagenes as $imagen)
                                                <td style="border: none; padding: 0px;">
                                                    <img class="modalImage"
                                                        src="{{ 'https://klaxen.co/imagenes/respuesta_auditoria/' . $imagen }}" />
                                                </td>
                                            @endforeach
                                        </table>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endforeach
            @endif
            @if ($datos->auditoria_id == 65)
                <table id="customers" class="table">
                    <thead>
                        <tr>
                            <th style="text-transform: uppercase">{{ $datos->actividad }}</th>
                        </tr>
                    </thead>
                </table>
                @foreach ($datos->respuestaActividadComun as $key => $item)
                    <table id="customers" class="table {{ $key > 0 ? 'page-break' : '' }}" style="width: 100%;">
                        <thead>
                            <tr>
                                <th
                                    style="text-transform: uppercase;   background: #e6f0ff !important;
                                color: black !important;
                                text-align: center !important;">
                                    Descripción
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $item->descripcion_general }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <table id="customers" class="table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th style="text-transform: uppercase">EVIDENCIAS
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($item->detalle as $imagen)
                                <tr>
                                    <td>
                                        <div name="cos">
                                            <div style="text-align: center">
                                                <img style="height: 400px; width: 400px; max-width: 400px; max-height: 400px;"
                                                    src="{{ $imagen->imagen == null ? public_path('img/imagen_no_disponible.png') : 'https://klaxen.co/storage/Actividades/' . $imagen->imagen }}"
                                                    alt="" />
                                            </div>
                                            <div>
                                                <p style="text-align: center">{{ $imagen->comentario }}</p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endforeach
                @foreach ($datos->respuestaLuminometria as $key => $item)
                    <table id="customers" class="table" style="width: 100%;">
                        <thead>
                            @if (sizeof($item) != 0)
                                <th colspan="12">
                                    Luminometrías de {{ $key }}
                                </th>

                                @if ($key == 'superficies')
                                    <tr class="theadDetalle">
                                        <th>Área</th>
                                        <th>Superficie</th>
                                        <th>Antes</th>
                                        <th>Después</th>
                                        <th colspan="2">Descripción</th>
                                        <th>Item</th>
                                        <th>Producto</th>
                                        <th>Cant. Concentración</th>
                                        <th>Unidad medida</th>
                                        <th>Cant. cantidad</th>
                                        <th>Unidad medida</th>
                                    </tr>
                                @endif
                                @if ($key == 'manos')
                                    <tr class="theadDetalle">
                                        <th>Área</th>
                                        <th>Cargo</th>
                                        <th>Respon.</th>
                                        <th>Antes</th>
                                        <th>Después</th>
                                        <th>Descrip.</th>
                                        <th>Item</th>
                                        <th>Producto</th>
                                        <th>Cant. Concentración</th>
                                        <th>Unidad medida</th>
                                        <th>Cant. cantidad</th>
                                        <th>Unidad medida</th>
                                    </tr>
                                @endif
                            @endif
                        </thead>

                        @if ($key == 'manos')
                            <tbody>
                                @foreach ($datos->respuestaLuminometria['manos'] as $dato)
                                    <tr>
                                        <td>{{ $dato->AREA }}</td>
                                        <td>{{ $dato->CARGO_PERSONA }}</td>
                                        <td>{{ $dato->NOMBRE_RESPONSABLE }}</td>
                                        <td>{{ $dato->ANTES }}</td>
                                        <td>{{ $dato->DESPUES }}</td>
                                        <td>
                                            {{ $dato->DESCRIPCION }}
                                        </td>
                                        <td>
                                            {{ $dato->ITEM_PRODUCTO_MANOS }}
                                        </td>
                                        <td>
                                            {{ $dato->PRODUCTO_MANOS }}
                                        </td>
                                        <td>
                                            {{ $dato->CANTIDAD_CONCENTRACION_MANOS }}
                                        </td>
                                        <td>
                                            {{ $dato->UNIDAD_MEDIDA_CONCENTRACION_MANOS }}
                                        </td>
                                        <td>
                                            {{ $dato->CANT_CANTIDAD_MANOS }}
                                        </td>
                                        <td>
                                            {{ $dato->UNIDAD_MEDIDA_CANT_MANOS }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        @endif

                        @if ($key == 'superficies')
                            <tbody>
                                @foreach ($datos->respuestaLuminometria['superficies'] as $dato)
                                    <tr>
                                        <td>{{ $dato->AREA }}</td>
                                        <td>{{ $dato->SUPERFICIE }}</td>
                                        <td>{{ $dato->ANTES }}</td>
                                        <td>{{ $dato->DESPUES }}</td>
                                        <td colspan="2">{{ $dato->DESCRIPCION }}</td>
                                        <td>
                                            {{ $dato->ITEM_PRODUCTO_SUPERFICIES }}
                                        </td>
                                        <td>
                                            {{ $dato->PRODUCTO_SUPERFICIES }}
                                        </td>
                                        <td>
                                            {{ $dato->CANTIDAD_CONCENTRACION_SUPERFICIES }}
                                        </td>
                                        <td>
                                            {{ $dato->UNIDAD_MEDIDA_CONCENTRACION_SUPERFICIES }}
                                        </td>
                                        <td>
                                            {{ $dato->CANT_CANTIDAD_SUPERFICIES }}
                                        </td>
                                        <td>
                                            {{ $dato->UNIDAD_MEDIDA_CONCENTRACION_SUPERFICIES }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        @endif


                    </table>
                @endforeach
                @if (isset($datos->respuestaCapacitacion))
                    <table id="customers" class="table" style="width: 100%;">
                        <thead>
                            <tr class="theadDetalle">
                                <th>Capacitación</th>
                                <th>Observación</th>
                                <th>Asistentes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $datos->respuestaCapacitacion->capacitacion }}</td>
                                <td>{{ $datos->respuestaCapacitacion->observacion }}</td>
                                <td>{{ sizeof($datos->respuestaCapacitacion->asistentes) }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <table id="customers" class="table" style="width: 100%;">
                        <thead>
                            <tr class="theadDetalle">
                                <th colspan="2">Asistentes</th>
                                <th>Identificación</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($datos->respuestaCapacitacion->asistentes as $dato)
                                <tr>
                                    <td colspan="2">{{ $dato->nombre }}</td>
                                    <td>{{ $dato->numero_documento }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <table id="customers" class="table" style="width: 100%;">
                        <thead>
                            <tr class="theadDetalle">
                                <th colspan="3">Evidencias</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($datos->respuestaCapacitacion->imagenes as $imagen)
                                <tr>
                                    <td colspan="3" style="text-align: center">
                                        <img style="max-width: 350px; max-height: 350px"
                                            src="{{ 'https://klaxen.co/storage/Capacitaciones/' . $imagen }}"
                                            alt="">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            @endif
        </div>
    </div>
</div>
<style>
    .container-fluid {
        font-family: Arial, Helvetica, sans-serif;
    }

    .dev-icon-back {
        font-size: 25px;
        cursor: pointer;
    }

    .dev-icon-certificate {
        color: #fe634e;
        font-size: 24px;
        cursor: pointer;
    }

    .dev-bg {
        background: rgba(254, 99, 78, 0.05);
        border-radius: 1.25rem;
        padding: 14px 18px 14px 18px;
    }

    .btn-menu {
        margin-right: 5px;
        margin-top: 5px;
        margin-bottom: 5px;
    }

    .menu-cap {
        background-color: white;
        border-radius: 1.25rem;
        /* padding: 8px; */
        padding-left: 5px;
        padding-right: 5px;
        margin-bottom: 20px;
        margin-left: 0px !important;
        margin-right: 0px !important;
        align-items: center;
    }

    .div-busqueda {
        display: flex;
        margin-left: auto;
        width: 50%;
        margin-top: 5px;
        margin-bottom: 5px;
    }

    .btn-barra {
        padding: 0.6rem 0.8rem;
        background-color: #e6f0ff;
        border-color: #e6f0ff;
        color: #002f54;
        box-shadow: none !important;
    }

    .btn-barra:hover {
        background-color: #002f54;
        border-color: #002f54;
        color: #e6f0ff;
        box-shadow: none !important;
    }

    .btn-barra-activo {
        background-color: #002f54;
        border-color: #002f54;
        color: #e6f0ff;
        box-shadow: none !important;
    }

    .btn-barra-naranja a {
        color: #e6f0ff;
    }

    .btn-barra-naranja {
        background-color: #ff7f00;
        border-color: #ff7f00;
        color: white;
        box-shadow: none !important;
    }

    .btn-barra-naranja:hover {
        background-color: #ff8000e0;
        color: white;
    }

    @media (max-width: 1125px) {
        .div-busqueda {
            /* margin-left: auto; */
            width: 100%;
            margin-top: 5px;
            margin-bottom: 5px;
        }
    }

    .dev-fonts-icon {
        font-size: 30px;
        margin-left: 8px;
    }

    .form-control-busqueda {
        height: 45px !important;
    }

    .titulo-card {
        height: 47px;
        /* Altura del div */
        display: -webkit-box;
        -webkit-line-clamp: 6;
        /* Número máximo de líneas a mostrar */
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .titulo-card:hover {
        height: auto;
        display: flex;
        overflow: auto;
        min-height: 47px;
    }

    .datosPrincipales {
        text-align: left;
        font-size: 16px;
    }

    .contenido {
        text-align: left;
        background-color: white;
        border-radius: 12px;
        padding: 5px;
    }

    .resultadoFinal {
        background: #002f54;
        color: white;
        width: 120px;
        text-align: center;
        /* padding-top: 3.6rem; */
        border-radius: 1.4rem;
        font-size: 30px;
        margin: 4px;
        transition: all 0.3s;
        cursor: pointer;
    }

    .resultadoPorCategoria {
        background: #fff8f6;
        color: #e48029;
        width: 150px;
        text-align: center;
        /* padding-top: 3.6rem; */
        border-radius: 1.4rem;
        font-size: 20px;
        margin: 4px;
        transition: all 0.3s;
        cursor: pointer;
    }

    .imageText {
        background: #ff7f00;
        border-radius: 10rem;
        margin-left: 80%;
        margin-top: -5%;
        width: 1rem;
        height: 1rem;
        font-size: 10px;
        font-weight: bold;
        color: white;
        text-align: center;
    }

    .modalImage {
        height: 50px;
        width: 50px;
        background-repeat: no-repeat;
        background-position: 50%;
        cursor: pointer;
    }

    #customers {
        border-collapse: collapse;
        width: 100%;
    }

    #customers td,
    #customers th {
        border: 1px solid #ddd;
        padding: 8px;
    }

    #customers tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    #customers th {
        text-align: center;
        background-color: #ff7f00;
        color: white;
    }

    .page-break {
        page-break-before: always;
    }
</style>
