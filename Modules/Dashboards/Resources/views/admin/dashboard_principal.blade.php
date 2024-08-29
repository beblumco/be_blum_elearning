{{-- Extends layout --}}
@extends('layout.template')

{{-- Content --}}
@section('content')
    <div id="app">
        <div class="container-fluid">
            <!-- Add Order -->
            <div class="modal fade" id="addOrderModalside">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add Event</h5>
                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form>
                                <div class="form-group">
                                    <label class="text-black font-w500">Event Name</label>
                                    <input type="text" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label class="text-black font-w500">Event Date</label>
                                    <input type="date" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label class="text-black font-w500">Description</label>
                                    <input type="text" class="form-control">
                                </div>
                                <div class="form-group">
                                    <button type="button" class="btn btn-primary">Create</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-12" style="min-height: 300px;">
                <div class="row">
                    <card1-component type="1" main_account="{{ auth()->user()->main_account_id }}"
                        icono='earth-globe-1' coloricon='#2BC155' colorfondo='#e3f9e9' badge="snapchat"
                        titulo="Sostenibilidad" subtitulo="Plástico ahorrado"
                        descripcion="Cantidad de plástico que dejaste de desperdiciar en productos de limpieza y desinfección">
                    </card1-component>

                    <card1-component type="2" main_account="{{ auth()->user()->main_account_id }}" icono='notepad'
                        coloricon='#204a23' colorfondo='#cfe8d6' badge="green" titulo="Entrenamiento"
                        subtitulo="Mis horas completadas" descripcion="Horas de capacitación que he recibido">
                    </card1-component>

                    @if (in_array(
                            'indica-acompanamiento',
                            collect($permisos)->pluck('evento')->toArray())
                            //|| DB::table('usuarios')->join('punto_evaluacion', 'punto_evaluacion.id', 'usuarios.id_punto')->join('unidad', 'unidad.id', 'punto_evaluacion.unidad_id')->join('centro_operacion', 'centro_operacion.id', 'unidad.centro_operacion_id')->where([['usuarios.id', auth()->user()->id], ['centro_operacion.main_account_id', '<>', null]])->exists()
                            )
                        <card1-component type="4" main_account="{{ auth()->user()->main_account_id }}" icono='flag-2'
                            coloricon='#cd5d30' colorfondo='rgb(255,234,221)' badge="snapchat" titulo="Acompañamiento"
                            subtitulo="Cantidad de acompañamientos"
                            descripcion="Número de acompañamiento virtuales y presenciales realizados a toda la organización.">
                        </card1-component>
                    @endif
                </div>
            </div>

            {{--  <div class="col-lg-12">
      <div class="col-lg-12 d-flex mb-1">
        <div class="col-lg-5">
          <h4 class="text-black font-w500 mb-0 fs-20">Ahorros en procesos LDI</h4>
        </div>
        <div class="col-lg-7 d-flex">
          <div class="col-lg-6 dropdown custom-dropdown d-flex justify-content-end">
            <div data-toggle="dropdown" aria-expanded="false">Todo el histórico
              <i class="fa fa-angle-down ml-3"></i>
            </div>
            <div class="dropdown-menu dropdown-menu-right" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(105px, 21px, 0px);">
              <a class="dropdown-item" href="#">Hoy</a>
              <a class="dropdown-item" href="#">Ayer</a>
              <a class="dropdown-item" href="#">Este mes</a>
              <a class="dropdown-item" href="#">Mes anterior</a>
              <a class="dropdown-item" href="#">Seleccionar periodo</a>
            </div>
          </div>

          <div class="col-lg-6 dropdown custom-dropdown d-flex justify-content-end">
            <div data-toggle="dropdown" aria-expanded="false">Todos los centros de costo
              <i class="fa fa-angle-down ml-3"></i>
            </div>
            <div class="dropdown-menu dropdown-menu-right" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(105px, 21px, 0px);">
              <a class="dropdown-item" href="#">Planta Yumbo</a>
              <a class="dropdown-item" href="#">Planta Medellín</a>
            </div>
          </div>
        </div>

      </div>
     </div>

     <div class="d-flex overflow-auto">

      <div class="col-lg-5 col-sm-6 pl-0">
        <div class="card">
          <div class="card-body dev-widget-stat d-flex">
            <div class="media align-items-center col-lg-12 pl-0">
              <span class="">
                <svg xmlns="http://www.w3.org/2000/svg" style="color: #FE634E" width="50" height="53" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-droplet">
                  <path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"></path>
                </svg>
              </span>
              <div class="media-body ml-1 d-flex">
                <div class="col-lg-10 pr-0 pl-0">
                  <p class="mb-0" style="font-weight: 500">Agua usada para enjuague detergente</p>
                  <small class="text-muted mb-2 dev-font-size" style="font-weight: 300">Agua usada para enjuague en procesos de limpieza</b></small>
                </div>
                <div class="col-lg-2" style="display: flex;align-items: center;justify-content: flex-end;">
                  <p class="mb-0 p-0 mr-1" style="color: black; font-size: 21px;font-weight: 500">50</p>
                  <span style="color: black; font-size: 21px;font-weight: 500">Lt</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-5 col-sm-6 pl-0">
        <div class="card">
          <div class="card-body dev-widget-stat d-flex">
            <div class="media align-items-center col-lg-12 pl-0">
              <span class="">
                <svg xmlns="http://www.w3.org/2000/svg" style="color: #FE634E" width="50" height="53" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-droplet">
                  <path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"></path>
                </svg>
              </span>
              <div class="media-body ml-1 d-flex">
                <div class="col-lg-10 pr-0 pl-0">
                  <p class="mb-0" style="font-weight: 500">Agua usada para enjuague desinfectante</p>
                  <small class="text-muted mb-2 dev-font-size" style="font-weight: 300">Agua usada para enjuague en procesos de desinfección</b></small>
                </div>
                <div class="col-lg-2" style="display: flex;align-items: center;justify-content: flex-end;">
                  <p class="mb-0 p-0 mr-1" style="color: black; font-size: 21px;font-weight: 500">20</p>
                  <span style="color: black; font-size: 21px;font-weight: 500">Lt</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-5 col-sm-6 pl-0">
        <div class="card">
          <div class="card-body dev-widget-stat d-flex">
            <div class="media align-items-center col-lg-12 pl-0">
              <span class="">
                <svg xmlns="http://www.w3.org/2000/svg" style="color: #FE634E" width="50" height="53" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash">
                  <polyline points="3 6 5 6 21 6"></polyline>
                  <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                </svg>
              </span>
              <div class="media-body ml-1 d-flex">
                <div class="col-lg-10 pr-0 pl-0">
                  <p class="mb-0" style="font-weight: 500">Desperdicio de plástico</p>
                  <small class="text-muted mb-2 dev-font-size" style="font-weight: 300">peso de plástico de empaque por litro</b></small>
                </div>
                <div class="col-lg-2" style="display: flex;align-items: center;justify-content: flex-end;">
                  <p class="mb-0 p-0 mr-1" style="color: black; font-size: 21px;font-weight: 500">10</p>
                  <span style="color: black; font-size: 21px;font-weight: 500">Gr</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-5 col-sm-6 pl-0">
        <div class="card">
          <div class="card-body dev-widget-stat d-flex">
            <div class="media align-items-center col-lg-12 pl-0">
              <span class="mr-4">
                <i class="flaticon-381-shut-down" style="font-size: 53px;color: #FE634E"></i>
              </span>
              <div class="media-body ml-1 d-flex">
                <div class="col-lg-10 pr-0 pl-0">
                  <p class="mb-0" style="font-weight: 500">Insumo usado</p>
                  <small class="text-muted mb-2 dev-font-size" style="font-weight: 300">Cantidad de insumo requerido para el proceso</b></small>
                </div>
                <div class="col-lg-2" style="display: flex;align-items: center;justify-content: flex-end;">
                  <p class="mb-0 p-0 mr-1" style="color: black; font-size: 21px;font-weight: 500">40</p>
                  <span style="color: black; font-size: 21px;font-weight: 500">Kg</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-5 col-sm-6 pl-0">
        <div class="card">
          <div class="card-body dev-widget-stat d-flex">
            <div class="media align-items-center col-lg-12 pl-0">
              <span class="mr-4">
                <svg xmlns="http://www.w3.org/2000/svg" width="50" height="53"style="color: #FE634E" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-clock">
                  <circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline>
                </svg>
              </span>
              <div class="media-body ml-1 d-flex">
                <div class="col-lg-10 pr-0 pl-0">
                  <p class="mb-0" style="font-weight: 500">Tiempo de limpieza total</p>
                  <small class="text-muted mb-2 dev-font-size" style="font-weight: 300">Tiempo de ejecución de la limpieza</b></small>
                </div>
                <div class="col-lg-2" style="display: flex;align-items: center;justify-content: flex-end;">
                  <p class="mb-0 p-0 mr-1" style="color: black; font-size: 21px;font-weight: 500">10</p>
                  <span style="color: black; font-size: 21px;font-weight: 500">Hrs.</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-5 col-sm-6 pl-0">
        <div class="card">
          <div class="card-body dev-widget-stat d-flex">
            <div class="media align-items-center col-lg-12 pl-0">
              <span class="mr-4">
                <svg xmlns="http://www.w3.org/2000/svg" width="50" height="53"style="color: #FE634E" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-clock">
                  <circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline>
                </svg>
              </span>
              <div class="media-body ml-1 d-flex">
                <div class="col-lg-10 pr-0 pl-0">
                  <p class="mb-0" style="font-weight: 500">Tiempo de limpieza área o equipo</p>
                  <small class="text-muted mb-2 dev-font-size" style="font-weight: 300">Tiempo de ejecución de la limpieza de un área o equipo específico</b></small>
                </div>
                <div class="col-lg-2" style="display: flex;align-items: center;justify-content: flex-end;">
                  <p class="mb-0 p-0 mr-1" style="color: black; font-size: 21px;font-weight: 500">2</p>
                  <span style="color: black; font-size: 21px;font-weight: 500">Hr.</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-5 col-sm-6 pl-0">
        <div class="card">
          <div class="card-body dev-widget-stat d-flex">
            <div class="media align-items-center col-lg-12 pl-0">
              <span class="mr-4">
                <svg xmlns="http://www.w3.org/2000/svg" width="50" height="53"style="color: #FE634E" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-clock">
                  <circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline>
                </svg>
              </span>
              <div class="media-body ml-1 d-flex">
                <div class="col-lg-10 pl-0">
                  <p class="mb-0" style="font-weight: 500">Tiempo parada de producción</p>
                  <small class="text-muted mb-2 dev-font-size" style="font-weight: 300">Tiempo que se para la producción para ejecutar procesos de limpieza</b></small>
                </div>
                <div class="col-lg-2" style="display: flex;align-items: center;justify-content: flex-end;">
                  <p class="mb-0 p-0 mr-1" style="color: black; font-size: 21px;font-weight: 500">80</p>
                  <span style="color: black; font-size: 21px;font-weight: 500">Min.</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-5 col-sm-6 pl-0">
        <div class="card">
          <div class="card-body dev-widget-stat d-flex">
            <div class="media align-items-center col-lg-12 pl-0">
              <span class="mr-4">
                <svg xmlns="http://www.w3.org/2000/svg" style="color: #FE634E" width="50" height="53" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user-plus"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>
              </span>
              <div class="media-body ml-1 d-flex">
                <div class="col-lg-10 pr-0 pl-0">
                  <p class="mb-0" style="font-weight: 500">Horas extras</p>
                  <small class="text-muted mb-2 dev-font-size" style="font-weight: 300">Total horas extras pagadas por ejecutar actividades de limpieza</b></small>
                </div>
                <div class="col-lg-2" style="display: flex;align-items: center;justify-content: flex-end;">
                  <p class="mb-0 p-0 mr-1" style="color: black; font-size: 21px;font-weight: 500">Si</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-5 col-sm-6 pl-0">
        <div class="card">
          <div class="card-body dev-widget-stat d-flex">
            <div class="media align-items-center col-lg-12 pl-0">
              <span class="mr-4">
                <svg xmlns="http://www.w3.org/2000/svg" style="color: #FE634E" width="50" height="53" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user-plus"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>
              </span>
              <div class="media-body ml-1 d-flex">
                <div class="col-lg-10 pr-0 pl-0">
                  <p class="mb-0" style="font-weight: 500">Producción por hora/minutos</p>
                  <small class="text-muted mb-2 dev-font-size" style="font-weight: 300">Cantidad de producto que se podría producir en determinado tiempo</b></small>
                </div>
                <div class="col-lg-2" style="display: flex;align-items: center;justify-content: flex-end;">
                  <p class="mb-0 p-0 mr-1" style="color: black; font-size: 21px;font-weight: 500">10</p>
                  <span style="color: black; font-size: 21px;font-weight: 500">Lt</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-5 col-sm-6 pl-0">
        <div class="card">
          <div class="card-body dev-widget-stat d-flex">
            <div class="media align-items-center col-lg-12 pl-0">
              <span class="mr-4">
                <svg xmlns="http://www.w3.org/2000/svg" style="color: #FE634E" width="50" height="53" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
              </span>
              <div class="media-body ml-1 d-flex">
                <div class="col-lg-10 pr-0 pl-0">
                  <p class="mb-0" style="font-weight: 500">No. Colaboradores LyD</p>
                  <small class="text-muted mb-2 dev-font-size" style="font-weight: 300">Cantidad de operarios necesarios para ejecutar el proceso de lyd</b></small>
                </div>
                <div class="col-lg-2" style="display: flex;align-items: center;justify-content: flex-end;">
                  <p class="mb-0 p-0 mr-1" style="color: black; font-size: 21px;font-weight: 500">20</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>



     </div>

     <div class="col-lg-12">
      <div class="col-lg-12 d-flex mb-1">
        <div class="col-lg-6">
          <h6 class="fs-20 text-black font-w500 mb-0">Cumplimiento de propuesta de valor</h6>
        </div>
      </div>
     </div>

     <!-- row -->
     <div class="row">
       <div class="col-xl-3 col-lg-6 col-sm-6">
         <div class="widget-stat card">
           <div class="card-body p-custom dev-widget-stat">
             <div class="media ai-icon">
               <span class="mr-1 bgl-primary text-primary dev-bg-icon">
                 <!-- <i class="ti-user"></i> -->
                 <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user-plus"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>
               </span>
               <div class="media-body">
                 <p class="mb-1">Acompañamiento</p>
                 <h4 class="mb-0">10 Horas</h4>
                 <span class="badge badge-primary" style="font-size: 11px;">95%</span>
               </div>
             </div>
           </div>
         </div>
       </div>
       <div class="col-xl-3 col-lg-6 col-sm-6">
         <div class="widget-stat card">
           <div class="card-body p-custom dev-widget-stat">
             <div class="media ai-icon">
               <span class="mr-1 bgl-warning text-warning">
                 <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-book"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
               </span>
               <div class="media-body">
                 <p class="mb-1">Entrenamiento</p>
                 <h4 class="mb-0">70 Horas</h4>
                 <span class="badge badge-danger" style="font-size: 11px;">98%</span>
               </div>
             </div>
           </div>
         </div>
       </div>
       <div class="col-xl-3 col-lg-6 col-sm-6">
         <div class="widget-stat card">
           <div class="card-body  p-custom dev-widget-stat">
             <div class="media ai-icon">
               <span class="mr-1 bgl-danger text-danger">
                 <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-droplet"><path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"></path></svg>
               </span>
               <div class="media-body">
                 <p class="mb-1">Insumos</p>
                 <div class="d-flex align-items-center">

                  <h4 class="mb-0">$3456.900</h4>
                  <span class="badge badge-danger" style="font-size: 11px;">10%</span>
                  <svg class="ml-1" width="16" height="8" viewBox="0 0 19 12" fill="none " xmlns="http://www.w3.org/2000/svg">
                    <path d="M2.00401 11.1924C0.222201 11.1924 -0.670134 9.0381 0.589795 7.77817L7.78218 0.585786C8.56323 -0.195262 9.82956 -0.195262 10.6106 0.585786L17.803 7.77817C19.0629 9.0381 18.1706 11.1924 16.3888 11.1924H2.00401Z" fill="red"></path>
                  </svg>

                </div>

               </div>
             </div>
           </div>
         </div>
       </div>
       <div class="col-xl-3 col-lg-6 col-sm-6">
         <div class="widget-stat card">
           <div class="card-body p-custom dev-widget-stat">
             <div class="media ai-icon">
               <span class="mr-1 bgl-success text-success">
                 <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-box"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
               </span>
               <div class="media-body">
                 <p class="mb-1">Equipos e implementos</p>
                 <h4 class="mb-0">$900.000</h4>
                 <span class="badge badge-success" style="font-size: 11px;">100%</span>
               </div>
             </div>
           </div>
         </div>
       </div>

       <div class="col-xl-6 col-xxl-6 col-lg-6">
         <div class="card border-0 pb-0">
           <div class="card-header border-0 pb-0">
             <h4 class="card-title" style="text-transform:none">Orden de servicio</h4>
             <button class="btn btn-primary">Nueva orden</button>
           </div>
           <div class="card-body">
             <div id="DZ_W_Todo3" class="widget-media dz-scroll height370">
               <ul class="timeline">

                 <li>
                   <div class="timeline-panel">
                     <div class="media mr-2">
                       <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-box"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                     </div>
                     <div class="media-body">
                       <h5 class="mb-1"># OS-34. Equipos e implementos - <small>POSTOBÓN - PLANTA YUMBO</small></h5>
                       <small class="text-muted">20 oct 2021 1:15pm <b>administrador</b></small>
                       <p class="mb-1">Para envío a la sucursal principal ...</p>
                       <a href="#" class="btn btn-success btn-xxs shadow">Resuelto</a>
                     </div>
                     <div class="dropdown">
                       <button type="button" class="btn btn-primary light sharp" data-toggle="dropdown">
                         <svg width="18px" height="18px" viewBox="0 0 24 24" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><rect x="0" y="0" width="24" height="24"/><circle fill="#000000" cx="5" cy="12" r="2"/><circle fill="#000000" cx="12" cy="12" r="2"/><circle fill="#000000" cx="19" cy="12" r="2"/></g></svg>
                       </button>
                       <div class="dropdown-menu dropdown-menu-right">
                         <a class="dropdown-item" href="#">Agregar observación</a>
                         <a class="dropdown-item" href="#">Editar</a>
                         <a class="dropdown-item" href="#">Eliminar</a>
                       </div>
                     </div>
                   </div>
                 </li>
                 <li>
                   <div class="timeline-panel">
                     <div class="media mr-2 media-danger">
                       <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-book"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                     </div>
                     <div class="media-body">
                       <h5 class="mb-1"># OS-30. Entrenamiento - <small>POSTOBÓN - PLANTA YUMBO</small></h5>
                       <small class="text-muted">19 oct 2021 1:15pm <b>administrador</b></small>
                       <p class="mb-1">Capacitación en uso de insumos químmicos</p>
                       <a href="#" class="btn btn-primary btn-xxs shadow">Proceso</a>
                       <!-- 				<a href="#" class="btn btn-outline-danger btn-xxs">Delete</a> -->
                     </div>
                     <div class="dropdown">
                       <button type="button" class="btn btn-danger light sharp" data-toggle="dropdown">
                         <svg width="18px" height="18px" viewBox="0 0 24 24" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><rect x="0" y="0" width="24" height="24"/><circle fill="#000000" cx="5" cy="12" r="2"/><circle fill="#000000" cx="12" cy="12" r="2"/><circle fill="#000000" cx="19" cy="12" r="2"/></g></svg>
                       </button>
                       <div class="dropdown-menu dropdown-menu-right">
                         <a class="dropdown-item" href="#">Agregar observación</a>
                         <a class="dropdown-item" href="#">Editar</a>
                         <a class="dropdown-item" href="#">Eliminar</a>
                       </div>
                     </div>
                   </div>
                 </li>
                 <li>
                   <div class="timeline-panel">
                     <div class="media mr-2 media-info">
                       <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user-plus"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>
                     </div>
                     <div class="media-body">
                       <h5 class="mb-1"># OS-25. Acompañamiento - <small>POSTOBÓN - PLANTA MEDELLÍN</small></h5>
                       <small class="text-muted">18 oct 2021 1:15pm <b>administrador</b></small>
                       <p class="mb-1">Revisión en rutinas LyD por inconsistencias ...</p>
                       <a href="#" class="btn btn-danger btn-xxs shadow">Abierto</a>
                       <!-- <a href="#" class="btn btn-outline-danger btn-xxs">Delete</a> -->
                     </div>
                     <div class="dropdown">
                       <button type="button" class="btn btn-info light sharp" data-toggle="dropdown">
                         <svg width="18px" height="18px" viewBox="0 0 24 24" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><rect x="0" y="0" width="24" height="24"/><circle fill="#000000" cx="5" cy="12" r="2"/><circle fill="#000000" cx="12" cy="12" r="2"/><circle fill="#000000" cx="19" cy="12" r="2"/></g></svg>
                       </button>
                       <div class="dropdown-menu dropdown-menu-right">
                         <a class="dropdown-item" href="#">Agregar observación</a>
                         <a class="dropdown-item" href="#">Editar</a>
                         <a class="dropdown-item" href="#">Eliminar</a>
                       </div>
                     </div>
                   </div>
                 </li>
                 <li>
                   <div class="timeline-panel">
                     <div class="media mr-2 media-success">
                       <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-droplet"><path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"></path></svg>
                     </div>
                     <div class="media-body">
                       <h5 class="mb-1"># OS-20. Insumos - <small>POSTOBÓN - PLANTA MEDELLÍN</small></h5>
                       <small class="text-muted">17 oct 2021 1:15pm <b>administrador</b></small>
                       <p class="mb-1">Entregar en sucursal principal</p>
                       <a href="#" class="btn btn-success btn-xxs shadow">Resuelto</a>
                     </div>
                     <div class="dropdown">
                       <button type="button" class="btn btn-success light sharp" data-toggle="dropdown">
                         <svg width="18px" height="18px" viewBox="0 0 24 24" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><rect x="0" y="0" width="24" height="24"/><circle fill="#000000" cx="5" cy="12" r="2"/><circle fill="#000000" cx="12" cy="12" r="2"/><circle fill="#000000" cx="19" cy="12" r="2"/></g></svg>
                       </button>
                       <div class="dropdown-menu dropdown-menu-right">
                         <a class="dropdown-item" href="#">Agregar observación</a>
                         <a class="dropdown-item" href="#">Editar</a>
                         <a class="dropdown-item" href="#">Eliminar</a>
                       </div>
                     </div>
                   </div>
                 </li>
                 <li>
                   <div class="timeline-panel">
                     <div class="media mr-2 media-success">
                       <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-droplet"><path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"></path></svg>
                     </div>
                     <div class="media-body">
                       <h5 class="mb-1"># OS-19. Insumos - <small>POSTOBÓN - PLANTA YUMBO</small></h5>
                       <small class="text-muted">16 oct 2021 1:15pm <b>administrador</b></small>
                       <p class="mb-1">Entregar en sucursal principal</p>
                       <a href="#" class="btn btn-success btn-xxs shadow">Resuelto</a>
                     </div>
                     <div class="dropdown">
                       <button type="button" class="btn btn-success light sharp" data-toggle="dropdown">
                         <svg width="18px" height="18px" viewBox="0 0 24 24" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><rect x="0" y="0" width="24" height="24"/><circle fill="#000000" cx="5" cy="12" r="2"/><circle fill="#000000" cx="12" cy="12" r="2"/><circle fill="#000000" cx="19" cy="12" r="2"/></g></svg>
                       </button>
                       <div class="dropdown-menu dropdown-menu-right">
                         <a class="dropdown-item" href="#">Agregar observación</a>
                         <a class="dropdown-item" href="#">Editar</a>
                         <a class="dropdown-item" href="#">Eliminar</a>
                       </div>
                     </div>
                   </div>
                 </li>
                 <li>
                   <div class="timeline-panel">
                     <div class="media mr-2 media-success">
                       <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-droplet"><path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"></path></svg>
                     </div>
                     <div class="media-body">
                       <h5 class="mb-1"># OS-18. Insumos - <small>POSTOBÓN - PLANTA YUMBO</small></h5>
                       <small class="text-muted">15 oct 2021 1:15pm <b>administrador</b></small>
                       <p class="mb-1">Entregar en sucursal principal</p>
                       <a href="#" class="btn btn-success btn-xxs shadow">Resuelto</a>
                     </div>
                     <div class="dropdown">
                       <button type="button" class="btn btn-success light sharp" data-toggle="dropdown">
                         <svg width="18px" height="18px" viewBox="0 0 24 24" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><rect x="0" y="0" width="24" height="24"/><circle fill="#000000" cx="5" cy="12" r="2"/><circle fill="#000000" cx="12" cy="12" r="2"/><circle fill="#000000" cx="19" cy="12" r="2"/></g></svg>
                       </button>
                       <div class="dropdown-menu dropdown-menu-right">
                         <a class="dropdown-item" href="#">Agregar observación</a>
                         <a class="dropdown-item" href="#">Editar</a>
                         <a class="dropdown-item" href="#">Eliminar</a>
                       </div>
                     </div>
                   </div>
                 </li>

               </ul>
             </div>
           </div>
         </div>
       </div>


        <propuesta-valor-component></propuesta-valor-component>


     </div>  --}}
        </div>
    </div>
@endsection
