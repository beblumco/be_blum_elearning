<!DOCTYPE html>
<html lang="es" class="h-100">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}" url="{{ env('URL') }}">
    <title>{{ config('dz.name') }} | @yield('title', $page_title ?? '')</title>
    <meta name="description" content="@yield('page_description', $page_description ?? '')" />
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/images/favicon.png') }}">
    {{-- <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet"> --}}
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&family=Roboto:wght@100;300;400;500;700;900&display=swap"
        rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400&display=swap" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <!--Añadimos el css generado con webpack-->

    {{-- Global Theme Styles (used by all pages) --}}
    @if (!empty(config('dz.public.global.css')))
        @foreach (config('dz.public.global.css') as $style)
            <link href="{{ asset($style) }}" rel="stylesheet" type="text/css" />
        @endforeach
    @endif

    @if (!empty(config('dz.public.pagelevel.css.' . $action)))
        @foreach (config('dz.public.pagelevel.css.' . $action) as $style)
            <link href="{{ asset($style) }}" rel="stylesheet" type="text/css" />
        @endforeach
    @endif
    <style>
        body {
            font-family: 'poppins', sans-serif;
        }
    </style>
    @yield('css')

    <!-- AlpineJS -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="h-100">

    <!--*******************
        Boton de whatsApp Start
    ********************-->
          @if (empty($whatsapp_no))  {{--ENVIAR DESDE EL CONTROLADOR LA VARIABLE SI NO SE DESEA VISUALIZAR  --}}
            <a class="btn-wsp" href="https://api.whatsapp.com/send?phone=573156253015" target="_blank">
                <i class="bi bi-whatsapp"></i>
            </a>
        @endif
    <!--*******************
        Boton de whatsApp end
    ********************-->


    <!--*******************
        Preloader start
    ********************-->
    <div id="preloader" style="z-index: 1000;">
        <div class="sk-three-bounce">
            <div class="sk-child sk-bounce1"></div>
            <div class="sk-child sk-bounce2"></div>
            <div class="sk-child sk-bounce3"></div>
        </div>
    </div>
    <!--*******************
        Preloader end
    ********************-->

    <div class="authincation h-100">
        <div class="container h-100">
            <div class="row justify-content-center h-100 align-items-center">
                @yield('content')
            </div>
        </div>
    </div>

    <script>
        const ROOT_URL = "{{ env('APP_URL') }}";
    </script>
    @include('elements.footer-scripts')
    @yield('scripts')

</body>

</html>
