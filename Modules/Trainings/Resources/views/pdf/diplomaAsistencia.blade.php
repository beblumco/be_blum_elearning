@php
    if($data->documento != null){ //SI NO TIENE DOCUMENTO SE LE DA MAS MARGIN PARA QUE NO SE DEFORME
        $marginCertificado = 12.5;
    }else{
        $marginCertificado = 16.5;
    }

    if($data->nom_modulo == null){
        $nombreCertificado = $data->nom_capacitacion;
    }else{
        $nombreCertificado = $data->nom_modulo;
    }

    $qbano = isset($data->centro_operacion_id) && $data->centro_operacion_id == 19 ? true : false;
    $url = !$qbano ? public_path() . '/img/Diploma-K-learning.jpg' : public_path() . '/img/Diploma_qbano.jpg';

    $marginCertificado-= $qbano ? 6 : 0;

    $designedBy = $data->designed_by != null ? "Capacitación diseñada por $data->designed_by" : '';
@endphp
<style>
	@page {
		margin: 0;
	}
</style>
<body
    style=" margin: 0px; background-image: url('{{ $url }}');  background-position: center; background-repeat: no-repeat;background-size: cover;">
    <div style="margin-top: {{ $qbano ? 30 : 33.5 }}%; margin-left: 0%; width: 100% ;text-align: center ">
        <span style="font-size: 28px">{{ mb_strtoupper($data->nombre, 'UTF-8') }}</span>
    </div>
    <div style="margin-left: 0%; width: 100% ;text-align: center ">
            <span style="font-size: 28px;">
                @if ($data->documento != null)
                    {{ "CC ".$data->documento }}
                @endif
            </span>
    </div>

    <div style="margin-left: 0%; width: 100% ;text-align: center; height: 20px; ">
        <span style="font-size: 18px;">
            {{ $designedBy }}
        </span>
    </div>

    <div style="margin-top: {{ $marginCertificado }}%; margin-left: 34.5%;  margin-right: 20.5%; ">
        <span style="width: 100% ;text-align: center; font-size: 24px; ">{{$nombreCertificado}}</span>
    </div>
    <div style="position: fixed;
        bottom: 0;
        margin-left: 78%;
        margin-bottom: {{ $qbano ? 15 : 6 }}%;"
    >
        <span style="width: 100% ;text-align: center; font-size: 24px; ">{{$data->tiempo}} Hr(s)</span>
    </div>
    <div style="position: fixed;
        bottom: 0;
        margin-left: 73%;"
    >
        <span style="width: 100% ;text-align: center; font-size: 24px; ">{{ \Carbon\Carbon::parse($data->fecha_terminada)->locale('es')->isoFormat('D [de] MMMM [del] YYYY') }}</span>
    </div>

</body>
