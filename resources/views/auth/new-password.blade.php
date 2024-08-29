{{-- Extends layout --}}
@extends('layout.fullPage')
@section('title', 'Nueva contraseña')
{{-- Content --}}
@section('content')
<div id="app" class="col-lg-6">
  <new-password-component 
  email="{{ $email }}"></new-password-component>
</div>
@endsection


