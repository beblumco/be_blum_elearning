@extends('layout.template')

@section('title_page', $page_title)

@section('content')
    <div class="col-lg-12" id="app">
        <training-assist-component></training-assist-component>
    </div>
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
            /*margin-right: 15px;*/
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


