@php
    if($data->img_avatar == null){
        $avatar = public_path().'/img/logo_savak.png';
    }else{
        $avatar = public_path().'/storage/'.$data->img_avatar;
    }

    if($data->main_account_asesor == 1){
        $avatarExperto = public_path().'/img/new_logo_KLpq.png';
    }else{
        $avatarExperto = public_path().'/storage/'.$avatarExperto;
    }
@endphp

<body style="width: 100%;">
    <table id="tituloReporte" style="width: 100%;">
        <tr>
            <td><img style="width: 150px; "
                    src="{{  public_path() . '/img/logoSavakWhite.png'   }}" alt="" srcset=""></td>
            <td style="width: 65%;">
                <h2 style="color: white; font-style: normal;"> REPORTE DE ENTRENAMIENTO</h2>
            </td>

        </tr>
    </table>
    <br>
    @if ($data->tipo == 2)
    <table id="datosCompania" style="width: 100%;">
        <tr>
            <td rowspan="6">
                <img style="margin-right: 5px; width: 150px; margin-left: 20px;"
                    src="{{ $avatar }}" alt="">
            </td>
            <td><b>Fecha:</b><br></td>
            <td><span>{{ $data->fecha_agendamiento }}</span><br></td>
        </tr>
        <tr>
            <td><b>Grupo empresa:</b><br></td>
            <td><span>{{ $data->grupo_empresa }}</span><br></td>
        </tr>
        <tr>
            <td><b>Razón social:</b><br></td>
            <td><span>{{ $data->empresa }}</span></td>
        </tr>
        <tr>
            <td><b>Centro de costo:</b><br></td>
            <td><span>{{ $data->centro_costo }}</span><br></td>
        </tr>
        <tr>
            <td><b>Ciudad:</b><br></td>
            <td><span>{{ $data->ciudad }}</span><br></td>
        </tr>
        <tr>
            <td><b>Coordinada con:</b><br></td>
            <td><span>{{ $data->anfitrion_cliente }}</span><br></td>
        </tr>
    </table>
    <br>
    @endif
   <!-- DONDE SE PUEDE ARRANCAR EL FOR PARA EL DETALLE DE LAS ACTIVIDADES -->
    <div class="datosActividad">
        <table class="datosActividad" style="width: 100%;">
            <tr>
                <td rowspan="5">
                    <img style="margin-right: 10px; width: 150px; margin-left: 20px;"
                    src="{{ $avatarExperto }}" alt=""></td>

                <td><b>Asesor experto:</b><br></td>
                <td><span>{{ $data->asesor }}</span><br></td>
            </tr>
            <tr>
                <td><b>Propuesta de valor:</b><br></td>
                <td><span>Entrenamiento</span></td>
            </tr>
            <tr>
                <td><b>Capacitación:</b><br></td>
                <td><span>{{ $data->capacitacion }}</span><br></td>
            </tr>
            <tr>
                <td><b>Duración:</b><br></td>
                <td><span>{{ number_format($data->duracion / 60, 1) }} Horas</span><br></td>
            </tr>
            <tr>
                <br>
            </tr>
            <tr>
                <td colspan="3">
                    <div style="background-color: #203763; text-align: center; width: 97%; height: 23px; margin: auto auto;">
                        <span style="color: white;">
                            DETALLE
                        </span>
                    </div>
                </td>
            </tr>
        </table>

        <div  class="datosActividad detalle">
            <p  class="textDesc">
                {!! $data->observacion !!}
            </p>
            <div class="img-container">
                @foreach ($img as $item)
                    <img
                    src="{{ public_path() . $item->path }}" alt="">
                @endforeach
            </div>
        </div>

    </div>
    <style>
        .textDesc {
            white-space: pre-line;
            text-align: left;
        }
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

        .detalle{
            padding: 0px 20px;
        }

        body{
            font-family: "Arial", sans-serif;
        }

        .img-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr); /* Configura 3 columnas de igual tamaño */
            grid-gap: 10px; /* Espacio entre las imágenes */
          }

          .img-container img {
            width: 100%; /* Ajusta el ancho de las imágenes al contenedor */
            height: auto; /* Ajusta la altura proporcionalmente */
            margin-bottom: 5px;
          }
    </style>
</body>
