<template>
  <!-- <div class="header-mod">
    <button style="background-color: #002f54; font-color: white; border-color: #002f54" class="btn btn-primary" @click="openModalCreate()">
      Crear grupo empresa
    </button>
  </div> -->

  <div class="row justify-content-end col-lg-12 m-3">
    <!--<div class="d-flex col-lg-1">
      <select
        class="form-control default-select"
        v-model="data.paginate.cant"
        @change="changeCantPaginate"
      >
        <option>10</option>
        <option>50</option>
        <option>100</option>
      </select>
    </div> -->
  </div>

  <div class="table-responsive">
    <table
      id="tableOrganization"
      class="table card-table display dataTablesCard"
    >
      <thead>
        <tr class="">
          <th>Nombre</th>
          <th>Identificación</th>
          <th>Estado</th>
          <th v-if="permisos.includes('org-mio-propuesta_valor')">Propuesta valor</th>
          <th v-if="permisos.includes('org-mio-editar_grupo_empresas') || permisos.includes('org-mio-eliminar_grupo_empresas')"></th>
        </tr>
      </thead>
      <tbody>
        <template v-for="(emp, index) in data.data_table" :key="index">
          <tr>
            <td>{{ emp.nombre }}</td>
            <td>{{ emp.nit }}</td>
            <td>{{ emp.estado }}</td>
            <td v-if="permisos.includes('org-mio-propuesta_valor')">
                <template v-if="emp.mensajes">
                <div :class="emp.mensajes.filter(mensaje => mensaje.estado == 0).length > 0 ? 'badge badge-primary':'badge badge-success'" style="position: relative;">
                    <a
                        class="ml-2"
                    style="color: white; text-align: center;"
                    href="javascript:void(0)"
                    @click="openModalMensajes(index)"
                    >
                    Mensajes
                    </a>
                    <div
                        class="countPreguntas rounded-circle"
                        :class="emp.mensajes.filter(mensaje => mensaje.estado == 0).length > 0 ? 'countPreguntas1':'countPreguntas0'"
                    >{{ emp.mensajes.filter(mensaje => mensaje.estado == 0).length }}</div>
              </div>
              </template>
            </td>
            <td v-if="permisos.includes('org-mio-editar_grupo_empresas') || permisos.includes('org-mio-eliminar_grupo_empresas')">
              <div>
                <a
                  class="badge badge-primary"
                  style="color: white"
                  href="javascript:void(0)"
                  @click="edit(index)"
                  v-if="permisos.includes('org-mio-editar_grupo_empresas')"
                >
                  Editar
                </a>
                <a href="javascript:void(0)"
                 class="badge badge-primary"
                  style="color: white"
                 @click="deleteElem(emp.id)"
                 v-if="permisos.includes('org-mio-eliminar_grupo_empresas')">
                  Eliminar
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

  <!-- MODAL MY ORGANIZATION -->
  <div
    class="modal fade"
    id="modal_create_organization"
    tabindex="-1"
    role="dialog"
    aria-hidden="true"
    data-backdrop="static"
  >
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            {{
              data.mode === "create"
                ? "Crear Grupo Empresa"
                : "Editar Grupo Empresa"
            }}
          </h5>
          <button type="button" class="close" @click="closeModalCreate">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div
            class="alert alert-warning alert-dismissible fade show"
            role="alert"
            v-if="data.errorValidation"
          >
            Los campos con (*) son obligatorios, asegurese de diligenciarlos.
          </div>

          <div class="row m-auto" >
            <!-- INIT INPUT -->
            <div class="mb-3 row col-lg-4">
              <label for="name" class="col-form-label">
                Nombre: <span style="color: red">*</span></label
              >
              <div class="col-sm-12">
                <div class="form-group">
                  <input
                    type="text"
                    class="form-control input-default"
                    v-model="data.form.nombre.val"
                  />
                </div>
              </div>
            </div>
            <!-- END INPUT -->

            <!-- INIT INPUT -->
            <div class="mb-3 row col-lg-4">
              <label for="staticEmail" class="col-form-label"
                >Nit: <span style="color: red">*</span>
              </label>
              <div class="col-sm-12">
                <div class="form-group">
                  <input
                    type="number"
                    class="form-control input-default"
                    placeholder=""
                    v-model="data.form.nit.val"
                  />
                </div>
              </div>
            </div>
            <!-- END INPUT -->

            <div class="mb-3 row col-lg-4">
              <label for="staticEmail" class="col-form-label">
                Estado: <span style="color: red">*</span></label
              >
              <div class="col-sm-12">
                <Select_Savk
                  ref="selectEstado"
                  v-model="data.form.estado.val"
                  :options="data.estados"
                  :maxItem="20"
                  placeholder="Seleccione una opción"
                  @selected="OnSelectedEstado"
                />
              </div>
            </div>

            <div class="mb-3 row col-lg-4">
              <label for="staticEmail" class="col-form-label">
                Sector: <span style="color: red">*</span></label
              >
              <div class="col-sm-12">
                <Select_Savk
                  ref="selectSector"
                  v-model="data.form.sector.val"
                  :options="data.sectors"
                  :maxItem="20"
                  placeholder="Seleccione un sector"
                  @selected="OnSelectedSector"
                />
              </div>
            </div>

            <div class="mb-3 row col-lg-4">
              <label for="staticEmail" class="col-form-label"> Pais: <span style="color: red">*</span></label>
              <div class="col-sm-12">
                <Select_Savk
                  ref="selectPais"
                  v-model="data.form.pais.val"
                  :options="data.countries"
                  :maxItem="20"
                  placeholder="Seleccione un país"
                  @selected="OnSelectedPais"
                />
              </div>
            </div>

            <div class="mb-3 row col-lg-4">
              <label for="staticEmail" class="col-form-label">
                Departamento: <span style="color: red">*</span>
              </label>
              <div class="col-sm-12">
                <Select_Savk
                  ref="selectDepartamento"
                  v-model="data.form.departamento.val"
                  :options="data.departaments"
                  :maxItem="20"
                  placeholder="Seleccione un departamento"
                  @selected="OnSelectedDepartamento"
                />
              </div>
            </div>

            <div class="mb-3 row col-lg-4">
              <label for="staticEmail" class="col-form-label"> Ciudad: <span style="color: red">*</span></label>
              <div class="col-sm-12">
                <Select_Savk
                  ref="selectCiudad"
                  v-model="data.form.ciudad.val"
                  :options="data.cities"
                  :maxItem="20"
                  placeholder="Seleccione una ciudad"
                  @selected="OnSelectedCiudad"
                />
              </div>
            </div>

            <div class="mb-3 row col-lg-4">
              <label for="staticEmail" class="col-form-label">
                Asesores:
              </label>
              <div class="col-sm-12" v-show="data.is_admin == 1">
                <Select_Savk
                  ref="selectAsesor"
                  v-model="data.form.asesor.val"
                  :options="data.asesores"
                  :maxItem="20"
                  placeholder="Seleccione un asesor"
                  @selected="OnSelectedAsesor"
                />
              </div>
              <div class="col-sm-12" v-if="data.is_admin != 1">
                <div class="form-group">
                  <input
                    type="text"
                    disabled
                    class="form-control disabled"
                    v-model="data.form.asesorNombre.val"
                  />
                </div>
              </div>
            </div>
            <div class="mb-3 row col-lg-4">
              <label for="staticEmail" class="col-form-label">
                Asignación Líder(es):
              </label>
              <div class="col-sm-12">
                <select2-multiple-control
                 ref="selLideresGrupoEmpresa"
                 :value="data.form.lideres.val"
                 :options="data.lideres"
                  @change="myChangeEvent($event)"
               />
              </div>

            </div>


            <!-- <div class="mb-3 row col-lg-6">
                <label for="staticAsesor" class="col-form-label">
                    Asesor: </label>
                <div class="col-sm-12">
                    <select id="" class="single-select" x-ref="sel2Asesor">
                        <option value=" ">Seleccione un asesor</option>
                        <template v-for="asesor in data.asesores">
                            <option :value="asesor.id" >
                                {{asesor.nombre_com}}
                            </option>
                        </template>
                    </select>
                </div>
            </div> -->
            <div class="mb-3 row col-lg-4">
                <label for="staticEmail" class="col-form-label">
                    Logo:
                </label>
                <div class="col-sm-12">
                    <div class="form-group">
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" accept=".jpg, .jpeg, .png" @change="OnChangeFileContenImagen" class="custom-file-input" ref="image_logo_ge">
                                <label class="custom-file-label">{{ data.label_fileImg }}</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mb-3 row col-lg-4" v-if="data.form.logo_url.val">
                <label for="staticEmail" class="col-form-label">
                    Logo actual:
                </label>
                <div class="col-sm-12">
                    <img :src="url+'storage/'+data.form.logo_url.val" style="max-width: 170px;">
                </div>
            </div>
            <div class="mb-3 row col-lg-4">
                <label for="staticEmail" class="col-form-label">
                  Imagen del certificado:
                </label>
                <div class="col-sm-12">
                    <div class="form-group">
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" accept=".jpg, .jpeg, .png" @change="OnChangeFileContenImagenCertificado" class="custom-file-input" ref="image_logo_certificado">
                                <label class="custom-file-label">{{ data.label_fileImgCertificado }}</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mb-3 row col-lg-4" v-if="data.form.logo_url_certificado.val">
                <label for="staticEmail" class="col-form-label">
                  Imagen del certificado actual:
                </label>
                <div class="col-sm-12">
                    <img :src="url+'storage/'+data.form.logo_url_certificado.val" style="max-width: 170px;">
                </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" @click="createOrUpdate">
            {{ data.mode === "create" ? "Crear" : "Actualizar" }}
          </button>
          <button
            type="button"
            class="btn btn-danger light"
            @click="closeModalCreate"
          >
            Cerrar
          </button>
        </div>
      </div>
    </div>
  </div>
  <!-- END MODAL MY ORGANIZATION -->

  <!-- Modal Mensajes-->
<div class="modal fade" id="modal_mensajes" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Mensajes</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <button class="nav-link active" id="nav-pendientes-tab" data-toggle="tab" data-target="#nav-pendientes" type="button" role="tab" aria-controls="nav-pendientes" aria-selected="true">Pendientes</button>
                <button class="nav-link" id="nav-historial-tab" data-toggle="tab" data-target="#nav-historial" type="button" role="tab" aria-controls="nav-historial" aria-selected="false">Historial</button>
            </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane fade show active" id="nav-pendientes" role="tabpanel" aria-labelledby="nav-pendientes-tab">
                <div class="table-responsive">
                    <table id="tableMensajes" class="table card-table display dataTablesCard">
                        <thead>
                            <tr>
                                <th>Capacitación</th>
                                <th>Grupo empresa</th>
                                <th>Usuario</th>
                                <th>fecha</th>
                                <th>Pregunta</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="data.mensajes.filter(mensaje => mensaje.estado == 0).length == 0">
                                <td colspan="6">No tienes mensajes pendientes.</td>
                            </tr>
                            <template v-for="(mensaje, index) in data.mensajes" :key="index">
                            <tr v-if="mensaje.estado == 0">
                                <td>{{ mensaje.capacitacion }}</td>
                                <td>{{ data.grupoEmpresa }}</td>
                                <td>{{ mensaje.nombre_com }}</td>
                                <td>{{ mensaje.fecha_formateada }}</td>
                                <td>{{ mensaje.pregunta }}</td>
                                <td>
                                    <div>
                                    <a
                                    class="badge badge-primary"
                                    style="color: white"
                                    href="javascript:void(0)"
                                    @click="openModalResponderMensaje(index)"
                                    >
                                    {{ mensaje.estado_pregunta }}
                                    </a>
                                </div>
                                </td>
                            </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade" id="nav-historial" role="tabpanel" aria-labelledby="nav-historial-tab">
                <div class="table-responsive">
                    <table id="tableMensajesHistorial" class="table card-table display dataTablesCard">
                        <thead>
                            <tr>
                                <th>Capacitación</th>
                                <th>Grupo empresa</th>
                                <th>Usuario</th>
                                <th>fecha</th>
                                <th>Pregunta</th>
                                <th>Respuesta</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="data.mensajes.length == 0">
                                <td colspan="6">No tienes mensajes en el historial.</td>
                            </tr>
                            <template v-for="(mensaje, index) in data.mensajes" :key="index">
                            <tr v-if="mensaje.estado == 1">
                                <td>{{ mensaje.capacitacion }}</td>
                                <td>{{ data.grupoEmpresa }}</td>
                                <td>{{ mensaje.nombre_com }}</td>
                                <td>{{ mensaje.fecha_formateada }}</td>
                                <td>{{ mensaje.pregunta }}</td>
                                <td>{{ mensaje.respuesta }}</td>
                            </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
<!-- End Modal Mensajes-->

<!-- Modal responder Mensajes-->
<div class="modal fade" id="modal_responder_mensaje" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Respuesta</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
            <div class="row">
                <div class="form-group col-lg-12">
                    <label for="respuesta">Respuesta:</label>
                    <textarea v-model="data.respuestaPregunta" class="form-control" id="respuesta" rows="3"></textarea>
                </div>
            </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" @click="responderMensaje()">Enviar Respuesta</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
<!-- End Modal responder Mensaje-->
</template>

<script>
import Select_Savk from "../../../../../resources/js/components/pages/otros/Select_Savk.vue";
export default {
  components: {
    Select_Savk,
  },
  created() {
    this.getSectors();
    this.getCities();
    this.getLideres();
    this.getCountries();
    this.getAsesores();
    this.getDepartaments();
    this.getDataAll();
  },
  props: {
    search: String,
  },
  data() {
    return {
        permisos : JSON.parse(localStorage.getItem('permisos')),
      url: document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("url"),
      data: {
        respuestaPregunta: '',
        mensajes: [],
        indexMensajes: null,
        pregunta: [],
        grupoEmpresa: '',
        estados: [
          { id: 1, name: "Activo" },
          { id: 2, name: "Inactivo" },
        ],
        countries: [],
        cities: [],
        sectors: [],
        asesores: [],
        departaments: [],
        lideres: [],
        mode: "create",
        errorValidation: false,
        label_fileImg: 'Selecciona un Archivo',
        label_fileImgCertificado: 'Selecciona un Archivo',
        form: {
          id: { required: false, val: "" },
          nombre: { required: true, val: "" },
          nit: { required: true, val: "" },
          estado: { required: true, val: "" },
          sector: { required: true, val: "" },
          pais: { required: true, val: "" },
          departamento: { required: true, val: "" },
          ciudad: { required: true, val: "" },
          asesor: { required: false, val: "" },
          asesorNombre: { required: false, val: "" },
          lideres: {required: false, val: []},
          logo: { required: false, val: [] },
          logo_url: { required: false, val: "" },
          logoCertificado: { required: false, val: [] },
          logo_url_certificado: { required: false, val: "" },
        },
        modal_create: null,
        paginate: {
          cant: 10,
          total: 1,
          current_page: 1,
          links: [],
        },

        data_table: [],
        is_admin: 0,

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
    };
  },
  methods: {
    async getSectors() {
      const response = await fetch(`${this.url}` + "administration/sectores");
      const data = await response.json();
      this.data.sectors = data;
    },
    OnChangeFileContenImagen(file)
    {
        if (file != undefined) {
            const maxSize = 3 * 1024 * 1024; // 3MB en bytes
            const files = file.target.files[0];

            let allFilesValid = true;

            if (files.size > maxSize) {
                allFilesValid = false;
            }

            if (allFilesValid) {
                this.data.form.logo.val = file.target.files[0];
                this.data.label_fileImg = "1 archivo cargado";
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
            this.data.label_fileImg = "Seleccionar una archivo";
            this.data.form.logo.val = [];
        }
    },
    OnChangeFileContenImagenCertificado(file)
    {
        if (file != undefined) {
            const maxSize = 10 * 1024 * 1024; // 3MB en bytes
            const files = file.target.files[0];

            let allFilesValid = true;

            if (files.size > maxSize) {
                allFilesValid = false;
            }

            if (allFilesValid) {
                this.data.form.logoCertificado.val = file.target.files[0];
                this.data.label_fileImgCertificado = "1 archivo cargado";
            } else {
                swal({
                title: "Error de imagen",
                text: "El tamaño de una o más imágenes excede el límite de 10 MB.",
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
            this.data.label_fileImgCertificado = "Seleccionar una archivo";
            this.data.form.logoCertificado.val = [];
        }
    },
    async getLideres() {
      const response = await fetch(`${this.url}` + "administration/lideres-grupo-empresa");
      const data = await response.json();
      this.data.lideres = data;
    },

    async getCountries() {
      const response = await fetch(`${this.url}` + "administration/paises");
      const data = await response.json();
      this.data.countries = data;
    },

    async getDepartaments(country_id) {
      if (country_id === undefined || country_id == " ") {
        this.data.departaments = [];
        return;
      }

      const response = await fetch(
        `${this.url}` + `administration/departamentos/${country_id}`
      );
      const data = await response.json();
      this.data.departaments = data;
    },

    async getCities(departament_id) {
      if (departament_id === undefined || departament_id == " ") {
        this.data.cities = [];
        return;
      }

      const response = await fetch(
        `${this.url}` + `administration/ciudades/${departament_id}`
      );
      const data = await response.json();
      this.data.cities = data;
    },
    async getAsesores() {
      const response = await fetch(`${this.url}` + "administration/asesores");
      const data = await response.json();
      this.data.asesores = data;
    },
    //Obtengo todos los centros de costos para la tabla
    async getDataAll(url = null) {
      url = url ?? `${this.url}` + "administration/grupo-empresa/all";

      const response = await fetch(
        url,
        this.data.optionsFetch({
          filters: this.data.filters,
          search: this.search,
          paginate: this.data.paginate,
        })
      );
      const { status, data, admin} = await response.json();

      if (status != 200) {
        toastr.error("Hubo un error al obtener la información.");
        return;
      }

      this.data.paginate.current_page = data.current_page;
      this.data.paginate.total = data.total;
      this.data.paginate.links = data.links;
      this.data.data_table = data.data;
      this.data.is_admin = admin;
    },
    openModalCreate() {
      $("#modal_create_organization").modal("show");
    },
    closeModalCreate(e) {
        this.data.errorValidation = false;
        this.data.mode = "create";
        this.resetDataForm();
        $("#modal_create_organization").modal("hide");
    },
    async openModalMensajes(index) {
        this.data.indexMensajes = index;
        this.data.mensajes = this.data.data_table[index].mensajes;
        this.data.grupoEmpresa = this.data.data_table[index].nombre;

        // const response = await fetch(
        //   `${this.url}` + "administration/grupo-empresa/mensajes",
        //   this.data.optionsFetch({
        //     ...data_edit
        //   })
        // );
        // const resp = await response.json();
        $("#modal_mensajes").modal("show");
    },
    closeModalMensajes(e) {
      $("#modal_mensajes").modal("hide");
    },

    async openModalResponderMensaje(index) {
        this.data.pregunta = this.data.mensajes[index]
        $("#modal_responder_mensaje").modal("show");
    },
    closeModalRespuestaMensaje() {
      $("#modal_responder_mensaje").modal("hide");
    },

    async responderMensaje() {
        if (this.data.respuestaPregunta == '') {
            toastr.error("Por favor ingrese una respuesta.");
            return
        }

        this.data.pregunta.respuesta = this.data.respuestaPregunta;
        const response = await fetch(
          `${this.url}` + "administration/grupo-empresa/responder_mensajes",
          this.data.optionsFetch({
            ...this.data.pregunta
          })
        );
        const resp = await response.json();

        switch (resp.status) {
            case 200:
                await this.getDataAll()
                this.data.mensajes = this.data.data_table[this.data.indexMensajes].mensajes;
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
                $("#modal_responder_mensaje").modal("hide");
                break;

            default:
                toastr.error("Algo salió mal.");
                break;
        }
    },

    //Limpio los campos
    resetDataForm() {
      this.$refs.selectEstado.Clear();
      this.$refs.selectSector.Clear();
      this.$refs.selectPais.Clear();
      this.$refs.selectDepartamento.Clear();
      this.$refs.selectCiudad.Clear();
      this.$refs.selectAsesor.Clear();
      Object.keys(this.data.form).forEach((prop) => {
        if (Array.isArray(this.data.form[prop].val) ) {
            this.data.form[prop].val = [];
        }else{
            this.data.form[prop].val = "";
        }
      });
      this.data.label_fileImg = "Seleccionar una archivo";
      this.data.label_fileImgCertificado = "Seleccionar una archivo";
      this.$refs.image_logo_ge.value = "";
      this.$refs.image_logo_certificado.value = "";
    },

    validateForm() {
      let next = true;

      Object.keys(this.data.form).forEach((el) => {
        if ((this.data.form[el].val === "" || this.data.form[el].val == undefined || this.data.form[el].val.length == 0) && this.data.form[el].required) {
            next = false;
        }
      });
      return next;
    },

    async createOrUpdate(e) {
      this.data.errorValidation = false;

      if (!this.validateForm()) {
        //this.data.errorValidation = true;
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
        const formData = new FormData();
        // Añadir los campos del formulario al FormData
        for (const [key, field] of Object.entries(this.data.form)) {
            if ((key === 'logo' || key === 'logoCertificado') && field.val instanceof File) {
                // Si el campo es 'logo' y contiene un archivo, añádelo directamente
                formData.append(key, field.val);
            } else if (Array.isArray(field.val)) {
                // Si el campo es un array, añádelo uno por uno
                field.val.forEach((item, index) => {
                    formData.append(`${key}[${index}]`, item);
                });
            } else {
                // Para otros campos, simplemente añade el valor
                formData.append(key, field.val);
            }
        }
        formData.append('mode', this.data.mode);
        const response = await fetch(
            `${this.url}` + "administration/grupo-empresa/crear",{
                method: "POST",
                headers: {
                    "X-CSRF-Token": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                },
                body: formData,
            }
        );


        const resp = await response.json();

        switch (resp.status) {
          case 201:
            this.getDataAll();
            this.closeModalCreate(null);
            this.resetDataForm();
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

    formatDate(date) {
      const d = new Date(date);
      return d.toLocaleString("es-ES", {
        hour12: false,
        weekday: "short",
        hour: "2-digit",
        month: "long",
        year: "numeric",
      });
    },

    previousPage() {
      loading(true);
      if (this.data.paginate.current_page === 1) return;

      this.data.paginate.current_page--;
      this.getDataAll(
        `${this.url}` +
          "/administration/grupo-empresa/all?page=" +
          this.data.paginate.current_page
      );
      loading(false);
    },

    nextPage() {
      loading(true);
      if (this.data.paginate.current_page === this.data.paginate.total) return;

      this.data.paginate.current_page++;
      this.getDataAll(
        `${this.url}` +
          "/administration/grupo-empresa/all?page=" +
          this.data.paginate.current_page
      );
      loading(false);
    },

    async numPage(num) {
      loading(true);
      await this.getDataAll(
        `${this.url}` + "administration/grupo-empresa/all?page=" + num
      );
      loading(false);
    },

    changeCantPaginate() {
      this.getDataAll();
    },

    //Editar el Grupo Empresa
    async edit(index) {
        await this.openModalCreate();
      this.ejemplo = "Agrego desde el editar";
      let data_edit = this.data.data_table[index];
      this.data.mode = "edit";

      this.data.form.id.val = data_edit.id;

      this.data.form.nombre.val = data_edit.nombre;
      this.data.form.nit.val = data_edit.nit;
      this.data.form.estado.val = data_edit.estado_num;
      this.data.form.sector.val = data_edit.sector_id;
      this.data.form.pais.val = data_edit.pais_id;
      this.data.form.departamento.val = data_edit.departamento;
      this.data.form.ciudad.val = data_edit.ciudad_id;
      this.data.form.asesor.val = data_edit.asesor_id;
      this.data.form.asesorNombre.val = data_edit.asesor;
      this.data.form.lideres.val = data_edit.lideres;
      this.data.form.logo_url.val = data_edit.img_avatar
      this.data.form.logo_url_certificado.val = data_edit.img_certificado

      let option = this.data.estados.find(
        (item) => item.id == this.data.form.estado.val
      );
      if (option) this.$refs.selectEstado.selectOption(option);

      if (data_edit.pais_id != null) {
        option = await this.data.countries.find(
            (item) => item.id == this.data.form.pais.val
        );
        if (option) this.$refs.selectPais.selectOption(option);

        await this.getDepartaments(option.id)
        option = this.data.departaments.find(
            (item) => item.id == this.data.form.departamento.val
        );
        if (option) this.$refs.selectDepartamento.selectOption(option);

        option = this.data.cities.find(
            (item) => item.id == this.data.form.ciudad.val
        );
        if (option) this.$refs.selectCiudad.selectOption(option);
      }

      option = this.data.sectors.find(
        (item) => item.id == this.data.form.sector.val
      );
      if (option) this.$refs.selectSector.selectOption(option);

      option = this.data.asesores.find(
        (item) => item.id == this.data.form.asesor.val
      );
      if (option) this.$refs.selectAsesor.selectOption(option);
    },

    async deleteElem(id) {
      const confir = await Swal.fire({
        title: "Esta seguro de eliminarlo?",
        showDenyButton: false,
        showCancelButton: true,
        confirmButtonText: "Si",
        cancelButtonText: `No`,
      });

      if (confir.value) {
        const response = await fetch(
          `${this.url}` + "administration/grupo-empresa/eliminar",
          this.data.optionsFetch({
            id,
          })
        );

        const { status, msg } = await response.json();

        switch (status) {
          case 200:
          swal({
                    title: "¡Exitoso!",
                    text: msg,
                    type: "success",
                    showCancelButton: false,
                    confirmButtonText: "Aceptar",
                    cancelButtonText: "No",
                    confirmButtonColor: '#1f3352',
                    cancelButtonColor: '#ff7f00',
                    allowOutsideClick: false
                });
            this.getDataAll();
            break;

          case 202:
            Swal.fire("Oopss!", msg, "warning");
            break;
        }
      }
    },
    OnSelectedEstado(item) {
      this.data.form.estado.val = item.id;
    },
    OnSelectedPais(item) {
      this.data.form.pais.val = item.id;
    },
    OnSelectedDepartamento(item) {
      this.data.form.departamento.val = item.id;
    },
    OnSelectedCiudad(item) {
      this.data.form.ciudad.val = item.id;
    },
    OnSelectedSector(item) {
      this.data.form.sector.val = item.id;
    },
    OnSelectedAsesor(item) {
      this.data.form.asesor.val = item.id;
    },
    myChangeEvent(val){
        if (Array.isArray(val)) {
            this.data.form.lideres.val = val
        }
    }
  },
  watch: {
    "data.form.pais": {
      async handler(val) {
        // console.log(val);
        if (val.val != "" && val.val != null)
          await this.getDepartaments(val.val);
      },
      deep: true,
    },
    "data.form.departamento": {
      async handler(val) {
        // console.log(val);
        if (val.val != "" && val.val != null) await this.getCities(val.val);
      },
      deep: true,
    },
    search: {
      async handler(val) {
        loading(true);
        this.getDataAll();
        loading(false);
      },
      deep: true,
    },
  },
};
</script>

<style scoped>
.countPreguntas{
    padding-top: 3px;
    display: inline-block;
    width: 20px;
    height: 20px;
    color: white !important;
    position: absolute;
    top: -9px;
    border: solid 1px;
}

.countPreguntas1{
    background-color: #ff7f00;
}

.countPreguntas0{
    background-color: #002F54;
}

.nav-link.active{
    color: white !important;
    background-color: #002F54 !important;
    border-color: #002F54 !important;
}

button.nav-link{
    background-color: #e6f0ff !important;
    border-color: #e6f0ff !important;
    color: #002f54 !important;
}
</style>
