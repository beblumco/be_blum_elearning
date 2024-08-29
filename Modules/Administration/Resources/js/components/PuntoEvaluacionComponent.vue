<template>
  <!-- <div class="header-mod">
    <button style="background-color: #002f54; font-color: white; border-color: #002f54" class="btn btn-primary" @click="openModalCreate">
      Crear punto
    </button>
  </div> -->

  <div class="row justify-content-end col-lg-12 m-3">
   <!--  <div class="d-flex col-lg-1">
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
          <th>Ciudad</th>
          <th>Empresa</th>
          <th>Estado</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <template v-for="(emp, index) in data.data_table" :key="emp.id">
          <tr>
            <td>{{ emp.nombre }}</td>
            <td>{{ emp.ciudad }}</td>

            <td>{{ emp.empresa }}</td>
            <td>{{ emp.estado }}</td>

            <td>
              <div>
                <a
                  class="badge badge-primary"
                  style="color: white"
                  href="javascript:void(0)"
                  @click="edit(index)"
                  v-if="permisos.includes('org-mio-editar_centro_costo')"
                >
                  Editar
                </a>
                <a href="javascript:void(0)"
                 class="badge badge-primary"
                  style="color: white"
                 @click="deleteElem(emp.id)"
                 v-if="permisos.includes('org-mio-eliminar_centro_costo')"
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

  <!-- MODAL CREAR PUNTO EVALUACIÓN -->
  <div
    class="modal fade"
    id="modal_create_point"
    tabindex="-1"
    role="dialog"
    aria-hidden="true"
  >
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modal-title-create-company">
            {{ data.mode === "create" ? "Crear Punto" : "Editar Punto" }}
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

          <div class="row m-auto" >
            <!-- INIT INPUT -->
            <div class="mb-3 row col-lg-6">
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

            <div class="mb-3 row col-lg-6">
                <label for="name" class="col-form-label">
                    Dirección: <span style="color: red">*</span></label>
                <div class="col-sm-12">
                    <div class="form-group">
                        <input type="text" class="form-control input-default "
                            v-model="data.form.dir.val">
                    </div>
                </div>
            </div>

            <!-- <div class="mb-3 row col-lg-6">
                                                <label for="name" class="col-form-label">
                                                    Telefono: <span style="color: red;">*</span></label>
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control input-default "
                                                            v-model="data.form.tel.val">
                                                    </div>
                                                </div>
                                            </div> -->

            <div class="mb-3 row col-lg-6">
              <label for="name" class="col-form-label">
                Email:</label
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
                Empresa: <span style="color: red">*</span></label
              >
              <div class="col-sm-12">
                <Select_Savk
                  ref="selectEmpresa"
                  v-model="data.form.empresa.val"
                  :options="data.companies"
                  :maxItem="20"
                  placeholder="Seleccione una opción"
                  @selected="OnSelectedEmpresa"
                />
              </div>
            </div>

            <div class="mb-3 row col-lg-6" v-show="data.main_account_id != 2">
              <label class="col-form-label"> Zona:</label>
              <div class="col-sm-12">
                <Select_Savk
                  ref="selectZona"
                  v-model="data.form.zona.val"
                  :options="data.zonas"
                  :maxItem="20"
                  placeholder="Seleccione una zona"
                  @selected="OnSelectedZona"
                />
              </div>
            </div>

            <div class="mb-3 row col-lg-6">
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

            <div class="mb-3 row col-lg-6">
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

            <div class="mb-3 row col-lg-6">
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
             <div class="mb-3 row col-lg-6">
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
  <!-- END MODAL PUNTO EVALUACIÓN -->
</template>

<script>
import Select_Savk from "../../../../../resources/js/components/pages/otros/Select_Savk.vue";
import { isValidEmail } from "../../../../../public/assets/js/functions.js";
export default {
  components: {
    Select_Savk,
  },
  created() {
    this.getCities();
    this.getCountries();
    this.getZonas();
    this.getDepartaments();
    this.getLideres();
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
        main_account_id: null,
        estados: [
          { id: 1, name: "Activo" },
          { id: 2, name: "Inactivo" },
        ],
        countries: [],
        zonas: [],
        cities: [],
        companies: [],
        departaments: [],
        mode: "create",
        errorValidation: false,
        form: {
          id: { required: false, val: "" },
          nombre: { required: true, val: "" },
          dir: { required: true, val: "" },
          tel: { required: false, val: "" },
          email: { required: false, val: "" },
          estado: { required: true, val: "" },
          pais: { required: true, val: "" },
          departamento: { required: true, val: "" },
          ciudad: { required: true, val: "" },
          empresa: { required: true, val: "" },
          lideres: { required: false, val: [] },
          zona: { required: false, val: "" },
        },
        modal_create: null,
        paginate: {
          cant: 10,
          total: 1,
          current_page: 1,
          links: [],
        },
        data_table: [],
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
    async getZonas() {
      //load(true);
      const response = await fetch(`${this.url}` + "administration/zonas");
      const data = await response.json();
      this.data.zonas = data;
      if (this.data.zonas.length > 0) {
          this.data.zonas.unshift({id: 'null', name: '-'});
      }
      //load(false);
    },
    async getCountries() {
      //load(true);
      const response = await fetch(`${this.url}` + "administration/paises");
      const data = await response.json();
      this.data.countries = data;
      //load(false);
    },
    async getLideres() {
      //load(true);
      const response = await fetch(`${this.url}` + "administration/lideres-centro-de-costos");
      const data = await response.json();
      this.data.lideres = data;
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
    async getCompanies() {
      //load(true);
      const response = await fetch(`${this.url}` + "administration/empresas");
      const data = await response.json();
      //load(false);
      return data;
    },

    //Obtengo todos los centros de costos para la tabla
    async getDataAll(url = null) {
      url = url ?? `${this.url}` + "administration/punto/all";

      //load(true);
      const response = await fetch(
        url,
        this.data.optionsFetch({
          search: this.search,
          filters: this.data.filters,
          paginate: this.data.paginate,
        })
      );
      const { status, data, main_account_id } = await response.json();
      //load(false);

      if (status != 200) {
        toastr.error("Hubo un error al obtener la información.");
        return;
      }

      this.data.main_account_id = main_account_id
      this.data.paginate.current_page = data.current_page;
      this.data.paginate.total = data.total;
      this.data.paginate.links = data.links;
      this.data.data_table = data.data;
    },

    async openModalCreate() {
      this.data.companies = await this.getCompanies(this.data);
      $("#modal_create_point").modal("show");
    },

    closeModalCreate(e) {
      this.data.errorValidation = false;
      this.data.mode = "create";
      $("#modal_create_point").modal("hide");
      this.resetDataForm();
    },

    //Limpio los campos
    resetDataForm() {
      this.$refs.selectEstado.Clear();
      this.$refs.selectEmpresa.Clear();
      this.$refs.selectPais.Clear();
      this.$refs.selectDepartamento.Clear();
      this.$refs.selectCiudad.Clear();
      this.$refs.selectZona.Clear();
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
      if(this.data.form.email.val != '' && !this.isValidEmail(this.data.form.email.val)){
        return;
      }
      try {
        //load(true);
        const response = await fetch(
          `${this.url}` + "administration/punto/crear",
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
          "/administration/punto/all?page=" +
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
          "/administration/punto/all?page=" +
          this.data.paginate.current_page
      );
      loading(false);
    },

    async numPage(num) {
      loading(true);
      await this.getDataAll(
        `${this.url}` + "administration/punto/all?page=" + num
      );
      loading(false);
    },

    changeCantPaginate() {
      this.getDataAll();
    },

    //Editar el Grupo Empresa
    async edit(index) {
        await this.openModalCreate();
        let data_edit = this.data.data_table[index];
        this.data.mode = "edit";

        this.data.form.id.val = data_edit.id;

        this.data.form.nombre.val = data_edit.nombre;
        this.data.form.ciudad.val = data_edit.ciudad;
        this.data.form.estado.val = data_edit.estado_id;
        this.data.form.empresa.val = data_edit.unidad_id ?? "";
        this.data.form.pais.val = data_edit.pais_id;
        this.data.form.departamento.val = data_edit.departamentos_id;
        this.data.form.ciudad.val = data_edit.ciudad_id;
        this.data.form.email.val = data_edit.email ?? "";
        this.data.form.dir.val = data_edit.direccion;
        this.data.form.tel.val = data_edit.telefono;
        this.data.form.lideres.val = data_edit.lideres;
        this.data.form.zona.val = data_edit.id_zona;

        let option = this.data.estados.find(
            (item) => item.id == this.data.form.estado.val
        );
        if (option) this.$refs.selectEstado.selectOption(option);

        let optionZona = this.data.zonas.find(
            (item) => item.id == this.data.form.zona.val
        );
        if (optionZona) this.$refs.selectZona.selectOption(optionZona);

        if (data_edit.pais_id != null) {
            option = await this.data.countries.find(
                (item) => item.id == this.data.form.pais.val
            );
            if (option) this.$refs.selectPais.selectOption(option);

            await this.getDepartaments(option.id);
            option = this.data.departaments.find(
                (item) => item.id == this.data.form.departamento.val
            );
            if (option) this.$refs.selectDepartamento.selectOption(option);

            option = this.data.cities.find(
                (item) => item.id == this.data.form.ciudad.val
            );
            if (option) this.$refs.selectCiudad.selectOption(option);
        }

        option = this.data.companies.find(
            (item) => item.id == this.data.form.empresa.val
        );
        if (option) this.$refs.selectEmpresa.selectOption(option);
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
          `${this.url}` + "administration/punto/eliminar",
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
    OnSelectedEmpresa(item) {
      this.data.form.empresa.val = item.id;
    },
    OnSelectedZona(item) {
      this.data.form.zona.val = item.id;
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

<style>
</style>
