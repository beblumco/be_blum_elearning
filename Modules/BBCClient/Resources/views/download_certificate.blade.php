@extends('layout.fullPage')

@section('title_page', 'BBC')

@section('content')
<div class="container-fluid mt-5">
    
    <div class="col-lg-12 d-flex justify-content-center flex-wrap">
        
        <div class="col-lg-12 d-flex justify-content-center mt-5">
                <iframe src="https://klaxen.co/savk/assets/docs/PUB_BBC_Klaxen.mp4" width="440" height="263" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
        </div>

        <div class="card col-lg-4 dev-cursor-pointer m-2" link="" id="dev_downloads_BBC">
            <div class="card-body p-0 dev-card-body">
                <span class="btn subtitle dev-font-green">Certifícate</span class="text-align-center">
            </div>
        </div>
    </div>
</div>
@endsection
