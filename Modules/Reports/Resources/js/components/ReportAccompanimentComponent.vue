<template>
  <div class="container-fluid">
    <div class="flex-wrap mb-2">
      <div
        class="d-flex flex-wrap mb-2 align-items-end justify-content-end row menu-cap"
      >
        <div class="container_bnts d-flex" id="container_bnts">
          <div class="d-flex col-12 mr-2"></div>
        </div>
        <div class="div-busqueda d-flex justify-content-end">
          <a
            class="badge badge-success mr-2 mb-2 mt-2"
            style="color: white"
            href="javascript:void(0)"
            @click="downloadReport()"
          >
            Descargar Excel
          </a>
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
                  <th>Fecha</th>
                  <th>Tipo</th>
                  <th>Operación</th>
                  <th>Centro de costo</th>
                  <th>Ubicación</th>
                  <th>Tiempo (min)</th>
                  <th>Resultado</th>
                  <th>Observación</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="result in data.data_table" :key="result.id">
                  <td>{{ result.fecha }}</td>
                  <td>{{ result.tipo }}</td>
                  <td>{{ result.operacion }}</td>
                  <td>{{ result.centro_costo }}</td>
                  <td>
                    {{ result.ciudad }}
                  </td>

                  <td>
                    {{ result.tiempo }}
                  </td>
                  <td>{{ result.resultado }}</td>
                  <td>
                    <div class="titulo-card">{{ result.observacion }}</div>
                  </td>
                </tr>
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
</template>

<script>
export default {
  created() {
    this.getDataAll();
  },
  data() {
    return {
      permisos: JSON.parse(localStorage.getItem("permisos")),
      token: document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content"),
      url: document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("url"),
      data: {
        paginate: {
          cant: 10,
          total: 1,
          current_page: 1,
          links: [],
        },
        data_table: [],
      },
    };
  },
  methods: {
    async getDataAll(url = null) {
      url = url ?? `${this.url}` + "informes/get-report-accompanimient";

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
      const { status, data } = await response.json();
      //load(false);

      if (status != 200) {
        toastr.error("Hubo un error al obtener la información.");
        return;
      }

      this.data.paginate.current_page = data.current_page;
      this.data.paginate.total = data.total;
      this.data.paginate.links = data.links;
      this.data.data_table = data.data;
    },
    previousPage() {
      loading(true);
      if (this.data.paginate.current_page === 1) return;

      this.data.paginate.current_page--;
      this.getDataAll(
        `${this.url}` +
          "informes/get-report-accompanimient?page=" +
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
          "informes/get-report-accompanimient?page=" +
          this.data.paginate.current_page
      );
      loading(false);
    },

    async numPage(num) {
      loading(true);
      await this.getDataAll(
        `${this.url}` + "informes/get-report-accompanimient?page=" + num
      );
      loading(false);
    },

    async downloadReport() {
      loading();
      let rs = await fetch(this.url + "informes/descargar-excel-acompañamiento", {
        method: "POST",
        body: JSON.stringify({
          search: this.search,
          filters: this.data.filters,
          paginate: this.data.paginate,
        }),
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
        link.download = "Informe acompañamiento.xlsx";
        link.click();

        // Limpiar la URL del objeto
        URL.revokeObjectURL(url);
      } else {
        const rd = await rs.json();
        console.error("Error al descargar el Excel: " + rd.msg);
        toastr.error("Error al descargar el Excel: " + rd.msg);
      }
    },
  },
};
</script>

<style>
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
</style>