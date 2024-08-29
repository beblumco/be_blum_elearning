<body style="width: 100%;">
    <table id="tituloReporte" style="width: 100%; font-family: 'Source Sans Pro', Helvetica, Arial, sans-serif">
        <tr>
            <td><img style="width: 150px; "
                    src="{{  public_path() . '/img/logo_savak.png'   }}" alt="" srcset=""></td>
            <td style="width: 65%;">
                <h2 style="font-style: normal;"> EVALUACIÓN - {{ $evaluacion }}</h2>
            </td>

        </tr>
    </table>
    <br>
    <table style="width: 100%; font-family: 'Source Sans Pro', Helvetica, Arial, sans-serif">
        <tr>
            <td colspan="3">
                Nombre: {{ $usuario->nombre_com }}
            </td>
        </tr>
        <tr>
            <td colspan="3">
                Identificación: {{ $usuario->codigo }}
            </td>
        </tr>
        <tr>
            <td colspan="3">
                Calificación: {{ $calificacion }}
            </td>
        </tr>
        <tr><td style="height: 30px;"></td></tr>
        @foreach ($data as $index => $item)
            <tr>
                <td colspan="2">{{ $index+1 }}. {{ $item->pregunta }}</td>
            </tr>
            @foreach ($item->answers as $answer)
                <tr>
                    <td style="width: 5%">
                        <input type="checkbox" {{ $answer->checked == 'true' ? 'checked' : '' }}>
                    </td>
                    <td style="width: 90%;">
                        {{ $answer->respuesta }}
                    </td>
                    <td style="width: 5%">
                        @if ($answer->checked == 'true' && $answer->correct == 'true')
                            <img style="width: 20px; " src="{{  public_path() . '/img/check-circle-fill.svg'   }}" alt="" srcset="">
                        @endif
                        @if ($answer->checked == 'true' && $answer->correct == 'false')
                            <img style="width: 20px; " src="{{  public_path() . '/img/x-circle-fill.svg'   }}" alt="" srcset="">
                        @endif
                    </td>
                </tr>
            @endforeach
            <tr><td style="height: 20px;"></td></tr>
        @endforeach
    </table>
    <br>

    <style>
    </style>
</body>
