<body style="width: 100%;">
    <table style="width: 100%; font-family: 'Source Sans Pro', Helvetica, Arial, sans-serif">
        <tr>
            <td><img style="width: 150px; "
                    src="{{  public_path() . '/img/logo_principal_primary.png'   }}" alt="" srcset=""></td>
            <td style="width: 65%;">
                <h2 style="font-style: normal; text-align: center"> Asistentes de capacitación {{ $capacitacion }}</h2>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                Fecha de realización: {{ $fecha }}
            </td>
        </tr>
        <tr>
            <td>
                <br>
            </td>
        </tr>
    </table>
    <table style="width: 100%;">
        <thead id="tituloReporte">
            <tr>
                {{--  <th><img style="width: 150px; "
                        src="{{  public_path() . '/img/logoSavakWhite.png'   }}" alt="" srcset=""></th>  --}}
                <th>
                    Nombre
                </th>
                <th>
                    Documento
                </th>
                <th>
                    Email
                </th>
                @if ($tipo == 1)
                    <th>
                        Empresa
                    </th>
                @endif
                @if ($evaluacion == true)
                    <th>
                        Evaluación
                    </th>
                @endif
                <th>
                    Firma
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $item)
                <tr>
                    <td>{{ $item->nombre }}</td>
                    <td>{{ $item->documento }}</td>
                    <td>{{ $item->email }}</td>
                    @if ($tipo == 1)
                        <td>
                            {{ $item->empresa }}
                        </td>
                    @endif
                    @if ($evaluacion == true)
                        <td>
                            {{ $item->aprobo }}
                        </td>
                    @endif
                    <td>
                        @if ($item->firma)
                            <img style="height: 40px; " src="{{ public_path() .'/'.$item->firma }}" alt="" srcset="">
                        @else
                            Sin firma
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <style>
        th{
            text-align: center;
            font-style: normal;
            color: white;
        }
        #tituloReporte {
            background-color: #203763;
            border-radius: 8px;
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
