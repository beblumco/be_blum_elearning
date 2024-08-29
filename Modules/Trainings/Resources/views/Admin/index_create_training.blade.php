@extends('layout.template')

@section('title_page', $page_title)



@section('content')
<div class="col-lg-12" id="app">
    <page-create-training-component :id_training="{{ $id_training_decrypt }}" :id_grupo="{{ auth()->user()->id_grupo }}" id_main_account="{{ auth()->user()->main_account_id }}"></page-create-training-component>
</div>

@section('scripts')
<script src="{{ asset('assets/vendor/select2/js/select2.full.min.js') }}"></script>
<script>
$(function () {
    $('.select2').select2({
        // theme: "bootstrap",
        containerCssClass: 'form-control dev-select2',
        tags: true
    });
})
</script>
@endsection
@endsection
