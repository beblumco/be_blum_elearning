@inject('controller','App\Http\Controllers\Controller')
@php
	$dataUser = \DB::table('usuarios')
	->select(
			'usuarios.*',
			\DB::raw('IF(usuarios.img_avatar IS NULL, "logo_klaxen.png", usuarios.img_avatar) AS IMG_AVATAR'),
            'c.img_avatar AS AVATAR_GE',
            'u.img_avatar AS AVATAR_EMPRESA',
		)
    ->leftJoin('punto_evaluacion as p', 'usuarios.id_punto', 'p.id')
    ->leftJoin('unidad as u', 'p.unidad_id', 'u.id')
    ->leftJoin('centro_operacion as c', 'u.centro_operacion_id', 'c.id')
	->where('usuarios.id','=', auth()->user()->id)
	->first();

    $logoCliente = !empty($dataUser->AVATAR_EMPRESA) ? $dataUser->AVATAR_EMPRESA : $dataUser->AVATAR_GE;
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}" url="{{ env("URL") }}" api="{{ env("API") }}">
    <title>{{ config('dz.name') }} | @yield('title', $page_title ?? '')</title>

	<meta name="description" content="@yield('page_description', $page_description ?? '')"/>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/images/favicon.png') }}">

	{{-- Global Theme Styles (used by all pages) --}}
	@if(!empty(config('dz.public.global.css')))
		@foreach(config('dz.public.global.css') as $style)
			<link href="{{ asset($style) }}" rel="stylesheet" type="text/css"/>
		@endforeach
	@endif


	@if(!empty(config('dz.public.pagelevel.css.'.$action)))
		@foreach(config('dz.public.pagelevel.css.'.$action) as $style)
				<link href="{{ asset($style) }}" rel="stylesheet" type="text/css"/>
		@endforeach
	@endif

    <!-- AlpineJS -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body>
    <!--*******************
        Boton de whatsApp Start
    ********************-->
          @if (empty($whatsapp_no))  {{--ENVIAR DESDE EL CONTROLADOR LA VARIABLE SI NO SE DESEA VISUALIZAR  --}}
            <a class="btn-wsp"
                href="https://wa.me/573156253015?text=Buen%20día%20equipo%20Klaxen,%20solicito%20su%20apoyo%20para%20"
                target="_blank">
                <i class="bi bi-whatsapp"></i>
            </a>
        @endif
    <!--*******************
        Boton de whatsApp end
    ********************-->

    <!--*******************
        Preloader start
    ********************-->
    <div id="preloader" style="z-index: 9999;">
        <div class="sk-three-bounce">
            <div class="sk-child sk-bounce1"></div>
            <div class="sk-child sk-bounce2"></div>
            <div class="sk-child sk-bounce3"></div>
        </div>
    </div>
    <!--*******************
        Preloader end
    ********************-->

    <!--**********************************
        Main wrapper start
    ***********************************-->
    <div id="main-wrapper" class="show menu-toggle">

        <!--**********************************
            Nav header start
        ***********************************-->

         <div class="nav-header d-flex align-items-center">
            <div class="d-sm-none">
                <button class="btn btn-primary ml-1" style="border-radius: 0.5rem !important;" onclick="menu()">
                    <i class="bi bi-list"></i>
                </button>
            </div>

           <a href="{{ route('login_index') }}" class="brand-logo">
             <img
                class="logo-abbr"
                src="{{ asset('img/logo_savak.png') }}"
                style="{{ empty($logoCliente) ? 'max-width: 180px !important; max-height: 70px;' :'' }}"
             >
             <img class="logo-compact" src="{{ asset('assets/images/logo-text.png') }}" alt="">
             <h1 class="brand-title"><b>Savk</b></h1>
           </a>

           @if(!empty($logoCliente))
                <div class="d-none d-sm-block">
                    <img class="logo-abbr" src="{{ asset('/storage/'.$logoCliente) }}" alt="">
                </div>
            @endif


           {{-- <div class="nav-control">
             <div class="hamburger">
               <span class="line"></span><span class="line"></span><span class="line"></span>
             </div>
           </div> --}}
         </div>

        <!--**********************************
            Nav header end
        ***********************************-->

        <!--**********************************
            Header start
        ***********************************-->

		@include('elements.header', ['dataUser' => $dataUser])


        <!--**********************************
            Header end ti-comment-alt
        ***********************************-->

        <!--**********************************
            Sidebar start
        ***********************************-->
        @include('elements.sidebar')
        <!--**********************************
            Sidebar end
        ***********************************-->



        <!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body">
            <!-- row -->
            @yield('content')
        </div>
        <!--**********************************
            Content body end
        ***********************************-->


        <!--**********************************
            Footer start
        ***********************************-->

		@include('elements.footer')

        <!--**********************************
            Footer end
        ***********************************-->

		<!--**********************************
           Support ticket button start
        ***********************************-->

        <!--**********************************
           Support ticket button end
        ***********************************-->


    </div>
    <!--**********************************
        Main wrapper end
    ***********************************-->

    <!--**********************************
        Scripts
    ***********************************-->
    <script>
        const ROOT_URL = "{{ env('APP_URL') }}";
        let permisos = @json($permisos);//Recibo la variable desde laravel

        permisos = permisos.map(permiso => permiso.evento);

        if (localStorage.getItem('permisos') !== null) {
            localStorage.removeItem('permisos');
        }

        localStorage.setItem('permisos', JSON.stringify(permisos));

        function menu() {
            var deznavElement = $('.deznav');
            if (deznavElement.hasClass('d-none')) {
                // Si tiene la clase d-none, quítala
                deznavElement.removeClass('d-none');
              } else {
                // Si no tiene la clase d-none, agrégala
                deznavElement.addClass('d-none');
              }
            console.log("hola");
        }
    </script>
	@include('elements.footer-scripts')
    @yield('scripts')
</body>
</html>
