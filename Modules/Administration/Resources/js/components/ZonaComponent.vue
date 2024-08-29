<template>
    <div class="table-responsive mt-4">
      <table
        id="tableZonas"
        class="table card-table display dataTablesCard"
      >
        <thead>
          <tr class="">
            <th>Nombre</th>
            <th>Asignaciones</th>
            <th v-if="permisos.includes('org-mio-editar_zona') || permisos.includes('org-mio-eliminar_zona')"></th>
          </tr>
        </thead>
        <tbody>
          <template v-for="(emp, index) in data.data_table" :key="index">
            <tr>
              <td>{{ emp.nombre }}</td>
                <td>
                    <a
                    href="#!"
                    class="badge badge-warning"
                    @click="viewPoints(index)"
                    >
                        {{ emp.centros.length }}
                    </a
                >
                </td>
              <td v-if="permisos.includes('org-mio-editar_zona') || permisos.includes('org-mio-eliminar_zona')">
                <div>
                  <a
                    class="badge badge-primary"
                    style="color: white"
                    href="javascript:void(0)"
                    @click="edit(index)"
                    v-if="permisos.includes('org-mio-editar_zona')"
                  >
                    Editar
                  </a>
                  <a href="javascript:void(0)"
                   class="badge badge-primary"
                    style="color: white"
                   @click="deleteElem(emp.id)"
                   v-if="permisos.includes('org-mio-eliminar_zona')">
                    Eliminar
                  </a>
                </div>
              </td>
            </tr>
          </template>
          <tr v-if="data.data_table_sin_asignar.length > 0">
            <td>
                Sin asignar
            </td>
            <td>
                <a
                href="#!"
                class="badge badge-warning"
                @click="viewPoints(null,'sinAsignar')"
                >
                    {{ data.data_table_sin_asignar.length }}
                </a>
            </td>
            <td></td>
          </tr>
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
      id="modal_create_zona"
      tabindex="-1"
      role="dialog"
      aria-hidden="true"
    >
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              {{
                data.mode === "create"
                  ? "Crear zona"
                  : "Editar zona"
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
              Los campos con (*) son obligatorios, asegúrese de diligenciarlos.
            </div>

            <div class="row m-auto" >
              <!-- INIT INPUT -->
              <div class="mb-3 row col-lg-12">
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
                <div class="mb-3 row col-lg-12">
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

    <div class="modal fade" id="modalPointsUser">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title">Puntos de Evaluación asignados</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
                </div>
                <div class="modal-body">
                    <div class="row mt-1">
                        <div class="col-lg-6">
                        <ul >
                            <template v-for="(point, index) in data.puntosAsignados" :key="point.id">
                            <li class="ml-4" style="list-style: outside;" v-if="index % 2 === 0">{{ point.nombre }}</li>
                            </template>
                        </ul>
                        </div>
                        <div class="col-lg-6">
                        <ul >
                            <template v-for="(point, index) in data.puntosAsignados" :key="point.id">
                            <li class="ml-4" style="list-style: outside;" v-if="index % 2 !== 0">{{ point.nombre }}</li>
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
  </template>

  <script>
  import Select_Savk from "../../../../../resources/js/components/pages/otros/Select_Savk.vue";
  export default {
    components: {
      Select_Savk,
    },
    created() {
      this.getDataAll();
      this.getLideres();
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
          mode: "create",
          errorValidation: false,
          lideres: [],
          puntosAsignados: [],
          form: {
            id: { required: false, val: "" },
            nombre: { required: true, val: "" },
            lideres: {required: false, val: []}
          },
          modal_create: null,
          paginate: {
            cant: 10,
            total: 1,
            current_page: 1,
            links: [],
          },

          data_table: [],
          data_table_sin_asignar: [],

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
        async getLideres() {
            const response = await fetch(`${this.url}` + "administration/lideres-zonas");
            const data = await response.json();
            this.data.lideres = data;
        },

        myChangeEvent(val){
            if (Array.isArray(val)) {
                this.data.form.lideres.val = val
            }
        },
      //Obtengo todos los centros de costos para la tabla
      async getDataAll(url = null) {
        url = url ?? `${this.url}` + "administration/zona/all";

        const response = await fetch(
          url,
          this.data.optionsFetch({
            filters: this.data.filters,
            search: this.search,
            paginate: this.data.paginate,
          })
        );
        const { status, data, sinAsignar } = await response.json();

        if (status != 200) {
          toastr.error("Hubo un error al obtener la información.");
          return;
        }

        this.data.paginate.current_page = data.current_page;
        this.data.paginate.total = data.total;
        this.data.paginate.links = data.links;
        this.data.data_table = data.data;
        this.data.data_table_sin_asignar = sinAsignar;
      },
      openModalCreate() {
        $("#modal_create_zona").modal("show");
      },
      closeModalCreate(e) {
        this.data.errorValidation = false;
        this.data.mode = "create";
        $("#modal_create_zona").modal("hide");
        this.resetDataForm();
      },

      //Limpio los campos
      resetDataForm() {
        Object.keys(this.data.form).forEach((prop) => {
          if (Array.isArray(this.data.form[prop].val) ) {
              this.data.form[prop].val = [];
          }else{
              this.data.form[prop].val = "";
          }
        });
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
        //   this.data.errorValidation = true;
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
          const response = await fetch(
            `${this.url}` + "administration/zona/crear",
            this.data.optionsFetch({
              ...this.data.form,
              mode: this.data.mode,
            })
          );
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
            "/administration/zona/all?page=" +
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
            "/administration/zona/all?page=" +
            this.data.paginate.current_page
        );
        loading(false);
      },

      async numPage(num) {
        loading(true);
        await this.getDataAll(
          `${this.url}` + "administration/zona/all?page=" + num
        );
        loading(false);
      },

      changeCantPaginate() {
        this.getDataAll();
      },

      //Editar el zona
      async edit(index) {
        await this.openModalCreate();
        let data_edit = this.data.data_table[index];
        this.data.mode = "edit";

        this.data.form.id.val = data_edit.id;
        this.data.form.nombre.val = data_edit.nombre;
        this.data.form.lideres.val = data_edit.lideres;


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
            `${this.url}` + "administration/zona/eliminar",
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
        async viewPoints(index,sinAsignar = null ) {
            if (sinAsignar == null) {
                this.data.puntosAsignados = this.data.data_table[index].centros;
            }else{
                this.data.puntosAsignados = this.data.data_table_sin_asignar;
            }
            $("#modalPointsUser").modal("show");
        },
    },
    watch: {
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
    .badge-warning {
        background-color: rgba(254, 99, 78, 0.05) !important;
        color: #ff7f00 !important;
        font-size: 0.9rem !important;
    }
  </style>
