<?php

namespace Modules\Administration\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AdministrationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function IndexMyOrganization()
    {
        $page_title = 'Mi organización';
        $action = __FUNCTION__;
        $permisos = $this->GetAllPermisos();
        return view('administration::index',compact('page_title', 'action','permisos'));
    }

    public function IndexAdminTrainings()
    {
        $page_title = 'Capacitaciones virtuales';
        $action = __FUNCTION__;
        $permisos = $this->GetAllPermisos();
        return view('administration::virtual_trainings',compact('page_title', 'action','permisos'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('administration::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('administration::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('administration::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}
