<?php

namespace Modules\Record\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RecordController extends Controller
{
public function RecordIndex()
    {
        $page_title = 'Historial';
        $permisos = $this->GetAllPermisos();
        $action = __FUNCTION__;

        return view('record::index',compact('page_title', 'action','permisos'));
    }
}
