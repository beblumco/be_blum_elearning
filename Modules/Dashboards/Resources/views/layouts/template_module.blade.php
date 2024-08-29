<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('dashboards.name') }} | @yield('title', $page_title ?? '')</title>

	<meta name="description" content="@yield('page_description', $page_description ?? '')"/>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/images/favicon.png') }}">

	@if(!empty(config('dashboards.public.pagelevel.css.'.$action)))
		@foreach(config('dashboards.public.pagelevel.css.'.$action) as $style)
				<link href="{{ asset($style) }}" rel="stylesheet" type="text/css"/>
		@endforeach
	@endif

	{{-- Global Theme Styles (used by all pages) --}}
	@if(!empty(config('dashboards.public.global.css')))
		@foreach(config('dashboards.public.global.css') as $style)
			<link href="{{ asset($style) }}" rel="stylesheet" type="text/css"/>
		@endforeach
	@endif

    <syle>
    .brand-logo h1{
        text-decoration: none;
        color: black;
    }
    </syle>
      @yield('style')
</head>

<body>

    <!--*******************
        Preloader start
    ********************-->
    <div id="preloader">
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
            {{-- LOGO SECTION --}}
            <a href="/dashboard" class="brand-logo">
                <img class="logo-abbr" src="{{ asset('img/logo_klaxen.png') }}" alt="">
                <img class="logo-compact" src="{{ asset('assets/images/logo-text.png') }}" alt="">
                <h1 class="brand-title"><b>Savk</b></h1>
            </a>
            {{--END - LOGO SECTION --}}

            {{-- MENÚ ICON --}}
            <div class="nav-control">
                <div class="hamburger">
                    <span class="line"></span><span class="line"></span><span class="line"></span>
                </div>
            </div>
            {{-- END - MENÚ ICON --}}
        </div>
        <!--**********************************
            Nav header end
        ***********************************-->

        <!--**********************************
            Header start
        ***********************************-->

		@include('dashboards::elements.header')


        <!--**********************************
            Header end ti-comment-alt
        ***********************************-->

        <!--**********************************
            Sidebar start
        ***********************************-->
        @include('dashboards::elements.sidebar')
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

		@include('dashboards::elements.footer')

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

    <script src="{{ asset('assets/js/custom.min.js') }}"></script>
    <!--**********************************
        Scripts
    ***********************************-->
	@include('dashboards::elements.footer-scripts')
</body>
</html>
