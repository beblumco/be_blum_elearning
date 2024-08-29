<?php

namespace Modules\BBCClient\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Mail\SuggestFormMail;
use Illuminate\Support\Facades\Mail;

class BBCClientController extends Controller
{
    public function IndexBBC()
    {
        $page_title = 'BBC';
        $action = __FUNCTION__;

        return view('bbcclient::index',compact('page_title', 'action'));
    }

    public function IndexDownloadBBC()
    {
        $page_title = 'BBC';
        $action = __FUNCTION__;

        return view('bbcclient::download_certificate',compact('page_title', 'action'));
    }

    public function IndexDownloadMatrizInsumoBBC()
    {
        $page_title = 'BBC';
        $action = __FUNCTION__;

        return view('bbcclient::download_matriz_insumo',compact('page_title', 'action'));
    }

    public function IndexFormBBC()
    {
        $page_title = 'BBC';
        $action = __FUNCTION__;

        return view('bbcclient::form',compact('page_title', 'action'));
    }

    public function SendEmailForm(Request $request)
    {
        $name = $request->get('name');
        $pdv = $request->get('pdv');
        $suggest = $request->get('suggest');

        $emails = ['coordinador.comercial@klaxen.com', 'gerenciacomercial@klaxen.com', 'lidercomercial@klaxen.com'];

        Mail::to($emails)->send(new SuggestFormMail((object)[
            'name' => $name,
            'pdv' => $pdv,
            'suggest' => $suggest
        ]));
        return response()->json([
            'success' => 1,
            'message' => 'Se envío el correo de tus inquietudes o novedades correctamente.',
            'responseCode' => 200,
            'data' => null
        ]);
    }

}
