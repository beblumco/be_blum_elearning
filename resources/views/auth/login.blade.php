@extends('layout.fullPage')

@section('content')
<div class="col-md-6" id="app">
    <login-component  active-account="{{ $active_account }}" />    
</div>
@endsection
