
<template>
  <div class="row justify-content-end col-lg-12 m-3"></div>
  <div class="flex-wrap mb-2 align-items-center justify-content-between">
    <div class="row menu-cap">
      <div class="div-busqueda">
        <div class="mr-2 d-flex align-items-center">
          <!-- <button
            class="btn btn-barra-naranja"
            style="width: max-content"
            @click="OnClickOpenModal"
          >
           Solicitar
          </button> -->
          <button
            class="btn btn-barra-naranja ml-1"
            style="width: max-content"
            @click="OnClickOpenModalCrear()"
            id="btn-asistida-crear"
            v-if="permisos.includes('ent-asi-crear_asistida')"
          >
           Crear asistida
          </button>

        </div>

        <div class="mr-2">
            <a href="#" @click.prevent="OnClickRedirectNewTraining" v-if="permisos.includes('ent-ele-crear_curso') && permisos.includes('ent-asi-crear_asistida')">
                <button id="btn-elearning-menu-crear-curso" class="btn btn-barra-naranja" style="width: max-content;">Crear curso</button>
            </a>
        </div>

        <div class="input-group">
          <input
            v-model="inputSearch"
            type="text"
            class="form-control form-control-busqueda"
            placeholder="Buscar"
            @keyup.enter="OnKeyUpSearch()"
          />
          <div class="input-group-append">
            <span class="input-group-text btn-barra-naranja">
              <a href="javascript:void(0)" class="aBuscar" @click="OnKeyUpSearch()">
                <i class="flaticon-381-search-2"></i>
              </a>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="table-responsive">
    <table class="table card-table display dataTablesCard">
      <thead>
        <tr class="">
          <th>Fecha</th>
          <th>Capacitación</th>
          <th>Duración (Min)</th>
          <th>Asesor experto</th>
          <th>Modalidad</th>
          <th>Tipo</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <template
          v-for="(t, index) in data.data_table"
          :key="t.ID_ACTIVIDAD_INICIADA"
        >
          <tr>
            <td>{{ t.fecha_agendamiento }}</td>
            <td>{{ t.capacitacion }}</td>
            <td>{{ t.duracion }}</td>
            <td>{{ t.asesor }}</td>
            <td>{{ t.modalidadLetras }}</td>
            <td>{{ t.tipoLetras }}</td>
            <td>
              <div class="d-flex justify-content-center align-items-center">
                <a
                  class="badge badge-primary"
                  style="color: white"
                  href="javascript:void(0)"
                  @click="OnClickOpenModalCrear(index)"
                  :id="'btn-asistida-editar-'+index"
                  v-if="permisos.includes('ent-asi-crear_asistida')"
                >
                  Editar
                </a>
                <a
                  class="badge badge-primary ml-1"
                  style="color: white"
                  href="javascript:void(0)"
                  @click="ViewPDF(index)"
                  :id="'btn-asistida-reporte-'+index"
                  v-if="permisos.includes('ent-asi-reporte')"
                >
                  Reporte
                </a>
                <a
                  class="badge badge-primary ml-1"
                  style="color: white"
                  href="javascript:void(0)"
                  @click="viewAssistents(index)"
                  :id="'btn-asistida-asistentes-'+index"
                  v-if="permisos.includes('ent-asi-asistentes')"
                >
                  Asistentes
                </a>
                <a
                  class="badge badge-primary ml-1"
                  style="color: white"
                  href="javascript:void(0)"
                  @click="OnClickOpenModalLink(t.link, index)"
                  :id="'btn-asistida-link-'+index"
                  v-if="permisos.includes('ent-asi-link')"
                >
                  Link
                </a>
              </div>
            </td>
          </tr>
        </template>
      </tbody>
    </table>
  </div>

  <nav class="mt-4 d-flex justify-content-center">
    <ul class="pagination pagination-circle">
      <li class="page-item page-indicator">
        <a class="page-link" href="javascript:void(0)" @click="previousPage">
          <i class="la la-angle-left"></i
        ></a>
      </li>

      <template v-for="(link, index) in data.paginate.links" :key="index">
        <template
          v-if="
            !(link.label.indexOf('Previous') > -1) &&
            !(link.label.indexOf('Next') > -1)
          "
        >
          <li class="page-item" :class="link.active ? 'active' : ''">
            <a
              class="page-link"
              href="javascript:void(0)"
              @click="numPage(link.label)"
              >{{ link.label }}</a
            >
          </li>
        </template>
      </template>

      <li class="page-item page-indicator">
        <a class="page-link" href="javascript:void(0)" @click="nextPage">
          <i class="la la-angle-right"></i
        ></a>
      </li>
    </ul>
  </nav>

  <!-- MODAL ASISTENTES -->
  <div
    class="modal fade"
    id="modal_assistant"
    ref="modal_assistant"
    data-toggle="modal"
    data-backdrop="static"
    data-keyboard="false"
    tabindex="-1"
    role="dialog"
    aria-hidden="true"
    style="overflow: scroll"
  >
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Listado de asistentes</h5>
          <div class="text-right">
            <button
                class="btn btn-barra-naranja"
                @click="downloadAllCertificates(data.cap_selected.id)"
                v-if="permisos.includes('ent-asi-descargar_certificados')"
            >
                Descargar certificados
            </button>
            <button
                class="btn btn-barra-naranja ml-1"
                @click="downloadAsistentesExcel(data.cap_selected.id)"
                v-if="permisos.includes('ent-asi-descargar_asistentes')"
            >
                Descargar asistentes
            </button>
            <button
                class="btn btn-barra-naranja ml-1"
                @click="openModalCargarAsistente(data.date_selected)"
                v-if="permisos.includes('ent-asi-cargar_asistentes')"
            >
                Cargar asistentes
            </button>
          </div>
        </div>
        <div class="modal-body">
          <div class="col-lg-12">
            <div class="table-responsive">
              <table class="table table-responsive-md">
                <thead>
                  <tr>
                    <th>Nombre</th>
                    <th>Documento</th>
                    <th></th>
                    <th class="align-middle text-center">Intentos</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="assistant in data.data_assist" :key="assistant.id">
                    <td>{{ assistant.NOMBRE_ASISTENTE }}</td>
                    <td>
                      {{
                        assistant.NUMERO_DOC_ASISTENTE
                      }}
                    </td>
                    <td>
                        <a
                          href="#"
                          class="badge badge-primary"
                          v-if="assistant.signature_path"
                          @click.prevent="openModalSignature(assistant.signature_path)">Firmado</a>
                    </td>
                    <td class="align-middle text-center">
                        <button class="badge ml-1" @click.prevent="openModalViewInstantes(assistant)"
                        :class="Object.keys(assistant.idsEvaluacion).length > 0 ? 'badge-primary':'badge-success'">
                            {{ assistant.intentos.intentos }}
                            <span v-if="data.cap_selected.permitir_certificacion == 1 && data.cap_selected.evaluara_por == 2" > de {{ assistant.intentos.gano }}</span>
                        </button>
                    </td>
                    <td>
                      <div class="d-flex">
                        <a
                          href="#"
                          class="badge badge-primary"
                          v-show="Object.keys(assistant.idsEvaluacion).length > 0 && assistant.certifica == 1"
                          @click.prevent="
                            downloadCertificate(
                                assistant.idsEvaluacion,
                                assistant.ID_ASISTENTE
                            )
                          "
                          >Certificado</a>
                          <div
                          class="badge badge-success"
                          v-show="Object.keys(assistant.idsEvaluacion).length == 0 || (Object.keys(assistant.idsEvaluacion).length > 0 && assistant.certifica == 0)"
                          >Asistente</div>
                      </div>
                    </td>
                  </tr>
                  <tr v-if="data.data_assist.length == 0">
                    <td colspan="4" class="text-center font-weight-bold">
                      No tienes datos
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button
            type="button"
            class="btn btn-danger light"
            @click="OnCloseModalAssistant"
          >
            Cerrar
          </button>
        </div>
      </div>
    </div>
  </div>
  <!-- MODAL ASISTENTES END-->

  <!-- MODAL ASISTENTES -->
  <div
    class="modal fade"
    id="modal_cargar_assistant"
    ref="modal_cargar_assistant"
    data-toggle="modal"
    data-backdrop="static"
    data-keyboard="false"
    tabindex="-1"
    role="dialog"
    aria-hidden="true"
  >
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Cargar asistentes</h5>
        </div>
        <div class="modal-body">
          <ul class="nav nav-tabs mb-3">
            <li class="nav-item">
              <a class="nav-link" :class="{'active': data.tabSelect == 1}" href="#" @click.prevent="tabCargar(1)">Excel</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" :class="{'active': data.tabSelect == 2}" href="#" @click.prevent="tabCargar(2)">Formulario</a>
            </li>
          </ul>
          <div v-show="data.tabSelect == 2">
            <div class="row">
                <div class="col-lg-12">
                    <button
                        type="button"
                        class="btn btn-primary float-right mb-1"
                        @click="OnClickAddCargar()"
                        v-show="data.tabSelect == 2"
                    >
                        Agregar otro asistente
                    </button>
                </div>
            </div>
            <div
              class="alert alert-warning alert-dismissible fade show"
              role="alert"
              v-show="data.errorValidationCargar"
            >
                  Los campos con (*) son obligatorios, asegurese de diligenciarlos.
            </div>
            <div class="row" v-for="(cargar, key) in data.formCargar" :key="key">
                <div class="mb-3 row" :class="data.classFormCargar">
                    <label for="name" class="col-form-label">
                        Nombre completo: <span style="color: red">*</span>
                    </label>
                    <div class="col-sm-12">
                        <div class="form-group">
                        <input
                            type="text"
                            class="form-control input-default"
                            v-model="cargar.nombreCom.val"
                        />
                        </div>
                    </div>
                </div>
                <div class="mb-3 row" :class="data.classFormCargar">
                    <label for="name" class="col-form-label">
                        Número de documento: <span v-show="cargar.documento.required" style="color: red">*</span>
                    </label>
                    <div class="col-sm-12">
                        <div class="form-group">
                        <input
                            type="text"
                            class="form-control input-default"
                            v-model.lazy="cargar.documento.val"
                            @input="validarNumeros"
                        />
                        </div>
                    </div>
                </div>
                <div class="mb-3 row" :class="data.classFormCargar">
                    <label for="name" class="col-form-label">
                        Correo:
                    </label>
                    <div class="col-sm-12">
                        <div class="form-group">
                        <input
                            type="text"
                            class="form-control input-default"
                            v-model="cargar.email.val"
                        />
                        </div>
                    </div>
                </div>
                <div class="mb-3 row" :class="data.classFormCargar" v-if="cargar.empresa.required">
                    <label for="name" class="col-form-label">
                        Empresa: <span style="color: red">*</span>
                    </label>
                    <div class="col-sm-12">
                        <div class="form-group">
                        <input
                            type="text"
                            class="form-control input-default"
                            v-model="cargar.empresa.val"
                        />
                        </div>
                    </div>
                </div>
            </div>
          </div>
            <div v-show="data.tabSelect == 1">
                <div class="row justify-content-center">
                    <div class="form-group col-md-6 col-12">
                        <label> Adjuntar Excel: <span class="dev-required">*</span></label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" accept=".xlsx" @change="OnChangeFileContenXlsx" class="custom-file-input file_excel_content" ref="file_excel_content">
                                <label class="custom-file-label">{{ data.form_cargar_data.label_file }}</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mt-4">
                        <div class="input-group">
                            <a class="btn btn-primary btn-sm" :href="data.formCargar[0].empresa.required == false ? url+'assets/formatosCargueAsistidas/formato-privada.xlsx' : url+'assets/formatosCargueAsistidas/formato-publica.xlsx'" >Descargar formato</a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="modal-footer">
          <button
            type="button"
            class="btn btn-primary"
            @click="saveCargarAssistant()"
            v-show="data.tabSelect == 2"
          >
            Guardar
          </button>
          <button
            type="button"
            class="btn btn-primary"
            @click="saveCargarAssistantExcel()"
            v-show="data.tabSelect == 1"
          >
            Cargar excel
          </button>
          <button
            type="button"
            class="btn btn-danger light"
            @click="OnCloseModalCargarAssistant"
          >
            Cerrar
          </button>
        </div>
      </div>
    </div>
  </div>
  <!-- MODAL CARGAR ASISTENTES END-->

  <!-- MODAL CREAR ASISTIDA -->
  <div class="modal fade" id="modal_crear_asistida" data-toggle="modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-lg">
          <div class="modal-content">
              <div class="modal-header">
                  <div class="col-lg-12 d-flex justify-content-between align-items-center flex-wrap">
                      <h3>Crear capacitación asistida por experto</h3>
                  </div>
              </div>
              <div class="modal-body">
                <div class="form-wizard order-create">
                    <ul class="nav nav-wizard">
                        <li class="lineaMitad" :class="{'active': data.stepAsistida >= 2}"><a class="nav-link" href="#">
                            <span :class="{'active': data.stepAsistida >= 1}">1</span>
                        </a></li>
                        <li class="lineaMitad" :class="{'active': data.stepAsistida >= 3}"><a class="nav-link" href="#">
                            <span :class="{'active': data.stepAsistida >= 2}">2</span>
                        </a></li>
                        <li><a class="nav-link" href="#">
                            <span :class="{'active': data.stepAsistida == 3}">3</span>
                        </a></li>
                    </ul>
                </div>
                <div class="row m-auto" v-show="data.stepAsistida == 1">
                    <div
                        class="alert alert-warning alert-dismissible fade show col-lg-12"
                        role="alert"
                        v-show="data.errorValidation"
                    >
                        Los campos con (*) son obligatorios, asegurese de diligenciarlos.
                    </div>
                    <div class="mb-3 row col-lg-12">
                        <label for="name" class="col-form-label">
                            Capacitación: <span style="color: red">*</span>
                        </label>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <Select_Savk
                                ref="selectCap"
                                id="selectCap"
                                v-model="data.form.id_capacitacion.val"
                                :options="data.training"
                                :maxItem="20"
                                placeholder="Seleccione una capacitación"
                                @selected="OnSelectedTraining"
                                />
                            </div>
                        </div>
                    </div>
                    <div class="mb-3 row col-lg-6">
                        <label for="name" class="col-form-label">
                            Fecha: <span style="color: red">*</span>
                        </label>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <input type="text" class="form-control" id="date-format" ref="fecha_agendamiento">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3 row col-lg-6">
                        <label for="name" class="col-form-label">
                            Modalidad: <span style="color: red">*</span>
                        </label>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <Select_Savk
                                ref="selectModalidad"
                                id="selectModalidad"
                                v-model="data.form.modalidad.val"
                                :options="data.default.options_modalidad"
                                :maxItem="20"
                                placeholder="Seleccione una modalidad"
                                @selected="OnSelectedModalidad"
                                />
                            </div>
                        </div>
                    </div>
                    <div class="mb-3 row col-lg-6">
                        <label for="name" class="col-form-label">
                            Tipo: <span style="color: red">*</span>
                        </label>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <Select_Savk
                                ref="selectTipo"
                                id="selectTipo"
                                v-model="data.form.tipo.val"
                                :options="data.default.options_tipo"
                                :maxItem="20"
                                placeholder="Seleccione un tipo"
                                @selected="OnSelectedTipo"
                                />
                            </div>
                        </div>
                    </div>
                    <div class="mb-3 row col-lg-6" v-show="data.form.tipo.val === 2">
                        <label for="name" class="col-form-label">
                            Grupo empresa: <span style="color: red">*</span>
                        </label>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <Select_Savk
                                ref="selectGrupoEmpresa"
                                id="selectGrupoEmpresa"
                                v-model="data.form.id_grupo_empresa.val"
                                :maxItem="20"
                                :options="data.grupoEmpresa"
                                placeholder="Seleccione un grupo empresa"
                                @selected="OnSelectedGrupoEmpresa"
                                />
                            </div>
                        </div>
                    </div>
                    <div class="mb-3 row col-lg-6" v-show='data.form.id_grupo_empresa.val != null && data.form.tipo.val === 2'>
                        <label for="name" class="col-form-label">
                            Empresa: <span style="color: red">*</span>
                        </label>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <Select_Savk
                                ref="selectEmpresa"
                                id="selectEmpresa"
                                v-model="data.form.id_empresa.val"
                                :options="data.empresa"
                                :maxItem="20"
                                placeholder="Seleccione una empresa"
                                @selected="OnSelectedEmpresa"
                                />
                            </div>
                        </div>
                    </div>
                    <div class="mb-3 row col-lg-6" v-show="data.form.id_empresa.val != null && data.form.tipo.val === 2">
                        <label for="name" class="col-form-label">
                            Centro de costo: <span style="color: red">*</span>
                        </label>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <Select_Savk
                                ref="selectCliente"
                                id="selectCliente"
                                :maxItem="20"
                                v-model="data.form.id_cliente.val"
                                :options="data.centroCosto"
                                placeholder="Seleccione un tipo"
                                @selected="OnSelectedCC"
                                />
                            </div>
                        </div>
                    </div>
                    <div class="mb-3 row col-lg-6">
                        <label for="name" class="col-form-label">
                            Duración: <span style="color: red">*</span>
                        </label>
                        <div class="col-sm-12">
                            <div class="form-group">
                            <input
                                type="text"
                                class="form-control input-default"
                                v-model.lazy="data.form.duracion.val"
                                placeholder="Minutos"
                                @input="validarNumeros"
                            />
                            </div>
                        </div>
                    </div>
                    <div class="mb-3 row col-lg-6">
                        <label for="name" class="col-form-label">
                            Anfitrión cliente:
                        </label>
                        <div class="col-sm-12">
                            <div class="form-group">
                            <input
                                type="text"
                                class="form-control input-default"
                                v-model="data.form.anfitrion.val"
                                placeholder="Cliente"
                            />
                            </div>
                        </div>
                    </div>
                    <div class="mb-3 row col-lg-12">
                        <label for="name" class="col-form-label">
                            Detalle:
                        </label>
                        <div class="col-sm-12">
                            <div class="form-group">
                            <textarea cols="30" rows="3" class="form-control input-default"
                                v-model="data.form.observacion.val"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="m-auto" v-show="data.stepAsistida == 2">

                    <ul class="nav nav-tabs mb-3">
                        <li class="nav-item">
                        <a class="nav-link" :class="{'active': data.tabSelect == 1}" href="#" @click.prevent="tabCargar(1)">Excel</a>
                        </li>
                        <li class="nav-item">
                        <a class="nav-link" :class="{'active': data.tabSelect == 2}" href="#" @click.prevent="tabCargar(2)">Formulario</a>
                        </li>
                    </ul>
                    <div v-show="data.tabSelect == 2">
                        <div class="row">
                            <div class="col-lg-12">
                                <button
                                    type="button"
                                    class="btn btn-primary float-right mb-1"
                                    @click="OnClickAddCargar()"
                                    v-show="data.tabSelect == 2"
                                >
                                    Agregar otro asistente
                                </button>
                            </div>
                        </div>
                        <div
                        class="alert alert-warning alert-dismissible fade show"
                        role="alert"
                        v-show="data.errorValidationCargar"
                        >
                            Los campos con (*) son obligatorios, asegurese de diligenciarlos.
                        </div>
                        <div class="row" v-for="(cargar, key) in data.formCargar" :key="key">
                            <div class="mb-3" :class="data.classFormCargar">
                                <label for="name" class="col-form-label">
                                    Nombre completo: <span style="color: red">*</span>
                                </label>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                    <input
                                        type="text"
                                        class="form-control input-default"
                                        v-model="cargar.nombreCom.val"
                                    />
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3" :class="data.classFormCargar">
                                <label for="name" class="col-form-label">
                                    Número de documento: <span v-show="cargar.documento.required" style="color: red">*</span>
                                </label>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                    <input
                                        type="text"
                                        class="form-control input-default"
                                        v-model.lazy="cargar.documento.val"
                                        @input="validarNumeros"
                                    />
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3" :class="data.classFormCargar">
                                <label for="name" class="col-form-label">
                                    Correo:
                                </label>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                    <input
                                        type="text"
                                        class="form-control input-default"
                                        v-model="cargar.email.val"
                                    />
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3" :class="data.classFormCargar" v-if="cargar.empresa.required">
                                <label for="name" class="col-form-label">
                                    Empresa: <span style="color: red">*</span>
                                </label>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                    <input
                                        type="text"
                                        class="form-control input-default"
                                        v-model="cargar.empresa.val"
                                    />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-show="data.tabSelect == 1">
                        <div class="row justify-content-center">
                            <div class="form-group col-md-6 col-12">
                                <label> Adjuntar Excel: <span class="dev-required">*</span></label>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" accept=".xlsx" @change="OnChangeFileContenXlsx" class="custom-file-input file_excel_content" ref="file_excel_content">
                                        <label class="custom-file-label">{{ data.form_cargar_data.label_file }}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mt-4">
                                <div class="input-group">
                                    <a class="btn btn-primary btn-sm" :href="data.formCargar[0].empresa.required == false ? url+'assets/formatosCargueAsistidas/formato-privada.xlsx' : url+'assets/formatosCargueAsistidas/formato-publica.xlsx'" >Descargar formato</a>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
                <div class="m-auto" v-show="data.stepAsistida == 3">
                    <div class="col-lg-12 d-flex justify-content-center mb-3">
                        <h5>Evidencias</h5>
                    </div>
                    <div class="col-lg-12 d-flex justify-content-center">
                        <div class="form-group col-md-6">
                            <label> Adjuntar imagen: <span class="dev-required">*</span></label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" accept="image/*" @change="OnChangeFileContenImagen" class="custom-file-input" ref="file_imagen_content" multiple>
                                    <label class="custom-file-label">{{ data.form_cargar_data.label_fileImg }}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <template v-for="(img, index) in data.cap_selected.img" :key="index">
                            <div class="col-md-3">
                                <div class="card p-0" style="background-color: rgb(0, 47, 84) !important;">
                                    <div class="card-body p-3 justify-content-center">
                                        <div class="col-md-12 p-0">
                                            <img class="w-100 img-asistida" :src="url+img.path">
                                        </div>
                                        <div class="col-md-12 text-center mt-2">
                                            <a href="javascript:void(0)" @click="deleteAsistidaImagen(img.id)">
                                                <i class="la la-trash dev-fonts-icon text-white"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>

                    </div>
                </div>
              </div>
              <div class="modal-footer">
                    <!-- BOTON PARA CREAR ASISTIDA -->
                    <button type="button" class="btn btn-primary light" @click="saveAsistida" v-if="data.stepAsistida == 1">Siguiente</button>
                    <!-- END BOTON PARA CREAR ASISTIDA -->

                    <button type="button" class="btn btn-primary light" @click="atrasAsistida(data.stepAsistida-1)" v-if="data.stepAsistida != 1">Atras</button>

                    <!-- BOTONES PARA CARGAR ASISTENTE EXCEL O FORM -->
                    <!-- form -->
                    <button
                        type="button"
                        class="btn btn-primary"
                        @click="siguenteStepAsistidaForm(1)"
                        v-show="data.tabSelect == 2 && data.stepAsistida == 2"
                    >
                        Siguiente
                    </button>
                    <!-- excel -->
                    <button
                        type="button"
                        class="btn btn-primary"
                        @click="siguenteStepAsistidaForm(2)"
                        v-show="data.tabSelect == 1 && data.stepAsistida == 2"
                    >
                        Siguiente
                    </button>
                    <!-- END BOTONES PARA CARGAR ASISTENTE EXCEL O FORM -->

                    <!-- BOTON PARA GUARDAR IMAGEN -->
                    <button type="button" class="btn btn-primary light" @click="saveAsistidaImagen" v-if="data.stepAsistida == 3">Finalizar</button>
                    <!-- END BOTON PARA GUARDAR IMAGEN -->

                    <button type="button" class="btn btn-danger light" @click="OnCloseModalCrear">Cerrar</button>
              </div>
          </div>
      </div>
  </div>
  <!-- MODAL CREAR ASISTIDA END -->

  <!-- MODAL LINK CAPACITACIÓN -->
  <div
    class="modal fade"
    id="modal_link"
    tabindex="-1"
    role="dialog"
    aria-hidden="true"
    ref="modal_link"
  >
    <div class="modal-dialog modal-xs">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modal-title-create-company">
            Compartir link
          </h5>
          <button type="button" class="close" @click="closeModalLink">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <div class="row mt-2">
                <p>Copia este link y compártelo con tu público.</p>
            </div>
            <div class="form-group">
                <div class="input-group">
                <input
                    type="text"
                    class="form-control input-default"
                    v-model="data.link"
                    id=""
                    disabled
                />
                <div class="input-group-append">
                    <button
                    type="button"
                    class="btn btn-danger light"
                    @click="CopyLink(data.link, this.$refs.modal_link)"
                    >
                    Copiar link
                    </button>
                </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-10 offset-md-1">
                    <img class="w-100 h-100" :src="url+data.qr" alt="Código qr">
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 text-center">
                    <a :href="url+data.qr" :download="'QR_'+data.cap_selected.capacitacion">
                        <button
                            type="button"
                            class="btn btn-primary btn-sm">
                            Descargar código
                        </button>
                    </a>
                </div>
            </div>
        </div>
        <div class="modal-footer">
          <button
            type="button"
            class="btn btn-danger light"
            @click="closeModalLink"
          >
            Cerrar
          </button>
        </div>
      </div>
    </div>
  </div>
  <!--END MODAL LINK CAPACITACIÓN -->

  <!-- POP UP FIRMA -->
  <div class="modal fade" id="modal_signature" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title pull-left">Firma</h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <img class="w-100 h-100" :src="url+data.signature_path" alt="Captured Image" v-if="data.signature_path">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                </div>

            </div>
        </div>
    </div>
    <!-- POP UP FIRMA - FIN -->

    <!-- MODAL VER INTENTOS -->
    <div
        class="modal fade"
        id="modal_view_instantes"
        tabindex="-1"
        role="dialog"
        aria-hidden="true"
        ref="modal_view_instantes"
    >
        <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="modal-title-create-company">
                Intentos
            </h5>
            <button type="button" class="close" data-dismiss="modal">
                <span>&times;</span>
            </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="tableSecciones" class="table card-table display dataTablesCard">
                        <thead>
                            <tr>
                                <th v-if="data.dataSelect.evaluara_por == 2">Módulo</th>
                                <th>Fecha</th>
                                <th>Calificación</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="data.dataIntentos.length == 0">
                                <td>
                                    No se generó evaluación para la certificación.
                                </td>
                            </tr>
                            <template v-for="(intento, index) in data.dataIntentos" :key="index">
                            <tr>
                                <td v-if="data.dataSelect.evaluara_por == 2">{{ intento.nombre }}</td>
                                <td>{{ intento.fecha_terminada }}</td>
                                <td>
                                    <div class="row">
                                        <div class="col-md-6 pt-2">{{ intento.resultado }}</div>
                                        <div class="col-md-4 pl-1">
                                            <i v-if="parseFloat(intento.resultado) >= parseFloat(intento.aprobacion)" class="bi bi-check-circle-fill icon-intentos"></i>
                                            <i v-if="parseFloat(intento.resultado) < parseFloat(intento.aprobacion)" class="bi bi-x-circle-fill icon-intentos"></i>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <button
                                        type="button"
                                        class="btn btn-primary btn-sm"
                                        @click="openModalViewEvaluacion(intento)"
                                    >
                                        Ver
                                    </button>
                                </td>
                            </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
            <button
                type="button"
                class="btn btn-danger light"
                data-dismiss="modal"
            >
                Cerrar
            </button>
            </div>
        </div>
        </div>
  </div>
  <!-- MODAL VER INTENTOS FIN -->

  <!-- MODAL VER EVALUACIÓN -->
  <div
        class="modal fade"
        id="modal_view_evaluacion"
        tabindex="-1"
        role="dialog"
        aria-hidden="true"
        ref="modal_view_evaluacion"
    >
        <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="modal-title-create-company">
                Evaluación
            </h5>
            <button type="button" class="close" data-dismiss="modal">
                <span>&times;</span>
            </button>
            </div>
            <div class="modal-body">
                <CardTestComponent :data_test="data.questions" :view="true" v-if="data.questions.length > 0"></CardTestComponent>
            </div>
            <div class="modal-footer">
            <button
                type="button"
                class="btn btn-danger light"
                data-dismiss="modal"
            >
                Cerrar
            </button>
            </div>
        </div>
        </div>
  </div>
</template>


<script>
import Select_Savk from "../../../../../../resources/js/components/pages/otros/Select_Savk.vue";
import { validarNumeros, guiaGetAll, saveVisualizacionGuia, CreateTour, guiasEspecificas  } from "../../../../../../public/assets/js/functions.js";
import CardTestComponent from "../TrainingExcecution/CardTestComponent.vue";

export default {
    components:{
        Select_Savk,
        CardTestComponent
    },
    async mounted() {
        this.GetDataInit();
        await this.guiaGetAll();
        this.CreateTour(this.guias);
        this.tour.start();
    },
    created(){
        this.getTraining()
        this.getGrupoEmpresa()
    },
    data() {
        return {
            permisos : JSON.parse(localStorage.getItem('permisos')),
            guias: [],
            guiasSecundarias: [],
            tour: null,
            token: document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
            url: document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("url"),
            data: {
                default:{
                    options_modalidad:[{
                        id: 1,
                        name: "Virtual",
                    },
                    {
                        id: 2,
                        name: "Presencial",
                    }],
                    options_tipo:[{
                        id: 1,
                        name: "Pública",
                    },
                    {
                        id: 2,
                        name: "Privada",
                    }],
                },
                dataIntentos: [],
                dataSelect: [],
                questions: [],
                signature_path: null,
                stepAsistida: 1,
                link : '',
                qr: '',
                tabSelect: 1,
                cap_selected: [],
                data_table: [],
                data_assist: [],
                paginate: {
                cant: 10,
                total: 1,
                current_page: 1,
                links: [],
                },
                date_selected: "",
                errorValidation: false,
                errorValidationCargar: false,
                training: [],
                centroCosto: [],
                grupoEmpresa: [],
                empresa: [],
                classFormCargar : 'col-lg-4',
                form: {
                    fecha_agendamiento: { required: true, val: "" },
                    id_capacitacion: { required: true, val: "" },
                    modalidad: { required: true, val: "" },
                    tipo: { required: true, val: "" },
                    id_cliente: { required: true, val: null },
                    id_grupo_empresa: { required: true, val: null },
                    id_empresa: { required: true, val: null },
                    duracion: { required: true, val: "" },
                    id_asistida: { required: false, val: "" },
                    anfitrion: { required: false, val: "" },
                    observacion: { required: false, val: "" },
                    update: { required: false, val: false },
                },
                formCargar: [
                    {
                        documento: { required: true, val: "" },
                        nombreCom: { required: true, val: "" },
                        email: { required: false, val: "" },
                        idAsistida: { required: false, val: "" },
                        empresa: { required: false, val: "" },
                    }
                ],
                form_cargar_data:{
                    xlsx:{
                        val : '',
                        required: true
                    },
                    imagen:{
                        val : '',
                        required: true
                    },
                    label_file: 'Selecciona un Archivo',
                    label_fileImg: 'Selecciona un Archivo',
                },
                optionsFetch: (dataForm) => ({
                method: "POST",
                headers: {
                "Content-type": "application/json; charset=UTF-8",
                "X-CSRF-Token": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
                },
                body: JSON.stringify(dataForm),
            }),
            },
            inputSearch: "",
        };
    },
  methods: {
    guiaGetAll,
    saveVisualizacionGuia,
    CreateTour,
    validarNumeros,
    OnChangeFileContenXlsx(file)
    {
        if(file != undefined)
        {
            this.data.form_cargar_data.label_file = "1 archivo cargado";
            this.data.form_cargar_data.xlsx.val = file.target.files[0];
        }
        else
        {
            this.data.form_cargar_data.label_file = "Seleccionar un archivo";
            this.data.form_cargar_data.xlsx.val = "";
        }
    },

    OnClickRedirectNewTraining() {
        window.location.href = `${this.url}capacitaciones/administracion`;
    },

    async openModalViewInstantes(certificado) {
        try {
            if (Object.keys(certificado.idsEvaluacion).length == 0 || certificado.intentos.intentos == 0) {
                toastr.warning('No se encontró intentos.');
                return
            }

            let data = new FormData();

            loading();
            data.append('id_capacitacion', this.data.cap_selected.id_capacitacion);
            data.append('id_modulo', certificado.id_modulo);
            data.append('id_usuario', certificado.ID_ASISTENTE);
            data.append('tipo', this.data.cap_selected.tipo);
            let rs = await fetch(`${this.url}capacitaciones/certificados/get-instantes`, {
                method: "POST", body: data, headers: {
                    'X-CSRF-TOKEN': this.token
                }
            });
            let rd = await rs.json();
            loading(false);

            switch (rd.status) {
                case 200:
                    this.data.dataSelect = certificado
                    this.data.dataIntentos = rd.data;
                    $("#modal_view_instantes").modal("show");
                    break;

                default:
                    break;
            }

        }
        catch (error) {
            loading(false);
            console.error(`Error`);
        }
    },

    async openModalViewEvaluacion(intento) {
            try {
                let data = new FormData();
                if (this.data.cap_selected.evaluara_por == 1) { //CAPACITACIÓN GENERAL
                    data.append('id_module', null);
                    data.append('id_capacitacion', this.data.cap_selected.id_capacitacion);
                    data.append('id_evaluacion', intento.id)
                }else if (this.data.cap_selected.evaluara_por == 2) { // POR MÓDULOS
                    data.append('id_module', intento.id_modulo);
                    data.append('id_capacitacion', null);
                    data.append('id_evaluacion', intento.id)
                }

                loading();
                let rs = await fetch(`${this.url}capacitaciones/get_data_test_view`, {
                    method: "POST", body: data, headers: {
                        'X-CSRF-TOKEN': this.token
                    }
                });
                let rd = await rs.json();
                loading(false);

                switch (rd.responseCode) {
                    case 202:
                        this.data.questions = rd.data.questions;
                        $("#modal_view_evaluacion").modal("show");
                        break;

                    case 400:
                        toastr.warning('No se encontró examen.');
                        break;

                    default:
                        break;
                }

            }
            catch (error) {
                loading(false);
                console.error(`Error al consultar el examen para realizarse: ${error.message}`);

            }
        },

    OnChangeFileContenImagen(file)
    {
        if (file != undefined) {
            const maxSize = 3 * 1024 * 1024; // 3MB en bytes
            const files = file.target.files;

            let allFilesValid = true;

            for (let i = 0; i < files.length; i++) {
                if (files[i].size > maxSize) {
                allFilesValid = false;
                break;
                }
            }

            if (allFilesValid) {
                this.data.form_cargar_data.imagen.val = files;
                this.data.form_cargar_data.label_fileImg =
                files.length == 1
                    ? "1 archivo cargado"
                    : files.length + " archivos cargados";
            } else {
                swal({
                title: "Error de imagen",
                text: "El tamaño de una o más imágenes excede el límite de 3 MB.",
                type: "warning",
                showCancelButton: false,
                confirmButtonText: "Aceptar",
                cancelButtonText: "No",
                confirmButtonColor: "#1f3352",
                allowOutsideClick: false,
                });

                // Limpiar el campo de selección de archivos
                file.target.value = null;
            }
        }
        else
        {
            this.data.form_cargar_data.label_fileImg = "Seleccionar una archivo";
            this.data.form_cargar_data.imagen.val = "";
        }
    },
    tabCargar(view){
      this.data.tabSelect = view
    },
    CopyLink(url, element_save) {
      var c = document.createElement("textarea");
      c.value = url;
      c.style.maxWidth = "0px";
      c.style.maxHeight = "0px";
      element_save.appendChild(c);

      c.focus();
      c.select();
      document.execCommand("copy");
      element_save.removeChild(c);

      toastr.success("Link de invitación copiada.");
    },
    closeModalLink(e) {
      $("#modal_link").modal("hide");
    },

    async OnClickOpenModalLink(link, index) {

        this.data.cap_selected = this.data.data_table[index];
        const data_form = new FormData();
        data_form.append('link', link);

        let rs = await fetch(`${this.url}capacitaciones/asistida/generarQr`, { method: "POST", body: data_form, headers: {
            'X-CSRF-TOKEN': this.token
        }});
        let rd = await rs.json();

        const { responseCode, message, data } = rd;

        if (responseCode == '200') {
            this.data.qr = `storage/qr_codes/${data}`;
        }else{
            console.log('Fallo generando código QR');
        }

        this.data.link = link
        $("#modal_link").modal("show");
    },

    openModalSignature(path) {
        this.data.signature_path = path
        $("#modal_signature").modal("show");
    },

    OnSelectedTraining(item) {
      this.data.form.id_capacitacion.val = item.id;
    },
    OnSelectedModalidad(item) {
      this.data.form.modalidad.val = item.id;
    },
    OnSelectedTipo(item) {
        this.data.form.tipo.val = item.id;
        if(item.id == 2){
            this.$refs.selectEmpresa.Clear()
            this.$refs.selectCliente.Clear()
            this.$refs.selectGrupoEmpresa.Clear()
            this.data.form.id_grupo_empresa.required = true
            this.data.form.id_empresa.required = true
            this.data.form.id_cliente.required = true
        }else{
            this.data.form.id_grupo_empresa.required = false
            this.data.form.id_empresa.required = false
            this.data.form.id_cliente.required = false

            this.data.form.id_grupo_empresa.val = null
            this.data.form.id_empresa.val = null
            this.data.form.id_cliente.val = null
        }
    },
    OnSelectedCC(item) {
        this.data.form.id_cliente.val = item.id;
    },
    OnSelectedGrupoEmpresa(item) {
        this.data.form.id_grupo_empresa.val = item.id;
        if (this.data.form.id_empresa.val != null) {
            this.$refs.selectEmpresa.Clear()
        }
        if (this.data.form.id_cliente.val != null) {
            this.$refs.selectCliente.Clear()
        }
        this.getEmpresa()
    },
    OnSelectedEmpresa(item) {
        this.data.form.id_empresa.val = item.id;
        if (this.data.form.id_cliente.val != null) {
            this.$refs.selectCliente.Clear()
        }
        this.getCC()
    },
    async getTraining() {
      const response = await fetch(`${this.url}` + `capacitaciones/get_training`);
      const data = await response.json();
      this.data.training = data;
    },
    async getGrupoEmpresa() {
      const response = await fetch(`${this.url}` + `capacitaciones/get_grupo_empresa`);
      const data = await response.json();
      this.data.grupoEmpresa = data;
    },
    async getEmpresa() {
        // loading()
        const response = await fetch(`${this.url}` + `capacitaciones/get_empresa/${this.data.form.id_grupo_empresa.val}`);
        const data = await response.json();
        this.data.empresa = data;
        // loading(false)

    },
    async getCC() {
        // loading()
        const response = await fetch(`${this.url}` + `capacitaciones/get_centro_costo/${this.data.form.id_empresa.val}`);
        const data = await response.json();
        this.data.centroCosto = data;
        // loading(false)
    },
    async downloadAsistentesExcel(id) {
        if (this.data.data_assist.length == 0) {
            toastr.error("No tienes datos.");
            return
        }

        let answer = await Swal({
            title: 'Selecciona el formato',
            input: 'select',
            inputOptions: {
                'pdf': 'PDF',
                'excel': 'EXCEL',
            },
            inputPlaceholder: 'Selecciona',
            cancelButtonText: "Cancelar",
            confirmButtonText: "Aceptar",
            allowOutsideClick: false,
            confirmButtonColor: '#1f3352',
            cancelButtonColor: '#ff7f00',
            showCancelButton: false,
            inputValidator: function (value) {
                return new Promise(function (resolve, reject) {
                if (value !== '')
                    resolve();
                else
                    resolve('Debes seleccionar un formato');
                });
            }
        });
        let formato
        if(answer.value)
        {
            formato = answer.value
        }

        loading()
        window.open(`${this.url}capacitaciones/asistida/downloadAsistentes/${id}/${formato}`);
        loading(false)
    },
    async saveAsistidaImagen(){
        if (this.data.form_cargar_data.imagen.val === '' || this.data.form_cargar_data.imagen.val.length == 0) {
            let answer = await swal({
                title: "Evidencia no seleccionada",
                text: `Desea continuar sin agregar evidencias.`,
                type: "warning",
                showCancelButton: true,
                confirmButtonText: "Si",
                cancelButtonText: "No",
                confirmButtonColor: '#1f3352',
                cancelButtonColor: '#ff7f00',
                allowOutsideClick: false
            });

            if (answer.value) {
                this.OnCloseModalCrear()
            }
        }else{
            try {
                const data_form = new FormData();
                // Agregar las imágenes al FormData
                for (let i = 0; i < this.data.form_cargar_data.imagen.val.length; i++) {
                    data_form.append('archivos[]', this.data.form_cargar_data.imagen.val[i]);
                }
                data_form.append('idAsistida', this.data.cap_selected.id);

                let rs = await fetch(`${this.url}capacitaciones/asistida/cargarImg`, { method: "POST", body: data_form, headers: {
                    'X-CSRF-TOKEN': this.token
                }});
                let rd = await rs.json();

                const { responseCode, message } = rd;

                switch (responseCode)
                {
                    case 201:
                        swal({
                            title: "¡Exitoso!",
                            text: message,
                            type: "success",
                            showCancelButton: false,
                            confirmButtonText: "Aceptar",
                            cancelButtonText: "No",
                            confirmButtonColor: '#1f3352',
                            cancelButtonColor: '#ff7f00',
                            allowOutsideClick: false
                        });
                        await this.GetDataInit(
                            `${this.url}` +
                            "capacitaciones/get-data-all-assist-by-expert?page=" +
                            this.data.paginate.current_page
                        );
                        this.OnCloseModalCrear()
                        break;
                    default:
                        Swal.fire(
                        "Oops!!",
                        "Parece que algo no salio del todo bien.",
                        "warning"
                        );
                        break;
                }

            } catch (error) {
                console.log(error);
            }
        }
    },
    async deleteAsistidaImagen(id){
        try {
            let answer = await swal({
                text: `¿Está seguro que desea eliminar la evidencia?`,
                showCancelButton: true,
                confirmButtonText: "Si",
                cancelButtonText: "No",
                confirmButtonColor: '#1f3352',
                cancelButtonColor: '#ff7f00',
                allowOutsideClick: false
            });

            if (answer.value) {
                const data_form = new FormData();
                data_form.append('id', id);

                let rs = await fetch(`${this.url}capacitaciones/asistida/deleteImg`, { method: "POST", body: data_form, headers: {
                    'X-CSRF-TOKEN': this.token
                }});
                let rd = await rs.json();

                const { responseCode, message } = rd;

                switch (responseCode)
                {
                    case 201:
                        swal({
                            title: "¡Exitoso!",
                            text: message,
                            type: "success",
                            showCancelButton: false,
                            confirmButtonText: "Aceptar",
                            cancelButtonText: "No",
                            confirmButtonColor: '#1f3352',
                            cancelButtonColor: '#ff7f00',
                            allowOutsideClick: false
                        });
                        await this.GetDataInit(
                            `${this.url}` +
                            "capacitaciones/get-data-all-assist-by-expert?page=" +
                            this.data.paginate.current_page
                        );

                        this.data.cap_selected = this.data.data_table.find(item => item.id == this.data.cap_selected.id)
                        break;
                    default:
                        Swal.fire(
                        "Oops!!",
                        "Parece que algo no salio del todo bien.",
                        "warning"
                        );
                        break;
                }
            }else{
                return
            }
        } catch (error) {
            console.log(error);
        }
    },
    async atrasAsistida(step){
        switch (step) {
            case 1:
                this.data.form.id_asistida.val = this.data.cap_selected.id
                this.data.form.update.val = true
                this.data.stepAsistida = 1
                break;
            case 2:
                this.data.formCargar = [];
                this.data.formCargar.push({
                        documento: { required: this.data.cap_selected.tipo == 1 ? false : true, val: "" },
                        nombreCom: { required: true, val: "" },
                        email: { required: false, val: "" },
                        idAsistida: { required: false, val: "" },
                        empresa: { required: this.data.cap_selected.tipo == 1 ? true : false, val: "" },
                    });

                this.data.stepAsistida = 2
                break
            default:
                break;
        }
    },
    async saveAsistida(e) {
        this.data.errorValidation = false;

        if (this.$refs.fecha_agendamiento.value) {
            this.data.form.fecha_agendamiento.val = this.$refs.fecha_agendamiento.value
        }

        if (!this.validateForm(this.data.form)) {
            // this.data.errorValidation = true;
            swal({
                //title: "¡Exitoso!",
                text: "Los campos con (*) son obligatorios, asegúrese de diligenciarlos.",
                type: "warning",
                showCancelButton: false,
                confirmButtonText: "Aceptar",
                cancelButtonText: "No",
                confirmButtonColor: '#1f3352',
                cancelButtonColor: '#ff7f00',
                allowOutsideClick: false
            });
            return;
        }
        try {
            if (this.$refs.fecha_agendamiento.value.includes("/")) {
                const fecha = this.$refs.fecha_agendamiento.value
                const [date, time] = fecha.split(' ');
                const [year, month, day] = date.split('/');

                const formattedDate = `${year}-${month}-${day} ${time}`;
                this.data.form.fecha_agendamiento.val = formattedDate
            }
            //load(true);
            const response = await fetch(
            `${this.url}` + "capacitaciones/asistida/crear",
            this.data.optionsFetch({
                ...this.data.form,
            })
            );
            // //load(false);
            const resp = await response.json();

            switch (resp.status) {
            case 201:
                await this.GetDataInit(
                    `${this.url}` +
                    "capacitaciones/get-data-all-assist-by-expert?page=" +
                    this.data.paginate.current_page
                );
                // this.OnCloseModalCrear();
                this.data.stepAsistida = 2
                this.data.cap_selected.id = resp.data;
                this.data.cap_selected.tipo = this.data.form.tipo.val
                this.data.formCargar[0].empresa.required = this.data.cap_selected.tipo == 1 ? true : false
                this.data.formCargar[0].documento.required = this.data.cap_selected.tipo == 1 ? false : true
                this.data.classFormCargar = this.data.cap_selected.tipo == 1 ? 'col-lg-3' : 'col-lg-4'
                swal({
                    title: "¡Exitoso!",
                    text: resp.msg,
                    type: "success",
                    showCancelButton: false,
                    confirmButtonText: "Aceptar",
                    cancelButtonText: "No",
                    confirmButtonColor: '#1f3352',
                    cancelButtonColor: '#ff7f00',
                    allowOutsideClick: false
                });
                break;

            case 202:
                Swal.fire("Oops!!", resp.msg, "warning");
                break;

            default:
                Swal.fire(
                "Oops!!",
                "Parece que algo no salio del todo bien.",
                "warning"
                );
                break;
            }
        } catch (error) {
            console.log(error);
        }
    },

    isValidEmail(email) {
      // Expresión regular para validar el formato del correo electrónico
      const emailRegex = /^[\w-]+(\.[\w-]+)*@([\w-]+\.)+[a-zA-Z]{2,7}$/;
      return emailRegex.test(email);
    },

    OnClickAddCargar(){
        this.data.formCargar.push({
            documento: { required: this.data.cap_selected.tipo == 1 ? false : true, val: "" },
            nombreCom: { required: true, val: "" },
            email: { required: false, val: "" },
            idAsistida: { required: false, val: "" },
            empresa: { required: this.data.cap_selected.tipo == 1 ? true : false, val: "" },
        });
        // this.$emit('scroll_down', $(".dev_container_questions")[0]);
    },

    async siguenteStepAsistidaForm(num){
        if (num == 1) { //formulario
            let empty = true
            for (let i = 0; i < this.data.formCargar.length; i++) {
                const objeto = this.data.formCargar[i];
                for (let propiedad in objeto) {
                    if (objeto[propiedad].val !== '' && objeto[propiedad].required == true) {
                        // console.log(objeto[propiedad]);
                        empty = false; // Si encuentra algún valor no vacío, retorna false
                    }
                }
            }
            if (empty) {
                let answer = await swal({
                    title: "Formulario vacío",
                    text: `¿Desea continuar sin agregar asistentes?`,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Si",
                    cancelButtonText: "No",
                    confirmButtonColor: '#1f3352',
                    cancelButtonColor: '#ff7f00',
                    allowOutsideClick: false
                });

                if (answer.value) {
                    this.data.stepAsistida = 3
                }
            }else{
                this.saveCargarAssistant()
                if (!this.data.errorValidationCargar) {
                    this.data.stepAsistida = 3
                }
            }
        }else{//excel
            if (this.data.form_cargar_data.xlsx.val === '') {
                let answer = await swal({
                    title: "Excel no seleccionado",
                    text: `Desea continuar sin agregar asistentes.`,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Si",
                    cancelButtonText: "No",
                    confirmButtonColor: '#1f3352',
                    cancelButtonColor: '#ff7f00',
                    allowOutsideClick: false
                });

                if (answer.value) {
                    this.data.stepAsistida = 3
                }
            }else{
                this.saveCargarAssistantExcel()
                this.data.stepAsistida = 3
            }

        }
        //this.data.stepAsistida = 3
    },

    async saveCargarAssistant(e) {
        this.data.errorValidationCargar = false;
        // let error = false;

        this.data.formCargar.forEach((obj) => {
            obj.idAsistida.val = this.data.cap_selected.id;

            if (!this.validateForm(obj)) {
                this.data.errorValidationCargar = true;
                // error=true
            }

            if (obj.email.val != ''  && !this.isValidEmail(obj.email.val)) {
                toastr.error("Ingrese correo valido por favor.");
                // error=true
                this.data.errorValidationCargar = true;
            }
        });
        if (this.data.errorValidationCargar) {
            return
        }

        try {
            //load(true);
            const response = await fetch(
            `${this.url}` + "capacitaciones/asistida/cargar",
            this.data.optionsFetch({
                ...this.data.formCargar,
            })
            );
            // //load(false);
            const resp = await response.json();

            switch (resp.status) {
            case 201:
                await this.GetDataInit(
                    `${this.url}` +
                    "capacitaciones/get-data-all-assist-by-expert?page=" +
                    this.data.paginate.current_page
                );
                this.OnCloseModalCargarAssistant();
                this.OnCloseModalAssistant();
                swal({
                    title: "¡Exitoso!",
                    text: resp.msg,
                    type: "success",
                    showCancelButton: false,
                    confirmButtonText: "Aceptar",
                    cancelButtonText: "No",
                    confirmButtonColor: '#1f3352',
                    cancelButtonColor: '#ff7f00',
                    allowOutsideClick: false
                });
                return true
                break;

            case 202:
                Swal.fire("Oops!!", resp.msg, "warning");
                break;

            default:
                Swal.fire(
                "Oops!!",
                "Parece que algo no salio del todo bien.",
                "warning"
                );
                break;
            }
        } catch (error) {
            console.log(error);
        }
    },
    async saveCargarAssistantExcel(e) {

        if (this.data.form_cargar_data.xlsx.val === '') {
            toastr.error("Ingrese un excel por favor.");
            return
        }

        try {
            const data_form = new FormData();
            data_form.append('archivo', this.data.form_cargar_data.xlsx.val);
            data_form.append('idAsistida', this.data.cap_selected.id);
            //load(true);

            let rs = await fetch(`${this.url}capacitaciones/asistida/cargarExcel`, { method: "POST", body: data_form, headers: {
                'X-CSRF-TOKEN': this.token
            }});
            let rd = await rs.json();

            const { responseCode, message, data } = rd;

            switch (responseCode)
            {
                case 201:
                    await this.GetDataInit(
                        `${this.url}` +
                        "capacitaciones/get-data-all-assist-by-expert?page=" +
                        this.data.paginate.current_page
                    );
                    this.OnCloseModalCargarAssistant();
                    this.OnCloseModalAssistant();
                    swal({
                        title: "¡Exitoso!",
                        text: message,
                        type: "success",
                        showCancelButton: false,
                        confirmButtonText: "Aceptar",
                        cancelButtonText: "No",
                        confirmButtonColor: '#1f3352',
                        cancelButtonColor: '#ff7f00',
                        allowOutsideClick: false
                    });
                    break;
                case 202:
                    Swal.fire("Oops!!", message, "warning");
                    this.data.form_cargar_data.label_file = 'Selecciona un archivo'
                    document.querySelector('.file_excel_content').value = null;
                    break;
                default:
                    Swal.fire(
                    "Oops!!",
                    "Parece que algo no salio del todo bien.",
                    "warning"
                    );
                    this.data.form_cargar_data.label_file = 'Selecciona un archivo'
                    document.querySelector('.file_excel_content').value = null;
                    break;
            }

        } catch (error) {
            console.log(error);
        }
    },
    validateForm(validar) {
      let next = true;

      Object.keys(validar).forEach((el) => {
        if (
          (validar[el].val === "" ||
            validar[el].val === undefined) &&
          validar[el].required
        ) {
          next = false;
        }
      });
      return next;
    },

    async OnKeyUpSearch()
    {
        loading(true);
        this.GetDataInit();
        loading(false);
    },

    async GetDataInit(url = null) {
      url =
        url ?? `${this.url}` + `capacitaciones/get-data-all-assist-by-expert`;
      try {
        let data = new FormData();
        data.append('search', this.inputSearch);

        //loading();
        let rs = await fetch(url, {
          method: "POST",
          body: data,
          headers: {
            "X-CSRF-TOKEN": this.token,
          },
        });
        let rd = await rs.json();
        //loading(false);

        switch (rd.responseCode) {
          case 206:
            this.data.paginate.current_page = rd.data.current_page;
            this.data.paginate.total = rd.data.total;
            this.data.paginate.links = rd.data.links;
            this.data.data_table = rd.data.data;
            if (rd.message != 'Datos encontrados') {
                swal({
                    title: "Advertencia",
                    text: rd.message,
                    type: "warning",
                    showCancelButton: false,
                    confirmButtonText: "Aceptar",
                    cancelButtonText: "No",
                    confirmButtonColor: '#1f3352',
                    cancelButtonColor: '#ff7f00',
                    allowOutsideClick: false
                });
            }
            break;

          default:
            break;
        }
      } catch (error) {
        //loading(false);
        console.error(`Error al traer links: ${error.message}`);
      }
    },
    async viewAssistents(index) {
        await this.GetDataInit(
            `${this.url}` +
            "capacitaciones/get-data-all-assist-by-expert?page=" +
            this.data.paginate.current_page
        );
        this.data.cap_selected = this.data.data_table[index];
        this.data.date_selected = this.data.data_table[index].id_capacitacion;
        this.data.data_assist = this.data.data_table[index].asistentes;
        $("#modal_assistant").modal("show");
    },
    openModalCargarAsistente(){
        if (this.data.cap_selected.tipo == 1) {
            this.data.formCargar[0].empresa.required = true
            this.data.formCargar[0].documento.required =false
            this.data.classFormCargar = 'col-lg-3'
        }else{
            this.data.formCargar[0].empresa.required = false
            this.data.formCargar[0].documento.required =true
            this.data.classFormCargar = 'col-lg-4'
        }
        $("#modal_cargar_assistant").modal("show");
    },
    OnCloseModalAssistant() {
      $("#modal_assistant").modal("hide");
    },

    OnCloseModalCargarAssistant() {
      $("#modal_cargar_assistant").modal("hide");
      this.data.formCargar = [];
      this.data.formCargar.push({
            documento: { required: true, val: "" },
            nombreCom: { required: true, val: "" },
            email: { required: false, val: "" },
            idAsistida: { required: false, val: "" },
            empresa: { required: false, val: "" },
        });

      this.data.form_cargar_data.label_fileImg = 'Selecciona un archivo'
      this.data.form_cargar_data.label_file = 'Selecciona un archivo'
      document.querySelector('.file_excel_content').value = null;
      this.$refs.file_excel_content.value = '';
      this.$refs.file_imagen_content.value = '';
    },

    async OnClickOpenModalCrear(index = null) {
        if (index != null) {
            loading()
            this.data.cap_selected = this.data.data_table[index];
            //editar
            this.data.form.id_asistida.val = this.data.cap_selected.id
            this.data.form.update.val = true

            $("#date-format").val(this.data.cap_selected.fecha_agendamiento)

            let optionCap = this.data.training.find(item => item.id == this.data.cap_selected.id_capacitacion);
            optionCap ? this.$refs.selectCap.selectOption(optionCap) : null

            let optionModalidad = this.data.default.options_modalidad.find(item => item.id == this.data.cap_selected.modalidad);
            optionModalidad ? this.$refs.selectModalidad.selectOption(optionModalidad) : null

            let optionTipo = this.data.default.options_tipo.find(item => item.id == this.data.cap_selected.tipo);
            optionTipo ? this.$refs.selectTipo.selectOption(optionTipo) : null

            let optionGE = this.data.grupoEmpresa.find(item => item.id == this.data.cap_selected.id_grupo_empresa);
            optionGE ? this.$refs.selectGrupoEmpresa.selectOption(optionGE) : null

            optionGE ? await new Promise(resolve => {
                const interval = setInterval(() => {
                    if (this.data.empresa.length > 0) {
                        clearInterval(interval);
                        resolve();
                    }
                }, 100); // Intervalo de revisión en milisegundos
            }) : null;

            let optionE = this.data.empresa.find(item => item.id == this.data.cap_selected.id_empresa);
            optionE ? this.$refs.selectEmpresa.selectOption(optionE) : null

            optionE ? await new Promise(resolve => {
                const interval = setInterval(() => {
                    if (this.data.centroCosto.length > 0) {
                        clearInterval(interval);
                        resolve();
                    }
                }, 100); // Intervalo de revisión en milisegundos
            }) : null;

            let optionCC = this.data.centroCosto.find(item => item.id == this.data.cap_selected.id_cliente);
            optionCC ? this.$refs.selectCliente.selectOption(optionCC) : null

            this.data.form.duracion.val = this.data.cap_selected.duracion
            this.data.form.anfitrion.val = this.data.cap_selected.anfitrion_cliente
            this.data.form.observacion.val = this.data.cap_selected.observacion
            loading(false)
        }
        this.data.stepAsistida = 1
        $("#modal_crear_asistida").modal("show");
    },
    OnCloseModalCrear() {
        this.data.form = {
            fecha_agendamiento: { required: true, val: "" },
            id_capacitacion: { required: true, val: "" },
            modalidad: { required: true, val: "" },
            tipo: { required: true, val: "" },
            id_cliente: { required: true, val: null },
            id_grupo_empresa: { required: true, val: null },
            id_empresa: { required: true, val: null },
            duracion: { required: true, val: "" },
            id_asistida: { required: false, val: "" },
            anfitrion: { required: false, val: "" },
            observacion: { required: false, val: "" },
            update: { required: false, val: false },
        },
        this.$refs.selectEmpresa.Clear()
        this.$refs.selectCliente.Clear()
        this.$refs.selectGrupoEmpresa.Clear()
        this.$refs.selectTipo.Clear()
        this.$refs.selectModalidad.Clear()
        this.$refs.selectCap.Clear()
        this.$refs.fecha_agendamiento.value = ''

        $("#modal_crear_asistida").modal("hide");
        this.data.form_cargar_data.label_file = 'Selecciona un archivo'
        this.data.form_cargar_data.label_fileImg = 'Selecciona un archivo'
        document.querySelector('.file_excel_content').value = null;
        this.$refs.file_excel_content.value = '';
        this.$refs.file_imagen_content.value = '';
        this.data.stepAsistida = 1
    },

    previousPage() {
        if (this.data.paginate.current_page === 1) return;

        loading(true);

        this.data.paginate.current_page--;
        this.GetDataInit(
            `${this.url}` +
            "capacitaciones/get-data-all-assist-by-expert?page=" +
            this.data.paginate.current_page
        );
        loading(false);
    },

    nextPage() {

        if (this.data.paginate.current_page === this.data.paginate.total) return;

        loading(true);

        this.data.paginate.current_page++;
        this.GetDataInit(
            `${this.url}` +
            "capacitaciones/get-data-all-assist-by-expert?page=" +
            this.data.paginate.current_page
        );
        loading(false);
    },

    async numPage(num) {
      loading(true);
      await this.GetDataInit(
        `${this.url}` +
          "capacitaciones/get-data-all-assist-by-expert?page=" +
          num
      );
      loading(false);
    },
    async downloadAllCertificates(id) {
      loading();
      let existe = false
      this.data.data_assist.forEach(element => {
        if (Object.keys(element.idsEvaluacion).length > 0 ) {
          existe = true
        }
      });

      if (!existe) {
        toastr.error("No hay certificados disponibles en este momento.");
        loading(false);
        return
      }

      window.open(
        `${this.url}capacitaciones/download-all-certificates-by-assist-expert/${id}`,
        "_blank"
      );
      loading(false);
    },
    async downloadCertificate(id, id_asistente) {
      loading();

      const idsTest = JSON.stringify(id);

      if (this.data.cap_selected.tipoLetras == 'Pública') {
        window.open(
            `${this.url}capacitaciones/download-certificates-public/${encodeURIComponent(idsTest)}`,
            "_blank"
        );
      }else{
        window.open(
            `${this.url}capacitaciones/download-certificate-by-assist-expert/${encodeURIComponent(idsTest)}/${id_asistente}`,
            "_blank"
        );

      }

      loading(false);
    },
    ViewPDF(index)
    {
        loading();
        window.open(
            `${this.url}capacitaciones/download-report-vissit/${this.data.data_table[index].id}`,
            "_blank"
        );
        loading(false);
    }
  },
};
</script>

<style scoped>
.dev-icon-back {
  font-size: 25px;
  cursor: pointer;
}

.dev-icon-certificate {
  color: #fe634e;
  font-size: 24px;
  cursor: pointer;
}

.dev-bg {
  background: rgba(254, 99, 78, 0.05);
  border-radius: 1.25rem;
  padding: 14px 18px 14px 18px;
}

.btn-menu {
  margin-right: 5px;
  margin-top: 5px;
  margin-bottom: 5px;
}

.menu-cap {
  background-color: white;
  border-radius: 1.25rem;
  /* padding: 8px; */
  padding-left: 5px;
  padding-right: 5px;
  margin-bottom: 20px;
  margin-left: 0px !important;
  margin-right: 0px !important;
  align-items: center;
}

.div-busqueda {
  display: flex;
  margin-left: auto;
  width: 43%;
  margin-top: 5px;
  margin-bottom: 5px;
}

.btn-barra {
  padding: 0.6rem 0.8rem;
  background-color: #e6f0ff;
  border-color: #e6f0ff;
  color: #002f54;
  box-shadow: none !important;
}

.btn-barra:hover {
  background-color: #002f54;
  border-color: #002f54;
  color: #e6f0ff;
  box-shadow: none !important;
}

.btn-barra-activo {
  background-color: #002f54;
  border-color: #002f54;
  color: #e6f0ff;
  box-shadow: none !important;
}

.btn-barra-naranja a {
  color: #e6f0ff;
}

.btn-barra-naranja {
  background-color: #ff7f00;
  border-color: #ff7f00;
  color: white;
  /* box-shadow: none !important; */
}

.btn-barra-naranja:hover {
  background-color: #ff8000e0;
  color: white;
}

@media (max-width: 1125px) {
  .div-busqueda {
    /* margin-left: auto; */
    width: 100%;
    margin-top: 5px;
    margin-bottom: 5px;
  }
}
.dev-fonts-icon {
  font-size: 30px;
  margin-left: 0px;
}

.dev-fonts-icon:hover {
  color: red !important;
}

.form-control-busqueda {
    height: 45px!important;
}

dtp-select-day{
    margin-right: 0px !important
}

.nav-tabs .nav-link.active, .nav-tabs .nav-item.show .nav-link {
    color: white;
    background-color: #002f54;
    border-color: #002f54;
}

.nav-tabs {
    border-bottom: 1px solid #002f54;
}

a:hover {
    color: #ff7f00;
}

.sw>.nav {
    display: flex;
    flex-wrap: wrap;
    list-style: none;
    padding-left: 0;
    margin-top: 0;
}

.nav-wizard>li {
    flex-basis: 0;
    flex-grow: 1;
    text-align: center;
}

.nav-wizard > .lineaMitad:after {
    content: "";
    position: absolute;
    top: 50%;
    left: 50%;
    height: 3px;
    transform: translateY(-50%);
    background: #f1f1f1 !important;
    z-index: 0;
    width: 100%;
}

.nav-wizard > .lineaMitad.active:after {
    background: #ff7f00 !important;
}
.form-wizard > ul > li{
    position: relative;
}

.form-wizard .nav-wizard li .nav-link span {
    border: 2px solid #ff7f00;
    color: #ff7f00;
    background-color: #fff;
}

.form-wizard .nav-wizard li .nav-link span.active {
    border: 2px solid #ff7f00;
    color: white;
    background-color: #ff7f00;
}

.img-asistida{
    max-height: 110px;
    min-height: 110px;
    object-fit: cover;
    /* object-position: left top; */
}
</style>
