<template>
  <div class="container-fluid">
    <div class="flex-wrap mb-2">
      <div
        class="d-flex flex-wrap mb-2 align-items-end justify-content-end row menu-cap"
      >
        <div class="container_bnts d-flex" id="container_bnts">
          <div class="d-flex col-12 mr-2">
            <div class="btn-menu">
              <button
                hidden
                class="btn btn-barra-naranja"
                style="width: max-content"
                ref="tour_btn_create_user"
                @click="OnClickOpenModal"
                v-if="
                  permisos.includes('accomp-crear_acompañamiento') ||
                  id_grupo == 30 ||
                  (main_account != 1 && savk_principal == 1)
                "
              >
                Crear acompañamiento
              </button>
            </div>
          </div>
        </div>
        <div class="div-busqueda">
          <div class="mr-2">
            <button
              class="btn btn-barra"
              ref="tour_btn_invite"
              style="width: max-content"
              @click="ClearFilters"
              v-show="visibleClearFilters"
            >
              Limpiar filtros
            </button>
          </div>
          <div class="input-group">
            <input
              type="text"
              class="form-control form-control-busqueda"
              placeholder="Buscar por centro de costos"
              @keyup.enter="OnKeyUpSearch()"
              v-model="data.filters.nombre_centro_costo"
            />
            <div class="input-group-append">
              <span class="input-group-text btn-barra-naranja">
                <a
                  href="javascript:void(0)"
                  @click="getDataAll()"
                  class="aBuscar"
                >
                  <i class="flaticon-381-search-2"></i>
                </a>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-xl-12">
        <div class="tab-content">
          <div class="table-responsive">
            <table class="table card-table display dataTablesCard">
              <thead>
                <tr class="">
                  <th @click="OnClickOpenModalFilters(1)">Fecha <i class="bi bi-funnel filter"></i></th>
                  <th @click="OnClickOpenModalFilters(2)">Modalidad <i class="bi bi-funnel filter"></i></th>
                  <th @click="OnClickOpenModalFilters(3)">Tipo <i class="bi bi-funnel filter"></i></th>
                  <th>Tiempo (Horas)</th>
                  <th @click="OnClickOpenModalFilters(5)">Realizado por: <i class="bi bi-funnel filter"></i></th>
                  <th @click="OnClickOpenModalFilters(6)">Centro de costos <i class="bi bi-funnel filter"></i></th>
                  <th class="d-flex justify-content-center">
                    <a
                      class="badge badge-success"
                      style="color: white"
                      href="javascript:void(0)"
                      @click="downloadExcel()"
                    >
                      Descargar
                    </a>
                  </th>
                </tr>
              </thead>
              <tbody>
                <template v-for="acom in data.data_table" :key="acom.id">
                  <tr>
                    <td>{{ acom.fecha }}</td>
                    <td>{{ acom.modalidad }}</td>
                    <td>{{ acom.tipo }}</td>
                    <td>{{ acom.tiempo }}</td>
                    <td>{{ acom.nombre_com }}</td>
                    <td>{{ acom.nombre }}</td>
                    <td>
                      <div class="d-flex">
                        <a
                          class="badge badge-primary mr-2"
                          style="color: white"
                          href="javascript:void(0)"
                          @click="downloadReport(acom.id)"
                          v-if="
                            permisos.includes('accomp-reportes') ||
                            id_grupo == 30 ||
                            (main_account != 1 && savk_principal == 1)
                          "
                        >
                          Reporte
                        </a>
                        <a
                          href="javascript:void(0)"
                          class="badge badge-primary mr-2"
                          style="color: white"
                          @click="OnClickOpenModalResultado(acom.id)"
                          v-if="
                            permisos.includes('accomp-resultado') ||
                            id_grupo == 30 ||
                            (main_account != 1 && savk_principal == 1)
                          "
                        >
                          Resultados
                        </a>
                        <!--<a
                          href="javascript:void(0)"
                          class="badge badge-primary"
                          style="color: white"
                          v-if="
                            permisos.includes('accomp-preguntas') ||
                            id_grupo == 30 ||
                            (main_account != 1 && savk_principal == 1)
                          "
                        >
                          Preguntas
                        </a> -->
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
                <a
                  class="page-link"
                  href="javascript:void(0)"
                  @click="previousPage"
                >
                  <i class="la la-angle-left"></i
                ></a>
              </li>

              <template
                v-for="(link, index) in data.paginate.links"
                :key="index"
              >
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
                <a
                  class="page-link"
                  href="javascript:void(0)"
                  @click="nextPage"
                >
                  <i class="la la-angle-right"></i
                ></a>
              </li>
            </ul>
          </nav>
        </div>
      </div>
    </div>
  </div>

  <!-- MODAL MAPA ACOMPAÑAMIENTO -->
  <div
    class="modal fade"
    id="modal_map_accompaniment"
    role="dialog"
    aria-hidden="true"
    style="z-index: 9999"
  >
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Ubicación</h5>
          <button type="button" class="close" @click="closeModalMap">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div style="width: auto; height: 400px" id="map"></div>
        </div>
        <div class="modal-footer">
          <button
            type="button"
            class="btn btn-danger light"
            @click="closeModalMap"
          >
            Cerrar
          </button>
        </div>
      </div>
    </div>
  </div>
  <!-- MODAL CREAR ACOMPAÑAMIENTO -->
  <div
    class="modal fade"
    id="modal_create_accompaniment"
    tabindex="-1"
    role="dialog"
    aria-hidden="true"
  >
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Crear acompañamiento</h5>
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
                Fecha acompañamiento: <span style="color: red">*</span></label
              >
              <div class="col-sm-12">
                <div class="form-group">
                  <input
                    type="date"
                    v-model="data.form.fecha.val"
                    class="form-control input-default"
                    id="name"
                  />
                </div>
              </div>
            </div>
            <!-- END INPUT -->
            <!-- INIT INPUT -->
            <div class="mb-3 row col-lg-6">
              <label for="name" class="col-form-label">
                Centro de costos: <span style="color: red">*</span></label
              >
              <div class="col-sm-12">
                <div class="form-group">
                  <Select_Savk
                    ref="selectCentroCosto"
                    v-model="data.form.centro_costo.val"
                    :options="data.centros"
                    :maxItem="20"
                    placeholder="Seleccione una opción"
                    @selected="OnSelectedCentroCosto"
                  />
                </div>
              </div>
            </div>
            <!-- END INPUT -->
            <!-- INIT INPUT -->
            <div class="mb-3 row col-lg-6">
              <label for="name" class="col-form-label">
                Modalidad: <span style="color: red">*</span></label
              >
              <div class="col-sm-12">
                <div class="form-group">
                  <Select_Savk
                    ref="selectModalidad"
                    v-model="data.form.modalidad.val"
                    :options="data.modalidad"
                    :maxItem="20"
                    placeholder="Seleccione una opción"
                    @selected="OnSelectedModalidad"
                  />
                </div>
              </div>
            </div>
            <!-- END INPUT -->
            <div class="mb-3 row col-lg-6">
              <label for="name" class="col-form-label">
                Observación general:</label
              >
              <div class="col-sm-12">
                <div class="form-group">
                  <textarea
                    v-model="data.form.observacion.val"
                    class="form-control input-default"
                    name=""
                    id=""
                    cols="30"
                    rows="5"
                  ></textarea>
                </div>
              </div>
            </div>
            <!-- END INPUT -->
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" @click="create">
            Crear
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
  <!-- END MODAL CREAR ACOMPAÑAMIENTO-->
  <!-- MODAL FILTROS -->
  <div
    class="modal fade"
    id="modal_filters"
    tabindex="-1"
    role="dialog"
    aria-hidden="true"
  >
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">{{ tituloFiltro }}</h5>
          <button type="button" class="close" @click="closeModalFilters">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div v-if="modeFiltro == 1" class="row">
            <!-- INIT INPUT -->
            <div class="mb-3 col-lg-12">
              <label for="name" class="col-form-label">
                Fecha Inicial: <span style="color: red">*</span></label
              >
              <div class="col-sm-12">
                <div class="form-group">
                  <input
                    type="date"
                    v-model="data.filters.fecha.inicial"
                    class="form-control input-default"
                    id="name"
                  />
                </div>
              </div>
            </div>
            <!-- INIT INPUT -->
            <div class="mb-3 col-lg-12">
              <label for="name" class="col-form-label">
                Fecha Final: <span style="color: red">*</span></label
              >
              <div class="col-sm-12">
                <div class="form-group">
                  <input
                    type="date"
                    v-model="data.filters.fecha.final"
                    class="form-control input-default"
                    id="name"
                  />
                </div>
              </div>
            </div>
          </div>
          <div v-if="modeFiltro == 2" class="row">
            <!-- INIT INPUT -->
            <div class="mb-3 col-lg-12">
              <label for="name" class="col-form-label">
                Modalidad: <span style="color: red">*</span></label
              >
              <div class="col-sm-12">
                <div class="form-group">
                  <Select_Savk
                    ref="selectModalidad"
                    v-model="data.filters.modalidad"
                    :options="data.modalidad"
                    :maxItem="20"
                    placeholder="Seleccione una opción"
                    @selected="OnSelectedFiltroModalidad"
                  />
                </div>
              </div>
            </div>
          </div>
          <div v-if="modeFiltro == 3" class="row">
            <!-- INIT INPUT -->
            <div class="mb-3 col-lg-12">
              <label for="name" class="col-form-label">
                Tipo: <span style="color: red">*</span></label
              >
              <div class="col-sm-12">
                <div class="form-group">
                  <Select_Savk
                    ref="selectModalidad"
                    v-model="data.filters.tipo"
                    :options="data.modalFilters.tipo"
                    :maxItem="20"
                    placeholder="Seleccione una opción"
                    @selected="OnSelectedFiltroTipo"
                  />
                </div>
              </div>
            </div>
          </div>
          <div v-if="modeFiltro == 5" class="row">
            <!-- INIT INPUT -->
            <div class="mb-3 col-lg-12">
              <label for="name" class="col-form-label">
                Realizado por: <span style="color: red">*</span></label
              >
              <div class="col-sm-12">
                <div class="form-group">
                  <Select_Savk
                    ref="selectModalidad"
                    v-model="data.filters.realizado"
                    :options="data.modalFilters.realizados"
                    :maxItem="20"
                    placeholder="Seleccione una opción"
                    @selected="OnSelectedFiltroRealizado"
                  />
                </div>
              </div>
            </div>
          </div>
          <div v-if="modeFiltro == 6" class="row">
            <!-- INIT INPUT -->
            <div class="mb-3 col-lg-12">
              <label for="name" class="col-form-label">
                Centro de costo: <span style="color: red">*</span></label
              >
              <div class="col-sm-12">
                <div class="form-group">
                  <Select_Savk
                    ref="selectModalidad"
                    v-model="data.filters.centro_costo"
                    :options="data.modalFilters.centros_costo"
                    :maxItem="20"
                    placeholder="Seleccione una opción"
                    @selected="OnSelectedFiltroCentroCosto"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" @click="filtrar">
            Filtrar
          </button>
          <button
            type="button"
            class="btn btn-danger light"
            @click="closeModalFilters"
          >
            Cerrar
          </button>
        </div>
      </div>
    </div>
  </div>
  <!-- END MODAL FILTROS-->

  <!-- MODAL RESULTADO ACOMPAÑAMIENTO -->
  <div
    class="modal fade"
    id="modal_result_accompaniment"
    tabindex="-1"
    role="dialog"
    style="overflow: scroll"
    aria-hidden="true"
  >
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Resultados acompañamiento</h5>
          <button type="button" class="close" @click="closeModalResult">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="table-responsive">
            <table class="table card-table display dataTablesCard">
              <thead>
                <tr class="">
                  <th>Tipo</th>
                  <th>Operación</th>
                  <th>Centro de costo</th>
                  <th>Área</th>
                  <th>Ubicación</th>
                  <th>Tiempo (min)</th>
                  <th>Resultado</th>
                  <th>Observación</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <template
                  v-for="result in data.data_table_result"
                  :key="result.id"
                >
                  <tr>
                    <td>{{ result.tipo }}</td>
                    <td>{{ result.operacion }}</td>
                    <td>{{ result.centro_costo }}</td>
                    <td>{{ result.nombreArea }}</td>
                    <td
                      @click="ubicarZonaAcompañamiento(result.lat, result.long)"
                    >
                      <a href="#">{{ result.ciudad }}</a>
                    </td>
                    <td>
                      {{ result.tiempo }}
                    </td>
                    <td>{{ result.resultado }}</td>
                    <td>
                      <div class="titulo-card">{{ result.observacion }}</div>
                    </td>
                    <td>
                      <div class="d-flex">
                        <a
                          class="badge badge-primary"
                          style="color: white"
                          target="_blank"
                          :href="
                            url + 'accompaniment/detalle-auditoria/' + result.idEncriptado
                          "
                        >
                          Detalle
                        </a>
                      </div>
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
            @click="closeModalResult"
          >
            Cerrar
          </button>
        </div>
      </div>
    </div>
  </div>
  <!-- END MODAL RESULTADO ACOMPAÑAMIENTO-->
</template>

<script>
import Select_Savk from "../../../../../resources/js/components/pages/otros/Select_Savk.vue";

export default {
  props: {
    id_grupo: String,
    savk_principal: String,
    main_account: String,
  },
  components: {
    Select_Savk,
  },
  created() {
    this.getDataAll();
    this.getLideres();
  },
  mounted() {},
  data() {
    return {
      permisos: JSON.parse(localStorage.getItem("permisos")),
      token: document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content"),
      url: document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("url"),
      tituloFiltro: "",
      modeFiltro: "",
      visibleClearFilters: false,
      data: {
        paginate: {
          cant: 10,
          total: 1,
          current_page: 1,
          links: [],
        },
        data_table: [],
        data_table_result: [],
        centros: [],
        modalidad: [
          { id: 1, name: "Presencial" },
          { id: 2, name: "Virtual" },
        ],
        errorValidation: false,
        form: {
          modalidad: {
            val: "",
            required: true,
          },
          observacion: {
            val: "",
            required: false,
          },
          fecha: {
            val: "",
            required: true,
          },
          centro_costo: {
            val: "",
            required: true,
          },
        },
        filters: {
          fecha: {
            inicial: null,
            final: null,
          },
          responsable: null,
          tipo: null,
          centro_costo: null,
          nombre_centro_costo: null,
          modalidad: null,
        },
        modalFilters: {
          centros_costo: [],
          realizados: [],
          tipo: [],
        },
      },
    };
  },
  methods: {
    //Obtengo todos los centros de costos para la tabla
    async getDataAll(url = null) {
      url = url ?? `${this.url}` + "accompaniment/get-all-accompaniments";

      //load(true);
      const response = await fetch(url, {
        method: "POST",
        headers: {
          "Content-type": "application/json; charset=UTF-8",
          "X-CSRF-Token": document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content"),
        },
        body: JSON.stringify({
          search: this.search,
          filters: this.data.filters,
          paginate: this.data.paginate,
        }),
      });
      const { status, data, usuarios, centros_costos, tipo } = await response.json();
      //load(false);

      if (status != 200) {
        toastr.error("Hubo un error al obtener la información.");
        return;
      }

      this.data.paginate.current_page = data.current_page;
      this.data.paginate.total = data.total;
      this.data.paginate.links = data.links;
      this.data.data_table = data.data;
      this.data.modalFilters.realizados = usuarios;
      this.data.modalFilters.centros_costo = centros_costos;
      this.data.modalFilters.tipo = tipo;
    },
    async getResultAll(id) {
      //load(true);
      const response = await fetch(`${this.url}` + "accompaniment/get-result", {
        method: "POST",
        headers: {
          "Content-type": "application/json; charset=UTF-8",
          "X-CSRF-Token": document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content"),
        },
        body: JSON.stringify({
          id: id,
        }),
      });
      const { status, data } = await response.json();
      //load(false);

      if (status != 200) {
        toastr.error("Hubo un error al obtener la información.");
        return;
      }
      this.data.data_table_result = data;
    },
    previousPage() {
      loading(true);
      if (this.data.paginate.current_page === 1) return;

      this.data.paginate.current_page--;
      this.getDataAll(
        `${this.url}` +
          "accompaniment/get-all-accompaniments?page=" +
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
          "accompaniment/get-all-accompaniments?page=" +
          this.data.paginate.current_page
      );
      loading(false);
    },

    async numPage(num) {
      loading(true);
      await this.getDataAll(
        `${this.url}` + "accompaniment/get-all-accompaniments?page=" + num
      );
      loading(false);
    },
    async OnClickOpenModalResultado(id) {
      await this.getResultAll(id);
      $("#modal_result_accompaniment").modal("show");
    },
    async OnClickOpenModal() {
      this.data.errorValidation = false;
      $("#modal_create_accompaniment").modal("show");
    },

    closeModalCreate(e) {
      this.resetDataForm();
      $("#modal_create_accompaniment").modal("hide");
    },
    closeModalResult(e) {
      $("#modal_result_accompaniment").modal("hide");
    },
    closeModalMap(e) {
      $("#modal_map_accompaniment").modal("hide");
    },
    async OnClickOpenModalFilters(title) {
      switch (title) {
        case 1:
          this.tituloFiltro = "Filtro fecha";
          this.modeFiltro = title;
          break;
        case 2:
          this.tituloFiltro = "Filtro modalidad";
          this.modeFiltro = title;
          break;
        case 3:
          this.tituloFiltro = "Filtro tipo";
          this.modeFiltro = title;
          break;
        case 5:
          this.tituloFiltro = "Filtro realizado por";
          this.modeFiltro = title;
          break;
        case 6:
          this.tituloFiltro = "Filtro centro de costo";
          this.modeFiltro = title;
          break;

        default:
          break;
      }

      $("#modal_filters").modal("show");
    },

    closeModalFilters(e) {
      $("#modal_filters").modal("hide");
    },
    async getLideres() {
      const response = await fetch(
        `${this.url}` + "accompaniment/centros-de-costo-usuario"
      );
      const data = await response.json();
      this.data.centros = data;
    },
    validateForm() {
      let next = true;

      Object.keys(this.data.form).forEach((el) => {
        if (this.data.form[el].val === "" && this.data.form[el].required) {
          next = false;
        }
      });
      return next;
    },
    OnKeyUpSearch() {
      this.getDataAll();
    },
    async create() {
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

      try {
        const response = await fetch(this.url + "accompaniment/crear", {
          method: "POST",
          headers: {
            "Content-type": "application/json; charset=UTF-8",
            "X-CSRF-Token": document
              .querySelector('meta[name="csrf-token"]')
              .getAttribute("content"),
          },
          body: JSON.stringify(this.data.form),
        });
        // load(false);
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
              confirmButtonColor: "#1f3352",
              cancelButtonColor: "#ff7f00",
              allowOutsideClick: false,
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
    OnSelectedModalidad(item) {
      this.data.form.modalidad.val = item.id;
    },
    OnSelectedFiltroModalidad(item) {
      this.data.filters.modalidad = String(item.id);
    },
    OnSelectedFiltroRealizado(item) {
      this.data.filters.responsable = item.id;
    },
    OnSelectedFiltroTipo(item) {
      this.data.filters.tipo = item.name;
    },
    OnSelectedFiltroCentroCosto(item) {
      this.data.filters.centro_costo = item.id;
    },
    OnSelectedCentroCosto(item) {
      this.data.form.centro_costo.val = item.id;
    },

    async filtrar() {
        loading();
        this.visibleClearFilters = true;
        await this.getDataAll();
        this.closeModalFilters();
        loading(false);
    },
    async ClearFilters() {
      this.data.filters = {
        fecha: {
          inicial: null,
          final: null,
        },
        responsable: null,
        centro_costo: null,
        nombre_centro_costo: null,
        modalidad: null,
        tipo: null
      };

      this.visibleClearFilters = false;
      loading()
      await this.getDataAll();
      loading(false)
    },

    resetDataForm() {
      this.$refs.selectModalidad.Clear();
      this.$refs.selectCentroCosto.Clear();

      this.data.errorValidation = false;

      Object.keys(this.data.form).forEach((prop) => {
        this.data.form[prop].val = "";
      });
    },

    async downloadReport(id) {
      loading();
      let rs = await fetch(this.url + "accompaniment/download-report/" + id, {
        method: "GET",
        headers: {
          "X-CSRF-TOKEN": this.token,
        },
      });
      loading(false);

      if (rs.status == 200) {
        const blob = await rs.blob();
        const url = URL.createObjectURL(blob);

        // Crear un enlace de descarga y hacer clic en él para iniciar la descarga
        const link = document.createElement("a");
        link.href = url;
        link.download = "Reporte.pdf";
        link.click();

        // Limpiar la URL del objeto
        URL.revokeObjectURL(url);
      } else {
        const rd = await rs.json();
        console.error("Error al descargar el PDF " + rd.msg);
        toastr.error("Error al descargar el PDF: " + rd.msg);
      }
    },

    async downloadExcel() {
      loading();
      let rs = await fetch(this.url + "accompaniment/download-excel", {
        method: "GET",
        headers: {
          "X-CSRF-TOKEN": this.token,
        },
      });
      loading(false);

      if (rs.status == 200) {
        const blob = await rs.blob();
        const url = URL.createObjectURL(blob);

        // Crear un enlace de descarga y hacer clic en él para iniciar la descarga
        const link = document.createElement("a");
        link.href = url;
        link.download = "Visitas.xlsx";
        link.click();

        // Limpiar la URL del objeto
        URL.revokeObjectURL(url);
      } else {
        const rd = await rs.json();
        console.error("Error al descargar el Excel " + rd.msg);
        toastr.error("Error al descargar el Excel: " + rd.msg);
      }
    },

    ubicarZonaAcompañamiento(lat, lng) {
      if (lat == null || lng == null) {
        toastr.warning("No hay registros de latitud y longitud");
        return;
      }

      const coords = { lat: parseFloat(lat), lng: parseFloat(lng) };

      const map = new google.maps.Map(document.getElementById("map"), {
        zoom: 50,
        center: coords,
      });

      const maker = new google.maps.Marker({
        position: coords,
        map,
      });

      $("#modal_map_accompaniment").modal("show");
    },
  },
};
</script>

<style scoped>

.filter{
    color: #ff7f00;
    cursor: pointer;
}
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
  width: 50%;
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
  box-shadow: none !important;
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
  margin-left: 8px;
}

.form-control-busqueda {
  height: 45px !important;
}

.titulo-card {
  height: 47px; /* Altura del div */
  display: -webkit-box;
  -webkit-line-clamp: 6; /* Número máximo de líneas a mostrar */
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
}

.titulo-card:hover {
  height: auto;
  display: flex;
  overflow: auto;
  min-height: 47px;
}
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
  width: 50%;
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
  box-shadow: none !important;
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
  margin-left: 8px;
}

.form-control-busqueda {
  height: 45px !important;
}
</style>
