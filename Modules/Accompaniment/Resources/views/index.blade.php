@extends('layout.template')

@section('css')
    <style>
        .event-tabs .nav-tabs .nav-link.active {
            background-color: #002f54;
            color: white;
            border-color: #002f54;
        }

        .event-tabs .nav-tabs .nav-link {
            color: #002f54;
            border-color: #002f54;
        }

        .pagination .page-item.active .page-link {
            background-color: #002f54;
            color: white;
            border-color: #002f54;
            box-shadow: 0 10px 20px 0px rgba(254, 99, 78, 0.05);
        }

        img.dev-icon-table {
            fill: #002f54
        }

        .dev-icon-table {
            cursor: pointer;
            width: 18px;
            filter: invert(68%) sepia(66%) saturate(5221%) hue-rotate(1deg) brightness(102%) contrast(105%);
        }

        a {
            margin-right: 15px;
        }

        .badge-primary {
            background-color: #ff7f00;
        }

        .badge-secondary {
            background-color: #002f54;
        }

        .pagination .page-item .page-link:hover {
            background-color: #ff7f00;
        }
    </style>
@endsection

@section('content')
    <div id="app">
        <index-accompaniment-component id_grupo="{{ Auth::user()->id_grupo }}"
            savk_principal="{{ Auth::user()->savk_principal }}"
            main_account="{{ Auth::user()->main_account_id }}"></index-accompaniment-component>
    </div>
    @section('scripts')
        <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyClW1wVkJdrfHUH_i0hMhDmPfwVq0xTrv8"></script>
    @endsection
@endsection
