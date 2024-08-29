<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    public function WelcomeIndex()
    {
        $page_title = 'Bienvenido a savk';
        $action = __FUNCTION__;

        return view('welcome.welcome-to-savk', compact('page_title','action'));
    }
}
