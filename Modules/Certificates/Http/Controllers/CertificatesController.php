<?php

namespace Modules\Certificates\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CertificatesController extends Controller
{
    public function CertificatesIndex()
    {
        $page_title = 'Certificados';
        $action = __FUNCTION__;

        return view('certificates::index',compact('page_title', 'action'));
    }
}
