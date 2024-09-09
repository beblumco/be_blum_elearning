<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}" url="{{ env('URL') }}" api="{{ env('API') }}">
    <title>{{ config('dz.name') }} | @yield('title', $page_title ?? '')</title>

    <meta name="description" content="@yield('page_description', $page_description ?? '')" />
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/images/favicon.png') }}">

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

    <!-- AlpineJS -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body>

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

        <div class="nav-header">
            <a href="{{ route('login_index') }}" class="brand-logo">
                <img class="logo-abbr" src="{{ asset('img/logo_principal_primary.png') }}" alt="">
                <img class="logo-compact" src="{{ asset('assets/images/logo-text.png') }}" alt="">
                <h1 class="brand-title"><b>Savk</b></h1>
            </a>

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
        <div class="header">
            <div class="header-content">
                <nav class="navbar navbar-expand">
                    <div class="collapse navbar-collapse justify-content-between">
                        <div class="header-left">
                            <div class="dashboard_bar">

                            </div>
                        </div>

                        <ul class="navbar-nav header-right">

                        </ul>
                    </div>
                </nav>
            </div>
        </div>

        <!--**********************************
            Content body start
        ***********************************-->
        <div style="margin-top: 10%; margin-left: 30px; margin-right: 30px">
            <!-- row -->
            @yield('content')
        </div>
        <!--**********************************
            Content body end
        ***********************************-->


        <!--**********************************
            Footer start
        ***********************************-->

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
    <script></script>
    @include('elements.footer-scripts')
    @yield('scripts')
</body>

</html>
