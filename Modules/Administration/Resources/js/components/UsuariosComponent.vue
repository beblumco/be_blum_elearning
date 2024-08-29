<template>
  <!-- <div class="header-mod">
    <button style="background-color: #002f54; font-color: white; border-color: #002f54" class="btn btn-primary" @click="openModalCreate">
      Crear usuario
    </button>
  </div> -->

  <div class="row justify-content-end col-lg-12 m-3">
    <!-- <div class="d-flex col-lg-1">
      <select
        class="form-control default-select"
        v-model="data.paginate.cant"
        @change="changeCantPaginate"
      >
        <option>10</option>
        <option>50</option>
        <option>100</option>
      </select>
    </div>-->
  </div>

  <div class="table-responsive">
    <table class="table card-table display dataTablesCard">
      <thead>
        <tr class="">
          <th>Nombre</th>
          <th>Correo</th>
          <th>Perfil</th>
          <th>Estado</th>
          <th id="tour_head_cc">Asignaciones</th>
          <th style="width: 20%;"></th>
        </tr>
      </thead>
      <tbody>
        <template v-for="(emp, index) in data.data_table" :key="emp.id">
          <tr>
            <td>{{ emp.nombre }}</td>
            <td>{{ emp.email }}</td>
            <td>{{ emp.nombre_perfil }}</td>
            <td>
              {{ emp.estado }}
            </td>
            <td class="text-center">
              <a
                href="#!"
                class="badge badge-warning"
                @click="viewPoints(emp.id)"
                >Ver</a
              >
            </td>
            <td>
              <div>
                <a
                  :id ="'badge-editar-usuario-'+index"
                  class="badge badge-primary"
                  style="color: white"
                  href="javascript:void(0)"
                  @click="edit(index)"
                  v-if="(permisos.includes('org-mio-editar_usuarios') && data.profile < emp.id_grupo) ||
                        (permisos.includes('org-mio-editar_usuarios') && emp.id == data.user_id) ||
                        (permisos.includes('org-mio-editar_usuarios') && data.profile == 44)"
                >
                  Editar
                </a>
                <a
                  href="javascript:void(0)"
                  class="badge badge-primary"
                  style="color: white"
                  @click="deleteElem(emp.id)"
                  v-if="(permisos.includes('org-mio-eliminar_usuarios') && data.profile < emp.id_grupo) ||
                        (permisos.includes('org-mio-eliminar_usuarios') && emp.id == data.user_id) ||
                        (permisos.includes('org-mio-editar_usuarios') && data.profile == 44)"
                >
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

  <div class="modal fade" id="modalPointsUser">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Puntos de Evaluación</h5>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-lg-6 border-right">
                    <h5 class="mb-2">Hago parte del centro de costo:</h5>
                    <ul style="display: list-item">
                        <template v-for="point in data.user_points" :key="point.id">
                            <li v-if="point.responsable == 0">{{ point.nombre }}</li>
                        </template>
                    </ul>
                </div>
                <div class="col-lg-6 pl-4" v-if="data.label_points != null">
                    <h5 class="mb-2">Soy responsable {{ data.label_points }}</h5>
                    <ul style="display: list-item">
                        <template v-for="point in data.user_points" :key="point.id">
                            <li v-if="point.responsable == 1">{{ point.nombre }}</li>
                        </template>
                    </ul>
                </div>
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

  <!-- MODAL CREAR USUARIO -->
  <div
    class="modal fade"
    id="modal_create_user"
    tabindex="-1"
    role="dialog"
    aria-hidden="true"
  >
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modal-title-create-company">
            {{ data.mode === "create" ? "Crear Usuario" : "Editar Usuario" }}
          </h5>
          <button type="button" class="close" @click="closeModalCreate">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div
            class="alert alert-warning alert-dismissible fade show"
            role="alert"
            v-show="data.errorValidation"
          >
            Los campos con (*) son obligatorios, asegurese de diligenciarlos.
          </div>

          <div class="row m-auto">
            <!-- INIT INPUT -->
            <div class="mb-3 row col-lg-6">
              <label for="name" class="col-form-label">
                Nombre completo: <span style="color: red">*</span></label
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

            <!-- <div class="mb-3 row col-lg-6">
              <label for="name" class="col-form-label" />
              Teléfono:
              <div class="col-sm-12">
                <div class="form-group">
                  <input
                    type="number"
                    class="form-control input-default"
                    v-model="data.form.tel.val"
                  />
                </div>
              </div>
            </div> -->
            <div class="mb-3 row col-lg-6">
              <label for="name" class="col-form-label">
                Número de identificación:
              </label
              >
              <div class="col-sm-12">
                <div class="form-group">
                  <input
                    type="number"
                    class="form-control input-default"
                    v-model="data.form.codigo.val"
                  />
                </div>
              </div>
            </div>

            <div class="mb-3 row col-lg-6">
              <label for="name" class="col-form-label">
                Correo: <span style="color: red">*</span></label
              >
              <div class="col-sm-12">
                <div class="form-group">
                  <input
                    type="email"
                    class="form-control input-default"
                    v-model="data.form.email.val"
                  />
                </div>
              </div>
            </div>

            <div class="mb-3 row col-lg-6">
              <label for="name" class="col-form-label">
                Contraseña: <span style="color: red">*</span></label
              >
              <div class="col-sm-12">
                <div class="form-group">
                  <input
                    type="password"
                    class="form-control input-default"
                    v-model="data.form.password.val"
                  />
                </div>
              </div>
            </div>

            <div class="mb-3 row col-lg-6">
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

            <div class="mb-3 row col-lg-6">
              <label for="staticEmail" class="col-form-label">
                Perfil: <span style="color: red">*</span></label>
              <div class="col-sm-12">
                <Select_Savk
                  ref="selectPerfil"
                  v-model="data.form.profile.val"
                  :options="data.profiles"
                  :maxItem="20"
                  placeholder="Seleccione un perfil"
                  @selected="OnSelectedPerfil"
                />
              </div>
            </div>

            <div class="form-group mb-3 row col-lg-6" v-if="data.profile == 44 && data.main_account_id != 2">
                <label for="can_to_approve" class="col-form-label">
                    Puede aprobar pedidos:
                </label>
                <div class="col-sm-12">
                    <div class="form-group">
                        <label class="radio-inline mr-3"><input type="radio" value="1" name="can_to_approve" v-model="data.form.can_to_approve.val"> Si</label>
                        <label class="radio-inline mr-3"><input type="radio" value="0" name="can_to_approve" v-model="data.form.can_to_approve.val"> No</label>
                    </div>
                </div>
            </div>

            <!-- <div
              class="mb-3 row col-lg-6"
              v-show="
                data.form.profile.val == 44 || data.form.profile.val == 45
              "
              x-transition
            >
              <label for="staticEmail" class="col-form-label">
                Empresas:
              </label>
              <div class="col-sm-12">
                <select2-multiple-control
                  ref="selectEmpresas"
                  :value="data.form.lider_empresas.val"
                  :options="data.companies"
                  @change="myChangeEventEmpresa($event)"
                />
              </div>
            </div> -->

            <div class="mb-3 row col-lg-6">
              <label for="staticEmail" class="col-form-label">
                Centro de costo asignado: <span style="color: red">*</span>
              </label>
              <div class="col-sm-12">
                <div>
                  <Select_Savk
                    ref="sel2Punto"
                    v-model="data.form.punto.val"
                    :options="data.evaluation_points"
                    :maxItem="20"
                    placeholder="Seleccione una opción"
                    @selected="OnSelectedPuntoEvaluacion"
                  />
                </div>
              </div>
            </div>

            <div class="mb-3 row col-lg-6">
              <label for="staticEmail" class="col-form-label">
                Sección:
              </label>
              <div class="row col-lg-12">
                <div class="col-lg-9 pr-0">
                  <Select_Savk
                    ref="sel2Seccion"
                    v-model="data.form.seccion.val"
                    :options="data.secciones"
                    :maxItem="20"
                    placeholder="Seleccione una opción"
                    @selected="OnSelectedSeccion"
                  />
                </div>
                <div class="col-lg-3 pl-0 pr-0 mt-2 text-right">
                    <button type="button" class="btn btn-primary btn-sm" @click="openModalcreateSeccion">
                        Crear
                    </button>
                </div>
              </div>
            </div>

            <!-- <div class="mb-3 row col-lg-6">
              <label for="staticEmail" class="col-form-label"> País: </label>
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
            </div> -->

            <!-- <div class="mb-3 row col-lg-6">
              <label for="staticEmail" class="col-form-label">
                Departamento:
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
            </div> -->

            <!-- <div class="mb-3 row col-lg-6">
              <label for="staticEmail" class="col-form-label"> Ciudad: </label>
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
            </div> -->
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
  <!-- END MODAL PUNTO EVALUACIÓN -->
  <!-- MODAL INVITAR USUARIO -->
  <div
    class="modal fade"
    id="modal_invitar_user"
    tabindex="-1"
    role="dialog"
    aria-hidden="true"
    ref="modal_invitar_user"
  >
    <div class="modal-dialog modal-xs">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modal-title-create-company">
            Invitar usuario
          </h5>
          <button type="button" class="close" @click="closeModalInvitar">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <p>
                Copia este link y compártelo con tu equipo
            </p>
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
                    id="button-copiar-link"
                    type="button"
                    class="btn btn-danger light"
                    @click="CopyLink(data.link, this.$refs.modal_invitar_user)"
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
                    <a :href="url+data.qr" download="QR_invitar_usuario.png">
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
            @click="closeModalInvitar"
          >
            Cerrar
          </button>
        </div>
      </div>
    </div>
  </div>

   <!-- MODAL CREAR SECCIÓN -->
   <div
    class="modal fade"
    id="modal_create_seccion"
    tabindex="-1"
    role="dialog"
    aria-hidden="true"
    ref="modal_create_seccion"
  >
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modal-title-create-company">
            Secciones
          </h5>
          <button type="button" class="close" @click="closeModalCreateSeccion">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <button class="nav-link active" id="nav-crear-tab" data-toggle="tab" data-target="#nav-crear" type="button" role="tab" aria-controls="nav-crear" aria-selected="true" @click="tapSelect(1)">Crear</button>
                    <button class="nav-link" id="nav-creados-tab" data-toggle="tab" data-target="#nav-creados" type="button" role="tab" aria-controls="nav-creados" aria-selected="false" @click="tapSelect(2)">Creados</button>
                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-crear" role="tabpanel" aria-labelledby="nav-crear-tab">
                    <div class="row justify-content-center">
                        <div class="mb-3 mt-3 col-lg-10">
                            <label for="name" class="col-form-label">
                                Sección: <span style="color: red">*</span></label
                            >
                            <div class="col-sm-12">
                                <div class="form-group">
                                <input
                                    type="text"
                                    class="form-control input-default"
                                    v-model="data.formSeccion.nombre.val"
                                />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="nav-creados" role="tabpanel" aria-labelledby="nav-creados-tab">
                    <div class="table-responsive">
                        <table id="tableSecciones" class="table card-table display dataTablesCard">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="6" v-if="data.secciones.length == 0">No tienes secciones creadas.</td>
                                </tr>
                                <template v-for="(seccion, index) in data.secciones" :key="index">
                                <tr>
                                    <td>
                                        <input
                                            :id = "'seccion-'+seccion.id"
                                            type="text"
                                            class="formSinBorde"
                                            :value="seccion.name"
                                        />
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <div>
                                                <a
                                                class="badge badge-primary"
                                                style="color: white"
                                                href="javascript:void(0)"
                                                @click="editarSeccion(seccion.id)"
                                                >
                                                Editar
                                                </a>
                                            </div>
                                            <div>
                                                <a
                                                class="badge badge-primary"
                                                style="color: white"
                                                href="javascript:void(0)"
                                                @click="eliminarSeccion(seccion.id)"
                                                >
                                                Eliminar
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" @click="createSeccion" v-if="data.tapSeccion == 1">
            Crear
          </button>
          <button
            type="button"
            class="btn btn-danger light"
            @click="closeModalCreateSeccion"
          >
            Cerrar
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Select_Savk from "../../../../../resources/js/components/pages/otros/Select_Savk.vue";
import { guiaGetAll, saveVisualizacionGuia, CreateTour, guiasEspecificas, isValidEmail } from "../../../../../public/assets/js/functions.js";
export default {
  components: {
    Select_Savk,
  },
  created() {
    this.getLinkInvitacion();
    this.getCities();
    this.getCountries();
    this.getDepartaments();
    this.getProfiles();
    this.getCompanies();
    this.getDataAll();
  },
  async mounted(){
    await this.guiaGetAll();
    this.CreateTour(this.guias);
    this.tour.start();
  },
  props: {
    search: String,
  },
  data() {
    return {
        permisos : JSON.parse(localStorage.getItem('permisos')),
        guias: [],
        guiasSecundarias: [],
        tour: null,
      url: document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("url"),
      token: document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
      data: {
        qr: '',
        tapSeccion : 1,
        estados: [
          { id: 1, name: "Activo" },
          { id: 2, name: "Inactivo" },
        ],
        link: "",
        countries: [],
        cities: [],
        evaluation_points: [],
        secciones: [],
        departaments: [],
        companies: [],
        profiles: [],
        mode: "create",
        errorValidation: false,
        user_points: [],
        label_points: null,
        formSeccion:{
            nombre: {required: true, val: "" }
        },
        form: {
          id: { required: false, val: "" },
          nombre: { required: true, val: "" },
          tel: { required: false, val: "" },
          codigo: { required: false, val: "" },
          email: { required: true, val: "" },
          estado: { required: true, val: "" },
          pais: { required: false, val: "" },
          departamento: { required: false, val: "" },
          ciudad: { required: false, val: "" },
          punto: { required: true, val: "" },
          password: { required: false, value: "" },
          profile: { required: true, val: "" },
          lider_empresas: { required: true, val: [] },
          seccion: { required: false, val: "" },
          can_to_approve: { required: false, val: 0 },
        },
        modal_create: null,
        paginate: {
          cant: 10,
          total: 1,
          current_page: 1,
          links: [],
        },
        data_table: [],
        profile: null,
        main_account_id: null,
        user_id: null,
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
    isValidEmail,
    guiaGetAll,
    saveVisualizacionGuia,
    CreateTour,
    guiasEspecificas,
    tapSelect(tap){
        this.data.tapSeccion = tap
    },
    async getCountries() {
      //load(true);
      const response = await fetch(`${this.url}` + "administration/paises");
      const data = await response.json();
      this.data.countries = data;
      //load(false);
    },

    async getDepartaments(country_id) {
      if (country_id === undefined || country_id == " ") {
        this.data.departaments = [];
        return;
      }
      //load(true);
      const response = await fetch(
        `${this.url}` + `administration/departamentos/${country_id}`
      );
      const data = await response.json();
      this.data.departaments = data;
      //load(false);
    },

    async getCities(departament_id) {
      if (departament_id === undefined || departament_id == " ") {
        this.data.cities = [];
        return;
      }
      //load(true);
      const response = await fetch(
        `${this.url}` + `administration/ciudades/${departament_id}`
      );
      const data = await response.json();
      this.data.cities = data;
      //load(false);
    },

    async getProfiles() {
      //load(true);
      const response = await fetch(`${this.url}` + `administration/perfiles`);
      const data = await response.json();
      this.data.profiles = data;
      //load(false);
    },

    async getCompanies() {
      //load(true);
      const response = await fetch(`${this.url}` + `administration/empresas`);
      const data = await response.json();

      this.data.companies = data.map(({ id, name }) => ({
        id: id,
        text: name,
      }));
      //load(false);
    },

    //Obtengo todas los puntos para el Select
    async getEvaluationPoints() {
      //load(true);
      const response = await fetch(
        `${this.url}` + "administration/puntos-evaluacion"
      );
      const data = await response.json();
      //load(false);
      return data;
    },

    async getSecciones() {
      //load(true);
      const response = await fetch(
        `${this.url}` + "administration/secciones"
      );
      const data = await response.json();
      //load(false);
      return data;
    },

    //Obtengo todas los puntos para el Select
    async getLinkInvitacion() {
      //load(true);
      const response = await fetch(
        `${this.url}` + "administration/usuarios/link-invitacion"
      );
      const data = await response.json();
      //load(false);
      this.data.link = data.data;
    },

    //Obtengo todos los centros de costos para la tabla
    async getDataAll(url = null) {
      url = url ?? `${this.url}` + "administration/usuarios/all";

      //load(true);
      const response = await fetch(
        url,
        this.data.optionsFetch({
          search: this.search,
          filters: this.data.filters,
          paginate: this.data.paginate,
        })
      );
      const { status, data, admin, perfil, user_id, main_account_id } = await response.json();
      //load(false);

      if (status != 200) {
        toastr.error("Hubo un error al obtener la información.");
        return;
      }

      this.data.paginate.current_page = data.current_page;
      this.data.paginate.total = data.total;
      this.data.paginate.links = data.links;
      this.data.data_table = data.data;
      this.data.profile = perfil;
      this.data.user_id = user_id;
      this.data.main_account_id = main_account_id;
    },

    async openModalCreate() {
        this.resetDataForm()
      this.data.evaluation_points = await this.getEvaluationPoints();
      this.data.secciones = await this.getSecciones();
      $("#modal_create_user").modal("show");

      //   this.data.form.profile.val = 4;
    },

    openModalcreateSeccion() {
      $("#modal_create_seccion").modal("show");
    },

    async OnClickOpenModalInvitar() {
        const data_form = new FormData();
        data_form.append('link', this.data.link);

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

        $("#modal_invitar_user").modal("show");
        //prueba
        // let guiasEspecificas = this.guiasEspecificas(['button-copiar-link']);
        // this.CreateTour(guiasEspecificas);
        // this.tour.start();
    },

    closeModalCreate(e) {
      this.data.errorValidation = false;
      this.data.mode = "create";
      $("#modal_create_user").modal("hide");
      this.resetDataForm();
    },

    closeModalInvitar(e) {
        $("#modal_invitar_user").modal("hide");
    },

    closeModalCreateSeccion(e) {
        this.data.formSeccion.nombre.val = '';
        $("#modal_create_seccion").modal("hide");
    },

    //Limpio los campos
    resetDataForm() {
      this.data.form.password.required = true;
      this.$refs.selectEstado.Clear();
      this.$refs.selectPerfil.Clear();
    //   this.$refs.selectPais.Clear();
    //   this.$refs.selectDepartamento.Clear();
    //   this.$refs.selectCiudad.Clear();
      this.$refs.sel2Punto.Clear();
      this.$refs.sel2Seccion.Clear();

      Object.keys(this.data.form).forEach((prop) => {
        if (prop === "punto" || prop === "lider_empresas") {
          this.data.form[prop].val = [];
        } else {
          this.data.form[prop].val = "";
        }
      });
      this.data.form.can_to_approve.val = 0;
    },

    validateForm() {
      let next = true;
      Object.keys(this.data.form).forEach((el) => {
        if (
          (this.data.form[el].val === "" ||
            this.data.form[el].val === undefined) &&
          this.data.form[el].required
        ) {
          next = false;
        }
      });
      return next;
    },

    async createSeccion(){
      if (this.data.formSeccion.nombre.val == '') {
        toastr.warning("Por favor llenar los campos.");
        return;
      }
      try {
        //load(true);
        const response = await fetch(
          `${this.url}` + "administration/seccion/crear",
          this.data.optionsFetch({
            ...this.data.formSeccion
          })
        );
        // //load(false);
        const resp = await response.json();

        switch (resp.status) {
          case 201:
            this.data.secciones = await this.getSecciones();
            this.data.formSeccion.nombre.val = '';
            this.closeModalCreateSeccion();
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

    async createOrUpdate(e) {
      this.data.errorValidation = false;

      if (!this.validateForm()) {
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
      if(!this.isValidEmail(this.data.form.email.val)){
        return;
      }
      try {
        //load(true);
        const response = await fetch(
          `${this.url}` + "administration/usuarios/crear",
          this.data.optionsFetch({
            ...this.data.form,
            mode: this.data.mode,
          })
        );
        // //load(false);
        const resp = await response.json();

        switch (resp.status) {
          case 201:
            this.getDataAll();
            this.closeModalCreate(null);
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
    previousPage() {
      loading(true);
      if (this.data.paginate.current_page === 1) return;

      this.data.paginate.current_page--;
      this.getDataAll(
        `${this.url}` +
          "administration/usuarios/all?page=" +
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
          "administration/usuarios/all?page=" +
          this.data.paginate.current_page
      );
      loading(false);
    },

    async numPage(num) {
      loading(false);
      await this.getDataAll(
        `${this.url}` + "administration/usuarios/all?page=" + num
      );
      loading(false);
    },

    changeCantPaginate() {
      this.getDataAll();
    },

    async getEvaluationPointsUser(user_id) {
      //load(true)
      const response = await fetch(
        `${this.url}` + "administration/usuarios/puntos/" + user_id
      );
      const res = await response.json();
      //load(false)
      return res.data;
    },

    //Editar el Grupo Empresa
    async edit(index) {
        await this.openModalCreate();
      this.data.form.password.required = false;
      this.data.form.password.val = "";
      let data_edit = this.data.data_table[index];

      this.data.mode = "edit";
      this.data.form.id.val = data_edit.id;

      this.data.form.nombre.val = data_edit.nombre;
      this.data.form.ciudad.val = data_edit.ciudad;
      this.data.form.estado.val = data_edit.estado_id;
      this.data.form.pais.val = data_edit.pais_id;
      this.data.form.departamento.val = data_edit.departamento_id;
      this.data.form.ciudad.val = data_edit.ciudad_id;
      this.data.form.email.val = data_edit.email ?? "";
      this.data.form.tel.val = data_edit.telefono;
      this.data.form.codigo.val = data_edit.codigo;
      this.data.form.punto.val = data_edit.id_punto;
      this.data.form.seccion.val = data_edit.id_seccion;
      this.data.form.profile.val = data_edit.id_grupo;
      this.data.form.can_to_approve.val = data_edit.can_to_approve;

      this.data.form.lider_empresas.val = data_edit.empresas_lider.map(
        (el) => el.id
      );

      let option = this.data.estados.find(
        (item) => item.id == this.data.form.estado.val
      );
      if (option) this.$refs.selectEstado.selectOption(option);

      option = this.data.profiles.find(
        (item) => item.id == this.data.form.profile.val
      );
      if (option) this.$refs.selectPerfil.selectOption(option);

      option = this.data.countries.find(
        (item) => item.id == this.data.form.pais.val
      );
      if (option) this.$refs.selectPais.selectOption(option);

      option = this.data.departaments.find(
        (item) => item.id == this.data.form.departamento.val
      );
      if (option) this.$refs.selectDepartamento.selectOption(option);

      option = this.data.cities.find(
        (item) => item.id == this.data.form.ciudad.val
      );
      if (option) this.$refs.selectCiudad.selectOption(option);

      option = this.data.evaluation_points.find(
        (item) => item.id == this.data.form.punto.val
      );
      if (option) this.$refs.sel2Punto.selectOption(option);

      option = this.data.secciones.find(
        (item) => item.id == this.data.form.seccion.val
      );
      if (option) this.$refs.sel2Seccion.selectOption(option);
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
          `${this.url}` + "administration/usuarios/eliminar",
          this.data.optionsFetch({
            id,
          })
        );

        const { status, msg } = await response.json();

        switch (status) {
          case 200:
            this.getDataAll();
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
            break;

          case 202:
            Swal.fire("Oopss!", msg, "warning");
            break;
        }
      }
    },

    async editarSeccion(id) {
      const confir = await Swal.fire({
        title: "Esta seguro de editarlo?",
        showDenyButton: false,
        showCancelButton: true,
        confirmButtonText: "Si",
        cancelButtonText: `No`,
      });

      if (confir.value) {
        const inputSeccion = document.getElementById('seccion-'+id);
        const valueSeccion = inputSeccion.value;

        if (valueSeccion == '') {
            toastr.warning("Por favor llenar los campos.");
            return;
        }

        const response = await fetch(
          `${this.url}` + "administration/seccion/editar",
          this.data.optionsFetch({
            id,valueSeccion
          })
        );

        const { status, msg } = await response.json();

        switch (status) {
          case 200:
            this.data.secciones = await this.getSecciones();
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
            break;

          default:
            Swal.fire("Oopss!", msg, "warning");
            break;
        }
      }
    },

    async eliminarSeccion(id) {
      const confir = await Swal.fire({
        title: "Esta seguro de eliminarlo?",
        showDenyButton: false,
        showCancelButton: true,
        confirmButtonText: "Si",
        cancelButtonText: `No`,
      });

      if (confir.value) {
        const response = await fetch(
          `${this.url}` + "administration/seccion/eliminar",
          this.data.optionsFetch({
            id,
          })
        );

        const { status, msg } = await response.json();

        switch (status) {
          case 200:
            this.data.secciones = await this.getSecciones();
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
    OnSelectedPerfil(item) {
      this.data.form.profile.val = item.id;
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
    OnSelectedPuntoEvaluacion(item) {
      this.data.form.punto.val = item.id;
    },
    OnSelectedSeccion(item) {
      this.data.form.seccion.val = item.id;
    },
    async viewPoints(user_id) {
      this.data.user_points = [];
      //load(true)
      const response = await fetch(
        `${this.url}` + "administration/usuario_puntos/" + user_id
      );
      const data = await response.json();
      //load(false)
      this.data.user_points = data.data;
      this.data.label_points = data.label;
      console.log(data.data);
      $("#modalPointsUser").modal("show");
    },
    myChangeEvent(val) {
      this.data.form.punto.val = val;
    },
    myChangeEventEmpresa(val) {
        if (Array.isArray(val)) {
            this.data.form.lider_empresas.val = val;
        }
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
  },
  watch: {
    "data.form.pais": {
      async handler(val) {
        if (val.val != "" && val.val != null)
          await this.getDepartaments(val.val);
      },
      deep: true,
    },
    "data.form.departamento": {
      async handler(val) {
        if (val.val != "" && val.val != null) await this.getCities(val.val);
      },
      deep: true,
    },
    search: {
      async handler(val) {
        loading(true);
        await this.getDataAll();
        loading(false);
      },
      deep: true,
    },
  },
};
</script>

<style scoped>
    .badge-warning {
    background-color: rgba(254, 99, 78, 0.05) !important;
    color: #ff7f00 !important;
    font-size: 0.9rem !important;
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

    .formSinBorde{
        border: 0px solid;
    }
</style>
