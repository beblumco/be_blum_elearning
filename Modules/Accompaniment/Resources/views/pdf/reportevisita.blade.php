<body style="width: 100%;">
    <br>
    <table id="datosCompania" style="width: 100%;">
        <tr>
            <td rowspan="7">
                <img style="margin-right: 5px; width: 150px; margin-left: 20px;"
                    src={{ $data->img_avatar == null ? public_path('img/imagen_no_disponible.png') : public_path() . '/storage/avatars/' . $data->img_avatar }} alt="">
            </td>
            <td><b>Fecha:</b><br></td>
            <td><span>{{ $data->fecha }}</span><br></td>
        </tr>
        <tr>
            <td><b>Grupo empresa:</b><br></td>
            <td><span>{{ $data->grupo_empresa }}</span><br></td>
        </tr>
        <tr>
            <td><b>Razón social:</b><br></td>
            <td><span>{{ $data->razon_social }}</span></td>
        </tr>
        <tr>
            <td><b>Centro de costo:</b><br></td>
            <td><span>{{ $data->centro_costo }}</span><br></td>
        </tr>
        <tr>
            <td><b>Ciudad:</b><br></td>
            <td><span>{{ $data->ciudad }}</span><br></td>
        </tr>
        <!--<tr>
            <td><b>Coordinada con:</b><br></td>
            <td><span>{{ $data->responsable }}</span><br></td>
        </tr>-->
        <tr>
            <td><b>Modalidad:</b><br></td>
            <td><span>{{ $data->modalidad }}</span><br></td>
        </tr>
    </table>
    <br>

    @foreach ($data->actividades as $key => $actividad)
        <!-- DONDE SE PUEDE ARRANCAR EL FOR PARA EL DETALLE DE LAS ACTIVIDADES -->
        <div class="datosActividad {{ $key > 0 ? 'page-break' : '' }}">
            <table class="datosActividad" style="width: 100%;">
                <tr>
                    <td rowspan="6">
                        <img style="margin-right: 10px; width: 150px; margin-left: 20px;"
                            src="{{ public_path() . '/img/new_logo_KLpq.png' }}" alt="">
                    </td>

                    <td><b>Actividad:</b><br></td>
                    <td><span>{{ $actividad->actividad }}</span><br></td>
                </tr>
                <tr>
                    <td><b>Experto Klaxen:</b><br></td>
                    <td><span>{{ $actividad->experto }}</span><br></td>
                </tr>
                <tr>
                    <td><b>Propuesta de valor:</b><br></td>
                    <td><span>Acompañamiento</span></td>
                </tr>
                <tr>
                    <td><b>Tipo:</b><br></td>
                    <td><span>{{ $actividad->tipo }}</span><br></td>
                </tr>
                <tr>
                    <td><b>Duración:</b><br></td>
                    <td><span>{{ number_format($actividad->tiempo / 60, 1) }} Hora(s)</span><br></td>
                </tr>
                <tr>
                    <td><b>Coordinada por:</b><br></td>
                    <td><span>{{ $actividad->responsable }}</span><br></td>
                </tr>
            </table>
            <div>
                <br>
            </div>
            <div>
                <div colspan="3">
                    <div
                        style="background-color: #203763; text-align: center; width: 97%; height: 23px; margin: auto auto;">
                        <span style="color: white;">
                            OBSERVACIÓN GENERAL
                        </span>
                    </div>
                    <div style="text-align: justify; width: 97%; margin: auto auto;">
                        <p>{{ $actividad->observacionGeneral->observacion }}</p>
                    </div>
                </div>
            </div>
            <div>

                <div colspan="3">
                    @if ((sizeof($actividad->respuestaActividadComun ?? [0]) > 0) && ($actividad->actividad != 'Capacitación presencial' || $actividad->actividad == 'Visita comercial'))
                        <div
                            style="background-color: #203763; text-align: center; width: 97%; height: 23px; margin: auto auto;">
                            <span style="color: white;">
                                {{ $actividad->actividad == 'Visita comercial' ? 'ACTIVIDADES REALIZADAS' : 'RESULTADOS' }}
                            </span>
                        </div>
                    @endif
                    <br>
                    <div
                        style="justify-content: center; text-align: justify; width: 97%; margin: auto auto; display: flex">
                        @if ($actividad->auditoria_id != 65)
                            <table
                                style="margin-left: auto; margin-right: auto; border-collapse: separate; border-spacing: 10px;">
                                @foreach ($actividad->resultadosPorCategoria as $key => $categoria)
                                    <tr>
                                        @foreach ($categoria as $item)
                                            <td class="contenedoresCategorias">
                                                <div name="cos">
                                                    <div class="textosCategoria">
                                                        <div style="height: 40px">
                                                            <p>{{ $item->CATEGORIA }}</p>
                                                        </div>
                                                        <div>
                                                            <p>{{ $item->RESULTADO }}%</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach

                            </table>
                            <table
                                style="margin-left: auto; margin-right: auto; border-collapse: separate; border-spacing: 10px;">
                                <tr>
                                    <td class="contenedoresCategorias">
                                        <div name="cos">
                                            <div class="textosCategoria">
                                                <div style="height: 40px">
                                                    <p>Resultado final</p>
                                                </div>
                                                <div>
                                                    <p>{{ $actividad->resultadoTotal[0]->RESULTADO }}%</p>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            <br>
                        @else
                            @if ($actividad->respuestaLuminometria != null)
                                <table class="table"
                                    style="font-size: 11px;margin-left: auto; margin-right: auto;width: 97%">
                                    <thead>
                                        <tr>
                                            <th colspan="11" style="font-size: 18px">LUMINOMETRÍA</th>
                                        </tr>
                                        <tr>
                                            <th>Área</th>
                                            <th>Superficie</th>
                                            <th>Antes</th>
                                            <th>Después</th>
                                            <th>Descripción</th>
                                            <th>Item</th>
                                            <th>Producto</th>
                                            <th>Cant. Concentración</th>
                                            <th>Unidad medida</th>
                                            <th>Cant. Cantidad</th>
                                            <th>Unidad medida (Canti)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($actividad->respuestaLuminometria as $r)
                                            <tr>
                                                <td>{{ $r->area }}</td>
                                                <td>{{ $r->superficie }}</td>
                                                <td>{{ $r->antes }}</td>
                                                <td>{{ $r->despues }}</td>
                                                <td>{{ $r->descripcion }}</td>
                                                <td>{{ $r->item }}</td>
                                                <td>{{ $r->producto }}</td>
                                                <td>{{ $r->cant_concentracion }}</td>
                                                <td>{{ $r->um_concentracion }}</td>
                                                <td>{{ $r->cant_cantidad }}</td>
                                                <td>{{ $r->um_cantidad }}</td>
                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                                <br>
                            @endif

                            @if ($actividad->respuestaActividadComun != null)
                                @foreach ($actividad->respuestaActividadComun as $r)
                                    <div>
                                        <p>{{ $r->descripcion_general }}</p>
                                        <table class="tableImagenes" style="margin-left: auto; margin-right: auto;">
                                            <thead>
                                                <th>Imagen</th>
                                                <th>Comentario</th>
                                            </thead>
                                            <tbody>
                                                @foreach ($r->detalle as $item)
                                                    <tr>
                                                        <td>
                                                            <br>
                                                            <img style="height: auto; width: auto; max-width: 200px; max-height: 200px;"
                                                                src="{{ $item->imagen == null ? public_path('img/imagen_no_disponible.png') : 'https://klaxen.co/storage/Actividades/' . $item->imagen }}"
                                                                alt="">
                                                        </td>
                                                        <td>
                                                            {{ $item->comentario }}
                                                            <br>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <br>
                                    </div>
                                @endforeach
                            @endif
                        @endif
                    </div>
                </div>
            </div>
            @if ($actividad->auditoria_id != 65)
                <div>
                    <div colspan="3">
                        <div
                            style="background-color: #203763; text-align: center; width: 97%; height: 23px; margin: auto auto;">
                            <span style="color: white;">
                                HALLAZGOS
                            </span>
                        </div>
                        <div style="text-align: justify; width: 97%;  margin: auto auto;">
                            <br>
                            @if (sizeof($actividad->hallazgos) == 0)
                                <p>Sin hallazgos</p>
                            @else
                                <table class="tableImagenes" style="margin-left: auto; margin-right: auto;">
                                    <thead>
                                        <th>Imagen</th>
                                        <th>Comentario</th>
                                    </thead>
                                    <tbody>
                                        @foreach ($actividad->hallazgos as $item)
                                            <tr>
                                                <td>
                                                    <br>
                                                    <img style="height: auto; width: auto; max-width: 200px; max-height: 200px;"
                                                        src="{{ $item->imagen == null ? public_path('img/imagen_no_disponible.png') : 'https://klaxen.co/imagenes/respuesta_auditoria/' . $item->imagen }}"
                                                        alt="">
                                                </td>
                                                <td>
                                                    <b>Pregunta: </b> {{ $item->pregunta }}.
                                                    <br>
                                                    <b>Respuesta: </b>{{ $item->respuesta }}.
                                                    <br>
                                                    <b>Observación: </b>{{ $item->observacion }}.
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

        </div>
        <br>

    @endforeach
    <style>
        #datosCompania {
            text-align: left;
            background-color: #dae3f4;
            border-radius: 12px;
            padding: 5px;
        }

        #tituloReporte {
            background-color: #203763;
            border-radius: 12px;
        }

        .datosActividad {
            padding: 5px;
            height: auto;
            min-height: 20px;
            background-color: #f1f1f1;
            border-radius: 12px;
        }

        .detalle {
            padding: 0px 20px;
        }

        body {
            font-family: "Arial", sans-serif;
        }

        .img-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            /* Configura 3 columnas de igual tamaño */
            grid-gap: 10px;
            /* Espacio entre las imágenes */
        }

        .img-container img {
            width: 100%;
            /* Ajusta el ancho de las imágenes al contenedor */
            height: auto;
            /* Ajusta la altura proporcionalmente */
            margin-bottom: 5px;
        }

        .contenedoresCategorias {
            background: white;
            height: 9.5rem;
            max-width: 120px;
            min-width: 120px;
            color: #4A4A4A;
            font-weight: bold;
            text-align: center;
            font-size: 9px;
            /* padding-top: 3.6rem; */
            border-radius: 1.4rem;
            margin: 4px;
            transition: all .3s;
            cursor: pointer;
            text-transform: uppercase;
        }

        .tableImagenes {
            font-family: arial, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        .tableImagenes td,
        .tableImagenes th {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }


        .table tbody {
            text-align: center;
            border: hidden;
        }

        .table tr thead,
        .table th {
            background-color: #dcdcdc;
            text-align: center;
            border: hidden;
        }

        .page-break {
            page-break-before: always;
        }
    </style>
</body>
