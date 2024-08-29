@extends('layout.template')

@section('title_page', $page_title)

@section('content')
<div class="col-lg-12" id="app">
    <page-chat-ia-component gpt={{env("URL_GPT")}}></page-chat-ia-component>
</div>
    
@endsection

