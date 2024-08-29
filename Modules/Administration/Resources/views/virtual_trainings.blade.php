@extends('layout.template')

@section('content')

<div class="container-fluid dev_user" codeUser="{{ auth()->user()->codigo }}">
  <div class="d-flex flex-wrap mb-2 align-items-center justify-content-between">

    <div class="mb-3 mr-3 col-lg-12 d-flex justify-content-between">
      <h6 class="fs-16 text-red font-w600 mb-0">Capacitaciones virtuales</h6>
    </div>

  </div>

  <div class="col-xl-12">
    <div class="card">

      <div class="card-body">
        <div id="parent_accordion" class="accordion accordion-rounded-stylish accordion-gradient">
          <div class="accordion__item">
            <div onclick="OnClickCollapse(this);" class="accordion__header accordion__header--primary" data-toggle="collapse" > <span class="accordion__header--icon"></span>
              <span class="accordion__header--text">Crear capacitación</span>
            </div>
            <div id="creation_collapse" class="collapse accordion__body" data-parent="#parent_accordion">
              <div class="accordion__body--text">
                
                <div class="col-lg-12">

                  <div class="basic-form col-lg-12">
                    <form>
                      <div class="form-row d-flex justify-content-between">
                        {{-- INIT SELECT --}}
                        <div class="mb-3 row col-lg-4">
                          <label for="staticEmail" class="col-form-label">Capacitación: <span class="dev-required">*</span></label>
                          <div class="col-lg-12 p-0">
                              <select id="training_select" class="single-select" onchange="OnChangeTrainingModules(this);">
                                  {{-- CARGADO POR JS --}}
                              </select>
                          </div>
                        </div>
                        {{-- END SELECT --}}

                        {{-- INIT SELECT --}}
                        <div class="mb-3 row col-lg-4">
                          <label for="staticEmail" class="col-form-label">Modulo: <span class="dev-required">*</span></label>
                          <div class="col-lg-12 p-0">
                              <select id="module_select" class="single-select" >
                                  {{-- CARGADO POR JS --}}
                              </select>
                          </div>
                        </div>
                        {{-- END SELECT --}}

                        {{-- INIT SELECT --}}
                        <div class="mb-3 row col-lg-4">
                          <label for="staticEmail" class="col-form-label">Grupo empresa: <span class="dev-required">*</span></label>
                          <div class="col-lg-12 p-0">
                              <select id="company_group_select" class="single-select" onchange="OnChangeCompanyGroup(this);">
                                {{-- CARGADO POR JS --}}
                              </select>
                          </div>
                        </div>
                        {{-- END SELECT --}}

                        {{-- INIT SELECT --}}
                        <div class="mb-3 row col-lg-4">
                          <label for="staticEmail" class="col-form-label">Empresa: <span class="dev-required">*</span></label>
                          <div class="col-lg-12 p-0">
                              <select id="company_select" class="single-select" onchange="OnChangeCompany(this);">
                                  {{-- CARGADO POR JS --}}
                              </select>
                          </div>
                        </div>
                        {{-- END SELECT --}}

                        {{-- INIT SELECT --}}
                        <div class="mb-3 row col-lg-4">
                          <label for="staticEmail" class="col-form-label">Punto de venta: <span class="dev-required">*</span></label>
                          <div class="col-lg-12 p-0">
                              <select id="pdv_select" class="single-select" >
                                  {{-- CARGADO POR JS --}}
                              </select>
                          </div>
                        </div>
                        {{-- END SELECT --}}

                        {{-- INIT DATE --}}
                        <div class="mb-3 row col-lg-4">
                          <label for="staticEmail" class="col-form-label">Fecha de capacitación: <span class="dev-required">*</span></label>
                          <div class="form-group col-lg-12 p-0">
                            <input type="date" class="form-control" id="fechaTraining">
                          </div>
                        </div>
                        {{-- END DATE --}}

                        {{-- INIT TEXTAREA --}}
                        <div class="mb-3 row col-lg-6">
                          <label for="staticEmail" class="col-form-label">Descripción: <span class="dev-required">*</span></label>
                          <form>
                            <div class="form-group col-lg-12 p-0">
                              <textarea class="form-control" rows="4" id="descriptionTextArea"></textarea>
                            </div>
                          </form>
                        </div>
                        {{-- END TEXTAREA --}}

                        {{-- INIT TEXTAREA --}}
                        <div class="mb-3 row col-lg-6 d-flex justify-content-end align-items-end">
                          <form>
                            <div class="form-group col-lg-12 p-0 m-0">
                              <button type="button" class="btn btn-primary mb-2" onclick="OnClickToCreateLink(this);">Crear link capacitación</button>
                            </div>
                          </form>
                        </div>
                        {{-- END TEXTAREA --}}

                      </div>
                    </form>
                  </div>
                  
                </div>
                
              </div>
            </div>
          </div>

        </div>

        <div id="parent_accordion_trainings" class="accordion accordion-rounded-stylish accordion-gradient">

          <div class="accordion__item">
            <div onclick="OnClickCollapseTrainings(this);" class="accordion__header accordion__header--primary" data-toggle="collapse" > <span class="accordion__header--icon"></span>
              <span class="accordion__header--text">Capacitaciones creadas</span>
            </div>
            <div id="creation_collapse_trinings" class="collapse accordion__body" data-parent="#parent_accordion_trainings">
              <div class="accordion__body--text">
                
                <div class="col-lg-12">
  
                  <div class="basic-form col-lg-12">

                    <div class="table-responsive">
                      <table class="table table-sm mb-0 table-responsive-lg text-black table_trainings">
                        <thead>
                          <tr>
                            <th class="align-middl text-center">Cliente</th>
                            <th class="align-middle pr-7 text-center">Fecha</th>
                            <th class="align-middle minw200 text-center">Capacitación</th>
                            <th class="align-middle text-center">Módulo</th>
                            <th class="align-middle text-center">Acciones</th>
                          </tr>
                        </thead>
                        <tbody id="orders">
                          {{-- CARGADO POR JS --}}
                        </tbody>
                      </table>
                    </div>
                  
                  </div>
                  
                </div>
                
              </div>
            </div>
          </div>

      </div>

      

      </div>

    </div>

    </div>
  </div>
  <!-- Column ends -->

</div>
@endsection
