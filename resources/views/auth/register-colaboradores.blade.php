@extends('layout.fullPage')

@section('css')
    <link href="{{ assets_version('assets/css/auth/login/main.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ assets_version('assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}">
    <link rel="stylesheet" href="{{ assets_version('assets/vendor/select2/css/select2.min.css') }}">
@endsection

@section('content')
    <div class="" id="app">
        <register-lider-grupo-empresa-component main_account="{{ $main_account }}" email_admin="{{$email}}" nombre_org="{{$organizacion}}"></register-lider-grupo-empresa-component>
    </div>

@section('scripts')
    <script src="{{ asset('assets/vendor/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins-init/select2-init.js') }}"></script>
    <script>
        $(function() {

            $('.select2').select2({
                theme: "bootstrap",
                containerCssClass: 'form-control alto'
            })

        })
    </script>
@endsection
@endsection
