@php
    if($data->documento != null){ //SI NO TIENE DOCUMENTO SE LE DA MAS MARGIN PARA QUE NO SE DEFORME
        $marginCertificado = '12.5';
    }else{
        $marginCertificado = '16.5';
    }

    if($data->nom_modulo == null){
        $nombreCertificado = $data->nom_capacitacion;
    }else{
        $nombreCertificado = $data->nom_modulo;
    }

    $designedBy = $data->designed_by != null ? "Capacitación diseñada por $data->designed_by" : '';

    if(empty($Img)){
        $Img = "/img/logo_principal_primary.png";
    }else{
        $Img = "/storage/$Img";
    }

    if(empty($img_certificado)){
        $img_certificado = "/img/Diploma_capacitaciones_propias.jpg";
    }else{
        $img_certificado = "/storage/$img_certificado";
    }

@endphp

<style>
	@page {
		margin: 0;
	}
</style>

<body
    style=" margin: 0px; background-image: url('{{ public_path() . $img_certificado }}');  background-position: center; background-repeat: no-repeat;background-size: cover;">
    <div style="margin-top: 1%; margin-left: 0%;text-align: center; height: 230px">
        @if($Img != null)
            <img src="{{ public_path() . $Img }}" style="height: 230px">
        {{--  @else  --}}
            {{--  <img src="{{ public_path() . '/img/new_logo_KLpq.png' }}" style="height: 230px">  --}}
        @endif

    </div>
    <div style="margin-top: 12%; margin-left: 0%; width: 100% ;text-align: center ">
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
        margin-bottom: 5%;"
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
